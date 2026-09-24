<?php

namespace App\Helpers;

/**
 * The official onboarding packages: self-contained HTML files (EN + FR-QC) in
 * public/onboarding-packages/, generated outside the app (C:\DCC\OCSAPP CTO Bob\Onboarding Packages,
 * build_<role>.py). This is the single source used by /onboarding/{role}, the portal
 * Documents pages and the business approval email.
 */
class OnboardingPackageHelper
{
    /** Waitlist/account role => file name part. */
    const FILES = [
        'buyer'    => 'Buyer',
        'seller'   => 'Seller',
        'supplier' => 'Supplier',
        'driver'   => 'Driver',
        'business' => 'Business_Account',
    ];

    public static function exists(string $role): bool
    {
        return isset(self::FILES[$role]);
    }

    /** File name for a role; $fr defaults to the current session language. */
    public static function fileName(string $role, ?bool $fr = null): string
    {
        $fr = $fr ?? (($_SESSION['language'] ?? 'fr') === 'fr');
        return 'OCSAPP_' . self::FILES[$role] . '_Onboarding_Package_' . ($fr ? 'FR_QC' : 'EN') . '.html';
    }

    public static function url(string $role, ?bool $fr = null): string
    {
        return url('onboarding-packages/' . self::fileName($role, $fr));
    }

    /** Absolute path on disk (for email attachments). */
    public static function path(string $role, bool $fr): string
    {
        return BASE_PATH . '/public/onboarding-packages/' . self::fileName($role, $fr);
    }
}
