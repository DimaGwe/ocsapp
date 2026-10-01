<?php

namespace App\Helpers;

require_once __DIR__ . '/FoundingBonusHelper.php';

/**
 * FoundingDriverHelper - Founding Driver Partner Program (Driver Agreement
 * Sec 6.2/8.13/Schedule D, draft): a 50-driver cohort earning a permanent
 * "Founding Driver" badge, plus (since 2026-10): a one-time milestone bonus
 * ($100 for 20 deliveries in the first 30 days), a referral bonus ($50 each
 * when a referred driver completes 15 deliveries in their first 30 days),
 * priority dispatch (new orders are offered to Founding Drivers first), and
 * an equipment kit tracked in users.founding_kit_status. Bonuses go through
 * FoundingBonusHelper's ledger and are paid as a delivery_earnings line in
 * the normal weekly payout. Membership itself never expires.
 *
 * Same single-row-mutex pattern as FoundingSellerHelper - a global, race-safe
 * counter (founding_driver_program) decides eligibility at the moment a
 * driver application is approved (AdminDeliveryController::approveApplicationPipeline()),
 * locked via SELECT...FOR UPDATE so two approvals in quick succession can't
 * both claim slot #50.
 */
class FoundingDriverHelper
{
    const TOTAL_SLOTS = 50;

    // Sec 8.13.1 - milestone bonus
    const MILESTONE_ORDERS = 20;
    const MILESTONE_BONUS = 100.00;
    const MILESTONE_DAYS = 30;
    // Sec 8.13.2 - referral bonus (paid to both drivers)
    const REFERRAL_ORDERS = 15;
    const REFERRAL_BONUS = 50.00;
    const REFERRAL_DAYS = 30;
    // Sec 8.13.3 - priority tier: other drivers see a ready order this many seconds later
    const PRIORITY_HEAD_START_SECONDS = 60;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Call once, at driver-approval time. Idempotent - a driver already
     * granted founding status just re-reports its existing slot.
     *
     * @return array{eligible: bool, founding_driver_number: ?int}
     */
    public static function claimSlotIfEligible(int $userId): array
    {
        $db = self::db();

        $userStmt = $db->prepare("SELECT founding_driver, founding_driver_number FROM users WHERE id = ? FOR UPDATE");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
        if ($user && (int)$user['founding_driver'] === 1) {
            return ['eligible' => true, 'founding_driver_number' => (int)$user['founding_driver_number']];
        }

        $startedTransaction = false;
        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $counterStmt = $db->prepare("SELECT slots_used, slots_total FROM founding_driver_program WHERE id = 1 FOR UPDATE");
            $counterStmt->execute();
            $counter = $counterStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$counter || (int)$counter['slots_used'] >= (int)$counter['slots_total']) {
                if ($startedTransaction) $db->rollBack();
                return ['eligible' => false, 'founding_driver_number' => null];
            }

            $slotNumber = (int)$counter['slots_used'] + 1;

            $db->prepare("UPDATE founding_driver_program SET slots_used = ? WHERE id = 1")
               ->execute([$slotNumber]);

