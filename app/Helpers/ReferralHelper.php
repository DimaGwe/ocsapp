<?php

namespace App\Helpers;

require_once __DIR__ . '/StoreCreditHelper.php';

/**
 * ReferralHelper - Founding Buyer Program, Referral Credit (Buyer Terms of
 * Service Sec. 12.1): "referring a friend who completes their first Order
 * earns you both a $5 credit toward a future Delivery Fee, with no limit on
 * the number of friends you may refer." Available to every buyer regardless
 * of Founding status - unlike the 200-slot free-delivery perk, there's no
 * cap here.
 */
class ReferralHelper
{
    const REFERRAL_BONUS_AMOUNT = 5.00;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Assigns a unique referral code to a newly-registered buyer. Called
     * once at registration - a short, human-shareable code (not a UUID),
     * same shape as the pre-existing waitlist referral_code precedent
     * elsewhere in this codebase.
     */
    public static function assignReferralCode(int $userId): string
    {
        $db = self::db();
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $exists = $db->prepare("SELECT id FROM users WHERE referral_code = ?");
            $exists->execute([$code]);
        } while ($exists->fetch());

        $db->prepare("UPDATE users SET referral_code = ? WHERE id = ?")->execute([$code, $userId]);
        return $code;
    }

    /**
     * Resolves a ?ref= code from the registration form to the referrer's
     * user id. Returns null silently on an invalid/unknown code - an
     * expired or mistyped referral link should never block registration.
     */
    public static function resolveReferrerId(string $code): ?int
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return null;
        }
        $stmt = self::db()->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->execute([$code]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }

    /**
     * Records who referred a new buyer, at registration time. Does not
     * credit anything yet - the $5 only pays out once the referred buyer's
     * first Order is actually delivered (Sec 12.1's "completes their first
     * Order"), not merely on signup.
     */
    public static function recordReferral(int $newUserId, ?int $referrerId): void
    {
        if (!$referrerId || $referrerId === $newUserId) {
            return;
        }
        self::db()->prepare("UPDATE users SET referred_by_user_id = ? WHERE id = ?")
            ->execute([$referrerId, $newUserId]);
    }

    /**
     * Call after an order's status is set to 'delivered' (Track A / Marché
     * only - the ToS's Founding Buyer Program is a Buyer-facing perk).
     * Credits both the referrer and the referred buyer $5 the first time
     * any of the referred buyer's orders reaches delivered - never twice,
     * guarded by referral_bonus_credited_at.
     */
    public static function maybeCreditReferral(int $orderId): void
    {
        $db = self::db();

        $orderStmt = $db->prepare("SELECT id, user_id FROM orders WHERE id = ?");
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) {
            return;
        }

        $userId = (int)$order['user_id'];
        $userStmt = $db->prepare("SELECT referred_by_user_id, referral_bonus_credited_at FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user || !$user['referred_by_user_id'] || $user['referral_bonus_credited_at']) {
            return;
        }

        // "Completes their first Order" - this must be the first time any
        // of this buyer's orders has reached delivered.
        $priorDelivered = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'delivered' AND id != ?");
        $priorDelivered->execute([$userId, $orderId]);
        if ((int)$priorDelivered->fetchColumn() > 0) {
            return;
        }

        $referrerId = (int)$user['referred_by_user_id'];

        // Mark credited BEFORE issuing the credit, inside no wider
        // transaction than this call - a duplicate concurrent call to this
        // method (e.g. two of the three delivery-completion code paths
        // racing) finds referral_bonus_credited_at already set and returns
        // above instead of double-crediting.
        $marked = $db->prepare("
            UPDATE users SET referral_bonus_credited_at = NOW()
            WHERE id = ? AND referral_bonus_credited_at IS NULL
        ");
        $marked->execute([$userId]);
        if ($marked->rowCount() === 0) {
            // Lost the race to a concurrent call - already handled.
            return;
        }

        \App\Helpers\StoreCreditHelper::addReferralCredit(
            $referrerId, self::REFERRAL_BONUS_AMOUNT,
            "Referral bonus: your friend's first order (order #{$orderId}) was delivered"
        );
        \App\Helpers\StoreCreditHelper::addReferralCredit(
            $userId, self::REFERRAL_BONUS_AMOUNT,
            "Referral bonus: your first order (order #{$orderId}) was delivered"
        );

        require_once __DIR__ . '/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'referral', '🎉 Referral Bonus Paid',
            "Referrer #{$referrerId} and buyer #{$userId} each credited \$5 - referred buyer's first order (#{$orderId}) delivered.",
            ['link' => '/admin/orders/view?id=' . $orderId, 'icon' => 'gift', 'priority' => 'low']
        );
    }
}
