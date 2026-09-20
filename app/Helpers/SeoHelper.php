<?php
/**
 * SEO Helper - Meta Tags, Schema.org, Open Graph, Twitter Cards
 * File: app/Helpers/SeoHelper.php
 */

namespace App\Helpers;

class SeoHelper {

    /**
     * Generate complete meta tags for a page
     *
     * @param array $data Page data containing title, description, image, etc.
     * @return string HTML meta tags
     */
    public static function generateMetaTags(array $data = []): string {
        $db = \Database::getConnection();
        $stmt = $db->query("SELECT `key`, value FROM settings WHERE category = 'seo'");
        $seoSettings = [];
        while ($row = $stmt->fetch()) {
            $seoSettings[$row['key']] = $row['value'];
        }

        // Get current language
        $lang = $_SESSION['language'] ?? 'en';
        $alternateLang = $lang === 'en' ? 'fr' : 'en';

        // Build title
        $siteName = $seoSettings['seo_site_name'] ?? 'OCS Marketplace';
        $separator = $seoSettings['seo_title_separator'] ?? '|';
        $title = isset($data['title'])
            ? htmlspecialchars($data['title']) . " $separator $siteName"
            : ($seoSettings['seo_default_title'] ?? $siteName);

        // Build description
        $description = isset($data['description'])
            ? htmlspecialchars($data['description'])
            : ($seoSettings['seo_default_description'] ?? '');

        // Build keywords
        $keywords = isset($data['keywords'])
            ? htmlspecialchars($data['keywords'])
            : ($seoSettings['seo_default_keywords'] ?? '');

        // Build image URL
        $image = isset($data['image'])
            ? self::getAbsoluteUrl($data['image'])
            : self::getAbsoluteUrl($seoSettings['seo_og_default_image'] ?? '');

        // Current URL
        $url = isset($data['url'])
            ? self::getAbsoluteUrl($data['url'])
            : self::getCurrentUrl();

        // Canonical URL
        $canonical = isset($data['canonical'])
            ? self::getAbsoluteUrl($data['canonical'])
            : $url;

        // Robots meta
        $robots = $data['robots'] ?? 'index,follow';

        // Type (for Open Graph)
        $type = $data['type'] ?? 'website';

        // Twitter card type
        $twitterCard = $data['twitter_card'] ?? 'summary_large_image';

        // Build meta tags
        $meta = [];

        // Basic SEO
        $meta[] = "<title>$title</title>";
        $meta[] = "<meta name=\"description\" content=\"$description\">";
        if ($keywords) {
            $meta[] = "<meta name=\"keywords\" content=\"$keywords\">";
        }
        $meta[] = "<meta name=\"robots\" content=\"$robots\">";
        $meta[] = "<link rel=\"canonical\" href=\"$canonical\">";

        // Language and hreflang for bilingual site
        $meta[] = "<meta http-equiv=\"content-language\" content=\"$lang\">";
        $meta[] = "<link rel=\"alternate\" hreflang=\"en\" href=\"" . self::getUrlWithLang($url, 'en') . "\">";
        $meta[] = "<link rel=\"alternate\" hreflang=\"fr\" href=\"" . self::getUrlWithLang($url, 'fr') . "\">";
        $meta[] = "<link rel=\"alternate\" hreflang=\"x-default\" href=\"" . self::getUrlWithLang($url, 'en') . "\">";

        // Open Graph
        $meta[] = "<meta property=\"og:type\" content=\"$type\">";
        $meta[] = "<meta property=\"og:title\" content=\"" . htmlspecialchars($data['title'] ?? $title) . "\">";
        $meta[] = "<meta property=\"og:description\" content=\"$description\">";
        $meta[] = "<meta property=\"og:url\" content=\"$url\">";
        $meta[] = "<meta property=\"og:site_name\" content=\"$siteName\">";
        $meta[] = "<meta property=\"og:locale\" content=\"" . ($lang === 'en' ? 'en_CA' : 'fr_CA') . "\">";
        $meta[] = "<meta property=\"og:locale:alternate\" content=\"" . ($alternateLang === 'en' ? 'en_CA' : 'fr_CA') . "\">";
        if ($image) {
            $meta[] = "<meta property=\"og:image\" content=\"$image\">";
            $meta[] = "<meta property=\"og:image:width\" content=\"1200\">";
            $meta[] = "<meta property=\"og:image:height\" content=\"630\">";
            $meta[] = "<meta property=\"og:image:alt\" content=\"" . htmlspecialchars($data['title'] ?? $siteName) . "\">";
        }

        // Open Graph for products
        if ($type === 'product' && isset($data['product'])) {
            $product = $data['product'];
            if (isset($product['price'])) {
                $meta[] = "<meta property=\"product:price:amount\" content=\"{$product['price']}\">";
                $meta[] = "<meta property=\"product:price:currency\" content=\"CAD\">";
            }
            if (isset($product['availability'])) {
                $availability = $product['availability'] ? 'in stock' : 'out of stock';
                $meta[] = "<meta property=\"product:availability\" content=\"$availability\">";
            }
            if (isset($product['brand'])) {
                $meta[] = "<meta property=\"product:brand\" content=\"" . htmlspecialchars($product['brand']) . "\">";
            }
        }

        // Twitter Cards
        $meta[] = "<meta name=\"twitter:card\" content=\"$twitterCard\">";
        $meta[] = "<meta name=\"twitter:title\" content=\"" . htmlspecialchars($data['title'] ?? $title) . "\">";
        $meta[] = "<meta name=\"twitter:description\" content=\"$description\">";
        if ($image) {
            $meta[] = "<meta name=\"twitter:image\" content=\"$image\">";
            $meta[] = "<meta name=\"twitter:image:alt\" content=\"" . htmlspecialchars($data['title'] ?? $siteName) . "\">";
        }
        if (!empty($seoSettings['seo_twitter_username'])) {
            $meta[] = "<meta name=\"twitter:site\" content=\"{$seoSettings['seo_twitter_username']}\">";
        }

        // Facebook App ID
        if (!empty($seoSettings['seo_facebook_app_id'])) {
            $meta[] = "<meta property=\"fb:app_id\" content=\"{$seoSettings['seo_facebook_app_id']}\">";
        }

        // Google verification
        if (!empty($seoSettings['seo_google_site_verification'])) {
            $meta[] = "<meta name=\"google-site-verification\" content=\"{$seoSettings['seo_google_site_verification']}\">";
        }

        return implode("\n    ", $meta);
    }