            $db->prepare("
                UPDATE users SET
                    founding_driver = 1, founding_driver_number = ?, founding_driver_granted_at = NOW(),
                    founding_kit_status = 'pending', founding_kit_updated_at = NOW()
                WHERE id = ?
            ")->execute([$slotNumber, $userId]);

            if ($startedTransaction) $db->commit();

            return ['eligible' => true, 'founding_driver_number' => $slotNumber];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('FoundingDriverHelper::claimSlotIfEligible error: ' . $e->getMessage());
            return ['eligible' => false, 'founding_driver_number' => null];
        }
    }

    public static function isFounding(int $userId): bool
    {
        $stmt = self::db()->prepare("SELECT founding_driver FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn() === 1;
    }

    /** Sec 8.13.3: Founding Drivers sit at the top dispatch priority tier from day one. */
    public static function hasPriorityDispatch(int $userId): bool
    {
        return self::isFounding($userId);
    }

    /** When the driver became an active contractor: approval date. */
    public static function activeSince(int $userId): ?string
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT founding_driver_granted_at, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user) return null;
        if (!empty($user['founding_driver_granted_at'])) return $user['founding_driver_granted_at'];

        $app = $db->prepare("SELECT reviewed_at FROM driver_applications WHERE user_id = ? AND status = 'approved' AND reviewed_at IS NOT NULL ORDER BY id DESC LIMIT 1");
        $app->execute([$userId]);
        return $app->fetchColumn() ?: $user['created_at'];
    }

    /** Completed deliveries inside the driver's first $days days (bonus lines excluded). */
    public static function completedInWindow(int $userId, string $since, int $days): int
    {
        $stmt = self::db()->prepare("
            SELECT COUNT(*) FROM delivery_earnings de
            WHERE de.driver_id = ?
              AND de.created_at >= ? AND de.created_at < DATE_ADD(?, INTERVAL ? DAY)
              AND de.id NOT IN (SELECT earning_id FROM founding_bonuses WHERE earning_id IS NOT NULL)
        ");
        $stmt->execute([$userId, $since, $since, $days]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Call right after a delivery's earnings row is written. Awards the
     * milestone bonus and the referral bonus when their thresholds are met.
     * Never throws: a bonus problem must not break delivery completion.
     */
    public static function afterDeliveryRecorded(int $driverId, int $assignmentId): void
    {
        try {
            $since = self::activeSince($driverId);
            if (!$since) return;

            if (self::isFounding($driverId)
                && !FoundingBonusHelper::has('driver', 'milestone', $driverId)
                && self::completedInWindow($driverId, $since, self::MILESTONE_DAYS) >= self::MILESTONE_ORDERS) {
                self::payBonus($driverId, $assignmentId, 'milestone', 0, self::MILESTONE_BONUS,
                    'Founding Driver milestone bonus (' . self::MILESTONE_ORDERS . ' deliveries in ' . self::MILESTONE_DAYS . ' days)',
                    'Milestone reached: ' . self::MILESTONE_ORDERS . ' deliveries in your first ' . self::MILESTONE_DAYS . ' days. Your $' . number_format(self::MILESTONE_BONUS, 0) . ' Founding Driver bonus is in your next weekly payout.',
                    'Étape franchie : ' . self::MILESTONE_ORDERS . ' livraisons dans vos ' . self::MILESTONE_DAYS . ' premiers jours. Votre prime Livreur fondateur de ' . number_format(self::MILESTONE_BONUS, 0) . ' $ sera incluse dans votre prochain versement hebdomadaire.');
            }

            // Referral: this driver was referred by a Founding Driver on the application
            $ref = self::db()->prepare("SELECT referred_by_user_id FROM driver_applications WHERE user_id = ? AND referred_by_user_id IS NOT NULL ORDER BY id DESC LIMIT 1");
            $ref->execute([$driverId]);
            $referrerId = (int)$ref->fetchColumn();
            if ($referrerId && $referrerId !== $driverId && self::isFounding($referrerId)
                && !FoundingBonusHelper::has('driver', 'referral_referred', $driverId, $referrerId)
                && self::completedInWindow($driverId, $since, self::REFERRAL_DAYS) >= self::REFERRAL_ORDERS) {
                $amount = number_format(self::REFERRAL_BONUS, 0);
                self::payBonus($driverId, $assignmentId, 'referral_referred', $referrerId, self::REFERRAL_BONUS,
                    'Founding Driver referral bonus (referred driver)',
                    'You completed ' . self::REFERRAL_ORDERS . ' deliveries in your first ' . self::REFERRAL_DAYS . ' days: your $' . $amount . ' referral bonus is in your next weekly payout.',
                    'Vous avez complété ' . self::REFERRAL_ORDERS . ' livraisons dans vos ' . self::REFERRAL_DAYS . ' premiers jours : votre prime de parrainage de ' . $amount . ' $ sera incluse dans votre prochain versement hebdomadaire.');
                self::payBonus($referrerId, $assignmentId, 'referral_referrer', $driverId, self::REFERRAL_BONUS,
                    'Founding Driver referral bonus (referrer)',
                    'A driver you referred completed ' . self::REFERRAL_ORDERS . ' deliveries: your $' . $amount . ' referral bonus is in your next weekly payout.',
                    'Un livreur que vous avez parrainé a complété ' . self::REFERRAL_ORDERS . ' livraisons : votre prime de parrainage de ' . $amount . ' $ sera incluse dans votre prochain versement hebdomadaire.');
            }
        } catch (\Throwable $e) {
            error_log('FoundingDriverHelper::afterDeliveryRecorded error: ' . $e->getMessage());
        }
    }

    /**
     * Ledger row first (the UNIQUE key decides whether this bonus is new),
     * then the delivery_earnings line the weekly payout batch picks up.
     * delivery_earnings.delivery_id is a required FK, so the bonus line points
     * at the delivery that triggered it.
     */
    private static function payBonus(int $driverId, int $assignmentId, string $type, int $relatedId, float $amount, string $note, string $msgEn, string $msgFr): void
    {
        $ledgerId = FoundingBonusHelper::award('driver', $type, $driverId, $relatedId, $amount, $note);
        if (!$ledgerId) return;

        $db = self::db();
        $db->prepare("
            INSERT INTO delivery_earnings
            (driver_id, delivery_id, order_id, base_fee, bonus, total_earning, platform_commission, net_earning,
             payment_status, payout_method, notes, created_at, updated_at)
            VALUES (?, ?, NULL, 0.00, ?, ?, 0.00, ?, 'pending', 'pending', ?, NOW(), NOW())
        ")->execute([$driverId, $assignmentId, $amount, $amount, $amount, $note]);
        $db->prepare("UPDATE founding_bonuses SET earning_id = ? WHERE id = ?")->execute([(int)$db->lastInsertId(), $ledgerId]);

        require_once __DIR__ . '/NotificationHelper.php';
        NotificationHelper::addDriverNotification($driverId, $msgEn, 'info', 0, $msgFr);
        NotificationHelper::add('delivery', 'Founding Driver bonus awarded',
            "Driver #{$driverId}: {$note} - \$" . number_format($amount, 2) . ' added to the next payout batch.',
            ['link' => '/admin/delivery/driver-details?id=' . $driverId]);
    }

    /** Dashboard data: bonus progress, referral code, kit status. */
    public static function perks(int $userId): array
    {
        $db = self::db();
        $since = self::activeSince($userId);
        $stmt = $db->prepare("SELECT referral_code, founding_kit_status FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
        $code = $user['referral_code'] ?? null;
        if (!$code) {
            require_once __DIR__ . '/ReferralHelper.php';
            $code = ReferralHelper::assignReferralCode($userId);
        }
        // Measured on the DB clock: timestamps are stored in DB time, PHP runs in America/Toronto
        $daysLeft = 0;
        if ($since) {
            $dl = self::db()->prepare("SELECT GREATEST(0, ? - TIMESTAMPDIFF(DAY, ?, NOW()))");
            $dl->execute([self::MILESTONE_DAYS, $since]);
            $daysLeft = (int)$dl->fetchColumn();
        }
        $refs = $db->prepare("SELECT COUNT(*) FROM founding_bonuses WHERE program = 'driver' AND bonus_type = 'referral_referrer' AND beneficiary_id = ?");
        $refs->execute([$userId]);
        return [
            'milestone_done'   => FoundingBonusHelper::has('driver', 'milestone', $userId),
            'milestone_count'  => $since ? self::completedInWindow($userId, $since, self::MILESTONE_DAYS) : 0,
            'milestone_target' => self::MILESTONE_ORDERS,
            'milestone_bonus'  => self::MILESTONE_BONUS,
            'days_left'        => $daysLeft,
            'referral_code'    => $code,
            'referral_bonus'   => self::REFERRAL_BONUS,
            'referral_orders'  => self::REFERRAL_ORDERS,
            'referrals_paid'   => (int)$refs->fetchColumn(),
            'kit_status'       => $user['founding_kit_status'] ?? null,
        ];
    }

    public static function remainingSlots(): int
    {
        $stmt = self::db()->query("SELECT slots_total - slots_used FROM founding_driver_program WHERE id = 1");
        return max(0, (int)($stmt->fetchColumn() ?: 0));
    }
}
