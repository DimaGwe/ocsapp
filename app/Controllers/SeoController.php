<?php

namespace App\Controllers;

/**
 * SeoController
 * Serves sitemap.xml and robots.txt for search engine crawlers.
 */
class SeoController
{
    public function sitemap(): void
    {
        // Public pages: path => [changefreq, priority]. Pages with a French and an English
        // address (localized_paths()) are listed once per language, linked with hreflang.
        $pages = [
            ''                 => ['daily',   '1.0'],
            'marketplace-central' => ['daily', '0.9'],
            'shops'            => ['daily',   '0.9'],
            'categories'       => ['weekly',  '0.8'],
            'deals'            => ['daily',   '0.8'],
            'best-sellers'     => ['weekly',  '0.7'],
            'buyer-central'    => ['monthly', '0.6'],
            'seller-central'   => ['monthly', '0.6'],
            'supplier-central' => ['monthly', '0.6'],
            'driver-central'   => ['monthly', '0.6'],
            'distribution'     => ['monthly', '0.6'],
            'founding'         => ['monthly', '0.5'],
            'waitlist'         => ['monthly', '0.5'],
            'about'            => ['monthly', '0.4'],
            'contact'          => ['monthly', '0.4'],
            'terms'            => ['monthly', '0.3'],
            'privacy'          => ['monthly', '0.3'],
            'returns'          => ['monthly', '0.3'],
            'cookies'          => ['monthly', '0.2'],
            'accessibility'    => ['monthly', '0.2'],
        ];
        foreach (array_keys(\App\Helpers\OnboardingPackageHelper::FILES) as $role) {
            $pages['onboarding/' . $role] = ['monthly', '0.4'];
        }

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($pages as $path => [$freq, $priority]) {
            $localized = localized_path_lang($path) !== null;
            foreach ($localized ? ['fr', 'en'] : [null] as $lang) {
                echo "  <url>\n";
                echo '    <loc>' . htmlspecialchars(url($path, $lang ?? 'en')) . "</loc>\n";
                if ($localized) {
                    foreach (['fr-CA' => 'fr', 'en-CA' => 'en', 'x-default' => 'fr'] as $hreflang => $l) {
                        echo '    <xhtml:link rel="alternate" hreflang="' . $hreflang . '" href="' . htmlspecialchars(url($path, $l)) . "\"/>\n";
                    }
                }
                echo "    <changefreq>{$freq}</changefreq>\n";
                echo "    <priority>{$priority}</priority>\n";
                echo "  </url>\n";
            }
        }

        echo '</urlset>';
        exit;
    }

    public function robots(): void
    {
        $baseUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');

        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "\n";
        // Block admin, supplier, distribution, delivery, and account areas
        echo "Disallow: /admin/\n";
        echo "Disallow: /supplier/\n";
        echo "Disallow: /distribution/\n";
        echo "Disallow: /delivery/\n";
        echo "Disallow: /account/\n";
        echo "Disallow: /checkout/\n";
        echo "Disallow: /payment/\n";
        echo "Disallow: /cart/\n";
        echo "Disallow: /api/\n";
        echo "Disallow: /login\n";
        echo "Disallow: /register\n";
        echo "Disallow: /forgot-password\n";
        echo "Disallow: /reset-password\n";
        echo "\n";
        echo "Sitemap: {$baseUrl}/sitemap.xml\n";
        exit;
    }
}
