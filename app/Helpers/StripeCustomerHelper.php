<?php

namespace App\Helpers;

require_once __DIR__ . '/PaymentGatewayHelper.php';

/**
 * StripeCustomerHelper - card-on-file (Ecosystem Backend Requirements Sec.
 * 5.3 "card-on-file requirement enforced at all times").
 *
 * No card-on-file capability existed anywhere in this codebase before this -
 * every prior Stripe integration (buyer checkout, distribution one-off
 * payments) uses one-shot Checkout Sessions with no saved PaymentMethod.
 * This uses a real Stripe Customer + SetupIntent + off-session PaymentIntent
 * flow rather than Stripe's native Subscription object for the recurring
 * plan billing in Task 13 - avoids needing pre-configured Dashboard
 * Product/Price objects, and matches this codebase's existing preference for
 * direct PaymentIntent calls over higher-level Stripe constructs.
 */
class StripeCustomerHelper
{
    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    private static function initStripe(): bool
    {
        $config = getStripeConfig();
        if (empty($config['secret_key'])) {
            return false;
        }
        \Stripe\Stripe::setApiKey($config['secret_key']);
        return true;
    }

    /**
     * Returns the business's Stripe Customer ID, creating one on first use.
     * @throws \RuntimeException if Stripe isn't configured
     */
    public static function ensureCustomer(int $businessProfileId): string
    {
        if (!self::initStripe()) {
            throw new \RuntimeException('Card payments are not configured.');
        }

        $db = self::db();
        $stmt = $db->prepare("
            SELECT bp.stripe_customer_id, bp.company_name, u.email
            FROM business_profiles bp JOIN users u ON u.id = bp.user_id
            WHERE bp.id = ?
        ");
        $stmt->execute([$businessProfileId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            throw new \RuntimeException('Business account not found.');
        }
        if (!empty($row['stripe_customer_id'])) {
            return $row['stripe_customer_id'];
        }

        $customer = \Stripe\Customer::create([
            'email' => $row['email'],
            'name' => $row['company_name'],
            'metadata' => ['business_profile_id' => $businessProfileId],
        ]);

        $db->prepare("UPDATE business_profiles SET stripe_customer_id = ? WHERE id = ?")
           ->execute([$customer->id, $businessProfileId]);

        return $customer->id;
    }

    /**
     * Creates a SetupIntent for the "add/update card" flow. Returns the
     * client_secret the frontend needs to complete Stripe.js confirmCardSetup.
     */
    public static function createSetupIntentClientSecret(int $businessProfileId): string
    {
        if (!self::initStripe()) {
            throw new \RuntimeException('Card payments are not configured.');
        }
        $customerId = self::ensureCustomer($businessProfileId);

        $setupIntent = \Stripe\SetupIntent::create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'usage' => 'off_session',
        ]);

        return $setupIntent->client_secret;
    }

    /**
     * Called after the frontend confirms the SetupIntent (or from the
     * setup_intent.succeeded webhook) - retrieves the resulting PaymentMethod
     * and saves it as the account's card-on-file.
     */
    public static function saveCardFromSetupIntent(int $businessProfileId, string $setupIntentId): void
    {
        if (!self::initStripe()) {
            throw new \RuntimeException('Card payments are not configured.');
        }

        $setupIntent = \Stripe\SetupIntent::retrieve($setupIntentId);
        if ($setupIntent->status !== 'succeeded' || empty($setupIntent->payment_method)) {
            throw new \RuntimeException('Card setup did not complete successfully.');
        }

        $pm = \Stripe\PaymentMethod::retrieve($setupIntent->payment_method);
        $card = $pm->card;

        self::db()->prepare("
            UPDATE business_profiles
            SET stripe_payment_method_id = ?, card_brand = ?, card_last4 = ?, card_exp_month = ?, card_exp_year = ?
            WHERE id = ?
        ")->execute([
            $pm->id, $card->brand ?? null, $card->last4 ?? null,
            $card->exp_month ?? null, $card->exp_year ?? null,
            $businessProfileId,
        ]);

        \App\Helpers\CreditHelper::logEvent($businessProfileId, 'card_on_file_saved', 'business', null,
            ($card->brand ?? 'card') . ' ****' . ($card->last4 ?? ''));
    }

    /**
     * Off-session charge against the saved card - used by plan billing
     * (Task 13) and the Sec 5.3 auto-charge-overdue job. Never called
     * synchronously from a request the business is actively waiting on
     * (that's what the one-shot Checkout Session flow is for) - this is for
     * scheduled/automated charges only.
     *
     * @return array{success: bool, payment_intent_id: ?string, error: ?string}
     */
    public static function chargeOffSession(int $businessProfileId, float $amount, string $description): array
    {
        if (!self::initStripe()) {
            return ['success' => false, 'payment_intent_id' => null, 'error' => 'Card payments are not configured.'];
        }

        $db = self::db();
        $stmt = $db->prepare("SELECT stripe_customer_id, stripe_payment_method_id FROM business_profiles WHERE id = ?");
        $stmt->execute([$businessProfileId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (empty($row['stripe_customer_id']) || empty($row['stripe_payment_method_id'])) {
            return ['success' => false, 'payment_intent_id' => null, 'error' => 'No card on file.'];
        }

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => (int)round($amount * 100),
                'currency' => 'cad',
                'customer' => $row['stripe_customer_id'],
                'payment_method' => $row['stripe_payment_method_id'],
                'off_session' => true,
                'confirm' => true,
                'description' => $description,
                'metadata' => ['business_profile_id' => $businessProfileId],
            ]);

            return ['success' => $intent->status === 'succeeded', 'payment_intent_id' => $intent->id, 'error' => null];
        } catch (\Stripe\Exception\CardException $e) {
            return ['success' => false, 'payment_intent_id' => $e->getError()->payment_intent->id ?? null, 'error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'payment_intent_id' => null, 'error' => $e->getMessage()];
        }
    }
}
