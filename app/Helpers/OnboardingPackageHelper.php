<?php

namespace App\Helpers;

/**
 * The official onboarding packages: self-contained HTML files (EN + FR-QC) in
 * public/onboarding-packages/, generated outside the app (C:\DCC\OCSAPP CTO Bob\Onboarding Packages,
 * build_<role>.py). This is the single source used by /onboarding/{role}, the portal
 * Documents pages and the business approval email. Public links always use the clean
 * routes (EN /onboarding/{role}, FR /guide-accueil/{slug}, + /pdf); the raw .html files redirect there.
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

    /** Role => French URL slug (/guide-accueil/{slug} is the FR address of /onboarding/{role}). */
    const FR_SLUGS = [
        'buyer'    => 'acheteur',
        'seller'   => 'vendeur',
        'supplier' => 'fournisseur',
        'driver'   => 'livreur',
        'business' => 'entreprise',
    ];

    public static function exists(string $role): bool
    {
        return isset(self::FILES[$role]);
    }

    /** Accepts a role or its French slug ('acheteur' => 'buyer'); unknown values pass through. */
    public static function roleFromSlug(string $slug): string
    {
        return array_search($slug, self::FR_SLUGS, true) ?: $slug;
    }

    /** File name for a role; $fr defaults to the current session language. */
    public static function fileName(string $role, ?bool $fr = null, string $ext = 'html'): string
    {
        $fr = $fr ?? (($_SESSION['language'] ?? 'fr') === 'fr');
        return 'OCSAPP_' . self::FILES[$role] . '_Onboarding_Package_' . ($fr ? 'FR_QC' : 'EN') . '.' . $ext;
    }

    /** Clean public URL (/onboarding/buyer or /guide-accueil/acheteur); null $fr = session language. */
    public static function url(string $role, ?bool $fr = null): string
    {
        return url('onboarding/' . $role, $fr === null ? null : ($fr ? 'fr' : 'en'));
    }

    public static function pdfUrl(string $role, ?bool $fr = null): string
    {
        return url('onboarding/' . $role . '/pdf', $fr === null ? null : ($fr ? 'fr' : 'en'));
    }

    /** Absolute path on disk (served by /onboarding/{role}, attached to emails). */
    public static function path(string $role, ?bool $fr = null, string $ext = 'html'): string
    {
        return BASE_PATH . '/public/onboarding-packages/' . self::fileName($role, $fr, $ext);
    }
}