    /**
     * Generate Product Schema (JSON-LD)
     */
    public static function generateProductSchema(array $product): string {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => strip_tags($product['description'] ?? ''),
            'sku' => $product['sku'] ?? '',
            'image' => isset($product['image']) ? self::getAbsoluteUrl($product['image']) : '',
            'url' => isset($product['url']) ? self::getAbsoluteUrl($product['url']) : self::getCurrentUrl(),
        ];

        // Brand
        if (!empty($product['brand'])) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product['brand']
            ];
        }

        // Offers
        if (isset($product['price'])) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $product['price'],
                'priceCurrency' => 'CAD',
                'availability' => ($product['stock'] ?? 0) > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url' => $schema['url'],
            ];

            if (isset($product['valid_until'])) {
                $schema['offers']['priceValidUntil'] = $product['valid_until'];
            }
        }

        // Aggregate Rating
        if (isset($product['rating']) && isset($product['review_count'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product['rating'],
                'reviewCount' => $product['review_count'],
                'bestRating' => '5',
                'worstRating' => '1'
            ];
        }

        return '<script type="application/ld+json">' . "\n" . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
    }

    /**
     * Generate LocalBusiness Schema (for shops)
     */
    public static function generateLocalBusinessSchema(array $shop): string {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Store',
            'name' => $shop['name'],
            'description' => strip_tags($shop['description'] ?? ''),
            'url' => isset($shop['url']) ? self::getAbsoluteUrl($shop['url']) : self::getCurrentUrl(),
        ];

        if (!empty($shop['logo'])) {
            $schema['logo'] = self::getAbsoluteUrl($shop['logo']);
            $schema['image'] = self::getAbsoluteUrl($shop['logo']);
        }

        if (!empty($shop['email'])) {
            $schema['email'] = $shop['email'];
        }

        if (!empty($shop['phone'])) {
            $schema['telephone'] = $shop['phone'];
        }

        if (!empty($shop['address'])) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'addressCountry' => 'CA',
                'addressLocality' => $shop['city'] ?? '',
                'addressRegion' => $shop['province'] ?? '',
                'postalCode' => $shop['postal_code'] ?? '',
                'streetAddress' => $shop['address']
            ];
        }

        if (isset($shop['rating']) && isset($shop['review_count'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $shop['rating'],
                'reviewCount' => $shop['review_count'],
                'bestRating' => '5',
                'worstRating' => '1'
            ];
        }

        return '<script type="application/ld+json">' . "\n" . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
    }

    /**
     * Generate Organization Schema (site-wide)
     */
    public static function generateOrganizationSchema(): string {
        $db = \Database::getConnection();
        $stmt = $db->query("SELECT value FROM settings WHERE `key` = 'seo_schema_organization'");
        $schemaJson = $stmt->fetchColumn();

        if ($schemaJson) {
            $schema = json_decode($schemaJson, true);
            return '<script type="application/ld+json">' . "\n" . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
        }

        // Default organization schema
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'OCS Marketplace',
            'url' => self::getBaseUrl(),
            'logo' => self::getAbsoluteUrl('uploads/logo.png'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'Customer Service',
                'availableLanguage' => ['English', 'French']
            ],
            'sameAs' => [
                'https://facebook.com/ocsmarketplace',
                'https://twitter.com/ocsmarketplace',
                'https://instagram.com/ocsmarketplace'
            ]
        ];

        return '<script type="application/ld+json">' . "\n" . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
    }

    /**
     * Generate Breadcrumb Schema
     */
    public static function generateBreadcrumbSchema(array $breadcrumbs): string {
        $items = [];
        $position = 1;

        foreach ($breadcrumbs as $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $breadcrumb['name'],
                'item' => self::getAbsoluteUrl($breadcrumb['url'])
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];

        return '<script type="application/ld+json">' . "\n" . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
    }

    /**
     * Generate SearchAction Schema (for site search)
     */
    public static function generateSearchActionSchema(): string {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'url' => self::getBaseUrl(),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => self::getBaseUrl() . '/search?q={search_term_string}'
                ],
                'query-input' => 'required name=search_term_string'
            ]
        ];

        return '<script type="application/ld+json">' . "\n" . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n" . '</script>';
    }

    /**
     * Get absolute URL from relative path
     */
    private static function getAbsoluteUrl(string $path): string {
        if (empty($path)) {
            return '';
        }

        // Already absolute
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }

        $baseUrl = self::getBaseUrl();
        $path = ltrim($path, '/');

        return $baseUrl . '/' . $path;
    }

    /**
     * Get base URL
     */
    private static function getBaseUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'ocsapp.ca';

        return $protocol . '://' . $host;
    }

    /**
     * Get current URL
     */
    private static function getCurrentUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'ocsapp.ca';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return $protocol . '://' . $host . $uri;
    }

    /**
     * Get URL with language parameter
     */
    private static function getUrlWithLang(string $url, string $lang): string {
        $parts = parse_url($url);
        $query = [];

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query['lang'] = $lang;

        $newUrl = $parts['scheme'] . '://' . $parts['host'] . ($parts['path'] ?? '');
        $newUrl .= '?' . http_build_query($query);

        return $newUrl;
    }

    /**
     * Truncate text for meta descriptions
     */
    public static function truncateDescription(string $text, int $maxLength = 160): string {
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $text = mb_substr($text, 0, $maxLength);
        $lastSpace = mb_strrpos($text, ' ');

        if ($lastSpace !== false) {
            $text = mb_substr($text, 0, $lastSpace);
        }

        return $text . '...';
    }

    /**
     * Extract keywords from text
     */
    public static function extractKeywords(string $text, int $maxKeywords = 10): string {
        $text = strip_tags($text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        // Remove common stop words
        $stopWords = ['the', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'a', 'an', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from'];
        $words = explode(' ', $text);
        $words = array_filter($words, function($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 3;
        });

        // Count word frequency
        $wordCount = array_count_values($words);
        arsort($wordCount);

        // Get top keywords
        $keywords = array_slice(array_keys($wordCount), 0, $maxKeywords);

        return implode(', ', $keywords);
    }
}
