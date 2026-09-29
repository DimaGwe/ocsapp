<?php

namespace App\Helpers;

require_once __DIR__ . '/PaymentGatewayHelper.php';
require_once __DIR__ . '/EmailHelper.php';

/**
 * HomeProfileHelper - teen accounts under a parent or guardian ("Home Profile").
 *
 * Standalone accounts are 18+ (Terms s5). A teen aged 13 to 17 can only use OCSAPP as a
 * member of a guardian's Home Profile:
 * - Parent setup: the guardian invites the teen from /account/home; the teen joins through a
 *   signed, expiring link (only a SHA-256 hash of the token is stored).
 * - Supervision: the guardian is emailed on every member order event and can open the order.
 * - Restricted items: products flagged age_restricted are hidden and blocked for members.
 * - Billing: member orders are charged off-session to the guardian's saved Stripe card.
 */
class HomeProfileHelper
{
    public const MIN_AGE = 13;
    public const MAX_AGE = 17;
    public const INVITE_DAYS = 7;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    // ---------------------------------------------------------------- membership

    public static function isMember(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }
        static $cache = [];
        if (!array_key_exists($userId, $cache)) {
            $stmt = self::db()->prepare("SELECT account_type FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $cache[$userId] = $stmt->fetchColumn() === 'home_member';
        }
        return $cache[$userId];
    }

    /** Is the logged-in user a Home Profile member? */
    public static function currentIsMember(): bool
    {
        return function_exists('isLoggedIn') && isLoggedIn() && self::isMember((int) userId());
    }

