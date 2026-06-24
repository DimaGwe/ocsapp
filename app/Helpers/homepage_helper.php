<?php

/**
 * Homepage Settings Helper Functions
 * Provides easy access to homepage configuration
 */

if (!function_exists('getHomepageSettings')) {
    /**
     * Get homepage settings (singleton - cached in session)
     *
     * @param bool $forceRefresh Force reload from database
     * @return array Homepage settings
     */
    function getHomepageSettings(bool $forceRefresh = false): array
    {
        // Check session cache first (5 minute cache)
        if (!$forceRefresh && isset($_SESSION['homepage_settings']) && isset($_SESSION['homepage_settings_time'])) {
            if (time() - $_SESSION['homepage_settings_time'] < 300) {
                return $_SESSION['homepage_settings'];
            }
        }

        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM homepage_settings WHERE id = 1");
            $stmt->execute();
            $settings = $stmt->fetch(\PDO::FETCH_ASSOC);

            // If no settings exist, return defaults
            if (!$settings) {
                $settings = getDefaultHomepageSettings();
            }

            // Cache in session
            $_SESSION['homepage_settings'] = $settings;
            $_SESSION['homepage_settings_time'] = time();

            return $settings;

        } catch (\PDOException $e) {
            error_log("Homepage settings error: " . $e->getMessage());
            return getDefaultHomepageSettings();
        }
    }
}

if (!function_exists('getDefaultHomepageSettings')) {
    /**
     * Get default homepage settings
     *
     * @return array Default settings
     */
    function getDefaultHomepageSettings(): array
    {
        return [
            'id' => 1,
            'show_hero_slider' => 1,
            'show_promo_banner' => 1,
            'show_categories' => 1,
            'show_best_sellers' => 1,
            'show_popular_shops' => 1,
            'show_deals' => 1,
            'show_virtual_mall' => 1,
            'show_sustainability' => 1,
            'section_order' => '["hero_slider","promo_banner","categories","best_sellers","popular_shops","deals","virtual_mall","sustainability"]',
            'categories_display_count' => 8,
            'best_sellers_display_count' => 8,
            'popular_shops_display_count' => 6,
            'deals_display_count' => 4,
        ];
    }
}

if (!function_exists('isHomepageSectionVisible')) {
    /**
     * Check if a homepage section should be visible
     *
     * @param string $section Section name (e.g., 'categories', 'best_sellers')
     * @return bool True if section is visible
     */
    function isHomepageSectionVisible(string $section): bool
    {
        $settings = getHomepageSettings();
        $key = 'show_' . $section;

        return isset($settings[$key]) && $settings[$key] == 1;
    }
}

if (!function_exists('getHomepageSectionTitle')) {
    /**
     * Get customized section title or fall back to translation
     *
     * @param string $section Section name (e.g., 'categories', 'best_sellers')
     * @param string|null $lang Language code (null = current language)
     * @return string Section title
     */
    function getHomepageSectionTitle(string $section, ?string $lang = null): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $settings = getHomepageSettings();
        $key = $section . '_title_' . $lang;

        // Return custom title if set
        if (!empty($settings[$key])) {
            return $settings[$key];
        }

        // Fall back to translation
        $translationKeys = [
            'categories' => 'popular_categories',
            'best_sellers' => 'best_sellers',
            'popular_shops' => 'top_shops',
            'deals' => 'hot_deals',
            'virtual_mall' => 'virtual_mall',
        ];

        $translationKey = $translationKeys[$section] ?? $section;
        return t($translationKey);
    }
}

if (!function_exists('getHomepageSectionDescription')) {
    /**
     * Get customized section description or fall back to translation
     *
     * @param string $section Section name
     * @param string|null $lang Language code (null = current language)
     * @return string Section description
     */
    function getHomepageSectionDescription(string $section, ?string $lang = null): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $settings = getHomepageSettings();
        $key = $section . '_desc_' . $lang;

        // Return custom description if set
        if (!empty($settings[$key])) {
            return $settings[$key];
        }

        // Return empty string if no custom description
        return '';
    }
}

if (!function_exists('getHomepageSectionCount')) {
    /**
     * Get number of items to display in a section
     *
     * @param string $section Section name
     * @return int Number of items to display
     */
    function getHomepageSectionCount(string $section): int
    {
        $settings = getHomepageSettings();
        $key = $section . '_display_count';

        return isset($settings[$key]) ? (int)$settings[$key] : 8;
    }
}

if (!function_exists('getHomepageMetaTitle')) {
    /**
     * Get homepage meta title for SEO
     *
     * @param string|null $lang Language code
     * @return string Meta title
     */
    function getHomepageMetaTitle(?string $lang = null): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $settings = getHomepageSettings();
        $key = 'meta_title_' . $lang;

        if (!empty($settings[$key])) {
            return $settings[$key];
        }

        // Default meta title
        return 'OCSAPP – Zero-Emission Grocery Delivery';
    }
}

if (!function_exists('getHomepageMetaDescription')) {
    /**
     * Get homepage meta description for SEO
     *
     * @param string|null $lang Language code
     * @return string Meta description
     */
    function getHomepageMetaDescription(?string $lang = null): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $settings = getHomepageSettings();
        $key = 'meta_description_' . $lang;

        if (!empty($settings[$key])) {
            return $settings[$key];
        }

        // Default meta description
        return 'Shop groceries, restaurants, stores & more on OCSAPP Marketplace';
    }
}

if (!function_exists('clearHomepageCache')) {
    /**
     * Clear homepage settings cache
     * Call this after updating homepage settings
     *
     * @return void
     */
    function clearHomepageCache(): void
    {
        unset($_SESSION['homepage_settings']);
        unset($_SESSION['homepage_settings_time']);
    }
}
