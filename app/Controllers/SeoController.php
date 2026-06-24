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
        $baseUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');

        // Static public routes to include
        $staticUrls = [
            ['loc' => $baseUrl . '/',               'changefreq' => 'daily',   'priority' => '1.0'],
            ['loc' => $baseUrl . '/shops',           'changefreq' => 'daily',   'priority' => '0.9'],
            ['loc' => $baseUrl . '/categories',      'changefreq' => 'weekly',  'priority' => '0.8'],
            ['loc' => $baseUrl . '/deals',           'changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => $baseUrl . '/best-sellers',    'changefreq' => 'weekly',  'priority' => '0.7'],
            ['loc' => $baseUrl . '/search',          'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/about',           'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => $baseUrl . '/contact',         'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => $baseUrl . '/seller-central',  'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/buyer-central',   'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => $baseUrl . '/terms',           'changefreq' => 'monthly', 'priority' => '0.3'],
            ['loc' => $baseUrl . '/privacy',         'changefreq' => 'monthly', 'priority' => '0.3'],
        ];

        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($staticUrls as $url) {
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            echo "    <changefreq>{$url['changefreq']}</changefreq>\n";
            echo "    <priority>{$url['priority']}</priority>\n";
            echo "  </url>\n";
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