    /** Active link for a member, with the guardian's contact and card fields. */
    public static function guardianFor(int $memberUserId): ?array
    {
        $stmt = self::db()->prepare("
            SELECT hpm.id AS link_id, hpm.guardian_user_id, hpm.first_name AS member_first_name,
                   g.email AS guardian_email, g.first_name AS guardian_first_name, g.last_name AS guardian_last_name,
                   g.stripe_customer_id, g.stripe_payment_method_id, g.card_brand, g.card_last4, g.status AS guardian_status
            FROM home_profile_members hpm
            JOIN users g ON g.id = hpm.guardian_user_id
            WHERE hpm.member_user_id = ? AND hpm.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$memberUserId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /** All non-removed members and pending invites of a guardian. */
    public static function membersOf(int $guardianUserId): array
    {
        $stmt = self::db()->prepare("
            SELECT hpm.*, u.email AS member_email, u.status AS member_status
            FROM home_profile_members hpm
            LEFT JOIN users u ON u.id = hpm.member_user_id
            WHERE hpm.guardian_user_id = ? AND hpm.status <> 'removed'
            ORDER BY hpm.created_at DESC
        ");
        $stmt->execute([$guardianUserId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function ageOn(string $birthDate, ?string $onDate = null): ?int
    {
        $birth = \DateTime::createFromFormat('!Y-m-d', $birthDate);
        if (!$birth || $birth->format('Y-m-d') !== $birthDate) {
            return null;
        }
        $on = new \DateTime($onDate ?? 'today');
        if ($birth > $on) {
            return null;
        }
        return (int) $birth->diff($on)->y;
    }

    /** A guardian must be an active, standard (18+) buyer account. */
    public static function canBeGuardian(int $userId): bool
    {
        $stmt = self::db()->prepare("SELECT account_type, status FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row && $row['account_type'] === 'standard' && $row['status'] === 'active';
    }

    // ---------------------------------------------------------------- invites

    /**
     * Creates an invite and emails it.
     * @return array{success: bool, error: ?string}
     */
    public static function invite(int $guardianUserId, string $firstName, string $email, string $birthDate, bool $fr): array
    {
        $firstName = trim($firstName);
        $email = strtolower(trim($email));
        if ($firstName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => $fr ? 'Veuillez entrer un prénom et un courriel valides.' : 'Please enter a valid first name and email.'];
        }
        if (!self::canBeGuardian($guardianUserId)) {
            return ['success' => false, 'error' => $fr ? 'Votre compte ne peut pas gérer un Profil Maison.' : 'Your account cannot manage a Home Profile.'];
        }
        $age = self::ageOn($birthDate);
        if ($age === null || $age < self::MIN_AGE || $age > self::MAX_AGE) {
            return ['success' => false, 'error' => $fr
                ? 'Un membre du Profil Maison doit avoir entre 13 et 17 ans. À 18 ans, il peut créer son propre compte.'
                : 'A Home Profile member must be 13 to 17 years old. At 18, they can create their own account.'];
        }

        $db = self::db();
        $exists = $db->prepare("SELECT 1 FROM users WHERE email = ?");
        $exists->execute([$email]);
        if ($exists->fetch()) {
            return ['success' => false, 'error' => $fr ? 'Ce courriel est déjà associé à un compte OCSAPP.' : 'This email is already linked to an OCSAPP account.'];
        }
        $pending = $db->prepare("SELECT 1 FROM home_profile_members WHERE invite_email = ? AND status = 'invited' AND invite_expires_at > NOW()");
        $pending->execute([$email]);
        if ($pending->fetch()) {
            return ['success' => false, 'error' => $fr ? 'Une invitation est déjà en attente pour ce courriel.' : 'An invite is already pending for this email.'];
        }

        $token = bin2hex(random_bytes(32));
        $db->prepare("
            INSERT INTO home_profile_members (guardian_user_id, invite_email, first_name, birth_date, invite_token_hash, invite_expires_at, status)
            VALUES (?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL " . self::INVITE_DAYS . " DAY), 'invited')
        ")->execute([$guardianUserId, $email, $firstName, $birthDate, hash('sha256', $token)]);

        $g = $db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
        $g->execute([$guardianUserId]);
        $guardian = $g->fetch(\PDO::FETCH_ASSOC) ?: ['first_name' => '', 'last_name' => ''];
        $guardianName = trim($guardian['first_name'] . ' ' . $guardian['last_name']);

        $base = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
        $link = $base . '/home-profile/join?token=' . $token;
        $n = htmlspecialchars($firstName);
        $gn = htmlspecialchars($guardianName);
        $l = htmlspecialchars($link);
        $body = self::emailShell(
            "<p>Bonjour $n,</p>
             <p>$gn vous invite à rejoindre son <strong>Profil Maison</strong> sur OCSAPP. Vous pourrez magasiner auprès des commerces locaux; vos commandes sont payées par votre parent ou tuteur, qui en est avisé.</p>
             <p><a href=\"$l&lang=fr\" style=\"background:#00b207;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;display:inline-block;\">Rejoindre le Profil Maison</a></p>
             <p style=\"color:#777;font-size:13px;\">Cette invitation expire dans " . self::INVITE_DAYS . " jours. Si vous ne vous attendiez pas à ce courriel, ignorez-le.</p>",
            "<p>Hi $n,</p>
             <p>$gn is inviting you to join their <strong>Home Profile</strong> on OCSAPP. You can shop from local businesses; your orders are paid by your parent or guardian, who is notified of them.</p>
             <p><a href=\"$l&lang=en\" style=\"background:#00b207;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;display:inline-block;\">Join the Home Profile</a></p>
             <p style=\"color:#777;font-size:13px;\">This invite expires in " . self::INVITE_DAYS . " days. If you weren't expecting this email, you can ignore it.</p>"
        );
        EmailHelper::sendRaw($email, 'Invitation au Profil Maison OCSAPP / OCSAPP Home Profile invite', $body);

        return ['success' => true, 'error' => null];
    }

    /** Valid, unexpired invite for a raw token, or null. */
    public static function findInvite(string $token): ?array
    {
        $token = preg_replace('/[^a-f0-9]/', '', strtolower($token));
        if (strlen($token) !== 64) {
            return null;
        }
        $stmt = self::db()->prepare("
            SELECT hpm.*, g.first_name AS guardian_first_name, g.last_name AS guardian_last_name
            FROM home_profile_members hpm JOIN users g ON g.id = hpm.guardian_user_id
            WHERE hpm.invite_token_hash = ? AND hpm.status = 'invited' AND hpm.invite_expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([hash('sha256', $token)]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Creates the member account from an invite. The invite email proves the address,
     * so the account starts active and verified.
     * @return array{success: bool, error: ?string, user_id: ?int}
     */
    public static function acceptInvite(string $token, string $lastName, string $password, bool $fr): array
    {
        $invite = self::findInvite($token);
        if (!$invite) {
            return ['success' => false, 'error' => $fr ? 'Cette invitation est invalide ou expirée.' : 'This invite is invalid or has expired.', 'user_id' => null];
        }
        $age = self::ageOn($invite['birth_date']);
        if ($age === null || $age < self::MIN_AGE || $age > self::MAX_AGE) {
            return ['success' => false, 'error' => $fr ? "Cette invitation ne correspond plus à l'âge requis (13 à 17 ans)." : 'This invite no longer matches the required age (13 to 17).', 'user_id' => null];
        }

        $db = self::db();
        $db->beginTransaction();
        try {
            $exists = $db->prepare("SELECT 1 FROM users WHERE email = ?");
            $exists->execute([$invite['invite_email']]);
            if ($exists->fetch()) {
                $db->rollBack();
                return ['success' => false, 'error' => $fr ? 'Ce courriel est déjà associé à un compte OCSAPP.' : 'This email is already linked to an OCSAPP account.', 'user_id' => null];
            }

            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
            if ($ip && strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            $db->prepare("
                INSERT INTO users (email, password, first_name, last_name, terms_accepted_at, terms_accepted_ip,
                                   status, role, account_type, birth_date, email_verified_at)
                VALUES (?, ?, ?, ?, NOW(), ?, 'active', 'buyer', 'home_member', ?, NOW())
            ")->execute([
                $invite['invite_email'], password_hash($password, PASSWORD_DEFAULT),
                $invite['first_name'], $lastName, $ip, $invite['birth_date'],
            ]);
            $userId = (int) $db->lastInsertId();

            $role = $db->query("SELECT id FROM roles WHERE name = 'buyer' LIMIT 1")->fetchColumn();
            if (!$role) {
                throw new \RuntimeException("Role 'buyer' not found");
            }
            $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$userId, $role]);

            $db->prepare("
                UPDATE home_profile_members
                SET member_user_id = ?, status = 'active', accepted_at = NOW(), invite_token_hash = NULL
                WHERE id = ? AND status = 'invited'
            ")->execute([$userId, $invite['id']]);

            $db->commit();
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            logger('Home Profile accept failed: ' . $e->getMessage(), 'error');
            return ['success' => false, 'error' => $fr ? 'Une erreur est survenue. Veuillez réessayer.' : 'Something went wrong. Please try again.', 'user_id' => null];
        }

        self::emailGuardian((int) $invite['guardian_user_id'],
            'Nouveau membre du Profil Maison / New Home Profile member',
            '<p>' . htmlspecialchars($invite['first_name']) . ' a rejoint votre Profil Maison OCSAPP. Vous recevrez un avis à chaque commande.</p>',
            '<p>' . htmlspecialchars($invite['first_name']) . ' joined your OCSAPP Home Profile. You will be notified of every order.</p>');

        return ['success' => true, 'error' => null, 'user_id' => $userId];
    }

    /** Guardian cancels an invite or removes a member (member account is suspended). */
    public static function remove(int $guardianUserId, int $linkId): bool
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT member_user_id FROM home_profile_members WHERE id = ? AND guardian_user_id = ? AND status <> 'removed'");
        $stmt->execute([$linkId, $guardianUserId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        $db->prepare("UPDATE home_profile_members SET status = 'removed', removed_at = NOW(), invite_token_hash = NULL WHERE id = ?")
           ->execute([$linkId]);
        if (!empty($row['member_user_id'])) {
            $db->prepare("UPDATE users SET status = 'suspended' WHERE id = ? AND account_type = 'home_member'")
               ->execute([$row['member_user_id']]);
        }
        return true;
    }

    // ---------------------------------------------------------------- restricted items

    /** Of the given product IDs, which are age-restricted (18+). */
    public static function restrictedAmong(array $productIds): array
    {
        $productIds = array_values(array_unique(array_filter(array_map('intval', $productIds))));
        if (empty($productIds)) {
            return [];
        }
        $in = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = self::db()->prepare("SELECT id FROM products WHERE age_restricted = 1 AND id IN ($in)");
        $stmt->execute($productIds);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** SQL fragment for listing queries: hides 18+ products from members. */
    public static function listingFilterSql(string $productAlias = 'p'): string
    {
        return self::currentIsMember() ? " AND $productAlias.age_restricted = 0 " : '';
    }

    public static function restrictedMessage(bool $fr): string
    {
        return $fr
            ? "Ce produit est réservé aux 18 ans et plus et n'est pas offert aux comptes du Profil Maison."
            : 'This product is for ages 18+ and is not available on Home Profile accounts.';
    }

    // ---------------------------------------------------------------- guardian card (Stripe)

    private static function initStripe(): bool
    {
        $config = getStripeConfig();
        if (empty($config['secret_key'])) {
            return false;
        }
        \Stripe\Stripe::setApiKey($config['secret_key']);
        return true;
    }

    public static function ensureCustomer(int $userId): string
    {
        if (!self::initStripe()) {
            throw new \RuntimeException('Card payments are not configured.');
        }
        $db = self::db();
        $stmt = $db->prepare("SELECT email, first_name, last_name, stripe_customer_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            throw new \RuntimeException('Account not found.');
        }
        if (!empty($row['stripe_customer_id'])) {
            return $row['stripe_customer_id'];
        }
        $customer = \Stripe\Customer::create([
            'email' => $row['email'],
            'name' => trim($row['first_name'] . ' ' . $row['last_name']),
            'metadata' => ['user_id' => $userId, 'purpose' => 'home_profile'],
        ]);
        $db->prepare("UPDATE users SET stripe_customer_id = ? WHERE id = ?")->execute([$customer->id, $userId]);
        return $customer->id;
    }

    public static function createSetupIntentClientSecret(int $userId): string
    {
        $customerId = self::ensureCustomer($userId);
        $setupIntent = \Stripe\SetupIntent::create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'usage' => 'off_session',
            'metadata' => ['user_id' => $userId, 'purpose' => 'home_profile'],
        ]);
        return $setupIntent->client_secret;
    }

    public static function saveCardFromSetupIntent(int $userId, string $setupIntentId): void
    {
        if (!self::initStripe()) {
            throw new \RuntimeException('Card payments are not configured.');
        }
        $setupIntent = \Stripe\SetupIntent::retrieve($setupIntentId);
        $customerId = self::ensureCustomer($userId);
        if ($setupIntent->status !== 'succeeded' || empty($setupIntent->payment_method) || $setupIntent->customer !== $customerId) {
            throw new \RuntimeException('Card setup did not complete successfully.');
        }
        $pm = \Stripe\PaymentMethod::retrieve($setupIntent->payment_method);
        self::db()->prepare("UPDATE users SET stripe_payment_method_id = ?, card_brand = ?, card_last4 = ? WHERE id = ?")
            ->execute([$pm->id, $pm->card->brand ?? null, $pm->card->last4 ?? null, $userId]);
    }

    /**
     * Charges a member's orders to their guardian's saved card.
     * @return array{success: bool, payment_intent_id: ?string, error: ?string}
     */
    public static function chargeGuardian(int $memberUserId, float $amount, string $description, array $orderIds): array
    {
        $g = self::guardianFor($memberUserId);
        if (!$g || empty($g['stripe_customer_id']) || empty($g['stripe_payment_method_id'])) {
            return ['success' => false, 'payment_intent_id' => null, 'error' => 'No guardian card on file.'];
        }
        if (!self::initStripe()) {
            return ['success' => false, 'payment_intent_id' => null, 'error' => 'Card payments are not configured.'];
        }
        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => (int) round($amount * 100),
                'currency' => 'cad',
                'customer' => $g['stripe_customer_id'],
                'payment_method' => $g['stripe_payment_method_id'],
                'off_session' => true,
                'confirm' => true,
                'description' => $description,
                'metadata' => [
                    'order_ids' => implode(',', $orderIds),
                    'home_profile_member_id' => $memberUserId,
                    'guardian_user_id' => $g['guardian_user_id'],
                ],
            ]);
            return ['success' => $intent->status === 'succeeded', 'payment_intent_id' => $intent->id, 'error' => null];
        } catch (\Stripe\Exception\CardException $e) {
            return ['success' => false, 'payment_intent_id' => $e->getError()->payment_intent->id ?? null, 'error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'payment_intent_id' => null, 'error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------------- supervision

    /**
     * Emails the guardian about a member's order event. Called from the EmailHelper
     * order notifications, so every buyer email about a member order reaches the guardian too.
     */
    public static function notifyGuardianOfOrder(array $order, string $event): void
    {
        try {
            // Callers pass anything from a full orders row to a partial array: resolve from the DB.
            if (!array_key_exists('guardian_user_id', $order) || !isset($order['total'])) {
                $o = self::db()->prepare("SELECT id, user_id, order_number, total, status, guardian_user_id FROM orders WHERE id = ?");
                $o->execute([(int) ($order['id'] ?? 0)]);
                $row = $o->fetch(\PDO::FETCH_ASSOC);
                if (!$row) {
                    return;
                }
                $order = array_merge($row, array_filter($order, fn($v) => $v !== null && $v !== ''));
            }
            $guardianId = (int) ($order['guardian_user_id'] ?? 0);
            if (!$guardianId) {
                return;
            }
            $m = self::db()->prepare("SELECT first_name FROM users WHERE id = ?");
            $m->execute([(int) $order['user_id']]);
            $member = htmlspecialchars((string) $m->fetchColumn());
            $num = htmlspecialchars((string) ($order['order_number'] ?? ''));
            $total = number_format((float) ($order['total'] ?? 0), 2);
            $link = htmlspecialchars(rtrim(env('APP_URL', 'https://ocsapp.ca'), '/') . '/account/home/order?id=' . (int) $order['id']);

            $events = [
                'placed'    => ['a passé la commande', 'placed order'],
                'status'    => ['a une mise à jour pour la commande', 'has an update on order'],
                'cancelled' => ['a une commande annulée :', 'has a cancelled order:'],
            ];
            [$fr, $en] = $events[$event] ?? $events['status'];
            $statusLine = !empty($order['status']) ? htmlspecialchars((string) $order['status']) : '';

            self::emailGuardian($guardianId,
                "Profil Maison : $member, commande $num / Home Profile: $member, order $num",
                "<p><strong>$member</strong> $fr <strong>$num</strong> (" . str_replace('.', ',', $total) . " \$)." . ($statusLine ? " Statut : $statusLine." : '') . "</p>
                 <p><a href=\"$link\">Voir la commande et le suivi</a></p>",
                "<p><strong>$member</strong> $en <strong>$num</strong> (\$$total)." . ($statusLine ? " Status: $statusLine." : '') . "</p>
                 <p><a href=\"$link\">View the order and tracking</a></p>");
        } catch (\Throwable $e) {
            logger('Home Profile guardian notification failed: ' . $e->getMessage(), 'warning');
        }
    }

    private static function emailGuardian(int $guardianId, string $subject, string $htmlFr, string $htmlEn): void
    {
        $stmt = self::db()->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$guardianId]);
        $email = $stmt->fetchColumn();
        if ($email) {
            EmailHelper::sendRaw($email, $subject, self::emailShell($htmlFr, $htmlEn));
        }
    }

    private static function emailShell(string $htmlFr, string $htmlEn): string
    {
        return '<div style="font-family:Segoe UI,Arial,sans-serif;max-width:560px;margin:0 auto;color:#1a1a1a;line-height:1.6;">'
            . '<h2 style="color:#00b207;margin:0 0 12px;">OCSAPP</h2>'
            . $htmlFr
            . '<hr style="border:0;border-top:1px solid #e5e7eb;margin:24px 0;">'
            . $htmlEn
            . '</div>';
    }
}
