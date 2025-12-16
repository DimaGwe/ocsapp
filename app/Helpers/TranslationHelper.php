<?php

/**
 * Translation Helper - Database-Driven Multilingual Support
 *
 * Usage:
 *   t('key')           - Get translation for current language
 *   t('key', 'fr')     - Get French translation
 *   trans('key')       - Alias for t()
 *
 * The helper loads translations from the database and caches them
 * in session for performance.
 */

if (!function_exists('getTranslationsFromDb')) {
    /**
     * Load all translations from database
     * Caches in session for performance
     */
    function getTranslationsFromDb(bool $forceRefresh = false): array
    {
        // Check session cache first
        if (!$forceRefresh && isset($_SESSION['translations_cache']) && isset($_SESSION['translations_cache_time'])) {
            // Cache for 5 minutes
            if (time() - $_SESSION['translations_cache_time'] < 300) {
                return $_SESSION['translations_cache'];
            }
        }

        try {
            $db = \Database::getConnection();
            $stmt = $db->query("SELECT `key`, en, fr, is_html FROM translations");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $translations = [
                'en' => [],
                'fr' => []
            ];

            foreach ($rows as $row) {
                $translations['en'][$row['key']] = [
                    'text' => $row['en'] ?? '',
                    'is_html' => (bool)$row['is_html']
                ];
                $translations['fr'][$row['key']] = [
                    'text' => $row['fr'] ?? '',
                    'is_html' => (bool)$row['is_html']
                ];
            }

            // Cache in session
            $_SESSION['translations_cache'] = $translations;
            $_SESSION['translations_cache_time'] = time();

            return $translations;

        } catch (\PDOException $e) {
            error_log("Translation DB error: " . $e->getMessage());
            // Return empty array on error - will use fallback
            return ['en' => [], 'fr' => []];
        }
    }
}

if (!function_exists('t')) {
    /**
     * Get translation for key
     *
     * @param string $key Translation key
     * @param string|null $lang Language code (en, fr) or null for current
     * @param string $default Default value if not found
     * @return string Translated text
     */
    function t(string $key, ?string $lang = null, string $default = ''): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $translations = getTranslationsFromDb();

        // Check if key exists in database translations
        if (isset($translations[$lang][$key])) {
            $item = $translations[$lang][$key];
            $text = $item['text'];

            // If empty, try English fallback
            if (empty($text) && $lang !== 'en') {
                $text = $translations['en'][$key]['text'] ?? '';
            }

            if (!empty($text)) {
                // If HTML, return as-is; otherwise escape
                return $item['is_html'] ? $text : htmlspecialchars($text);
            }
        }

        // Fallback to English
        if ($lang !== 'en' && isset($translations['en'][$key])) {
            $item = $translations['en'][$key];
            if (!empty($item['text'])) {
                return $item['is_html'] ? $item['text'] : htmlspecialchars($item['text']);
            }
        }

        // Return default or key
        return $default ?: $key;
    }
}

if (!function_exists('trans')) {
    /**
     * Alias for t()
     */
    function trans(string $key, ?string $lang = null, string $default = ''): string
    {
        return t($key, $lang, $default);
    }
}

if (!function_exists('tRaw')) {
    /**
     * Get translation without escaping (for pre-escaped or HTML content)
     */
    function tRaw(string $key, ?string $lang = null, string $default = ''): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $translations = getTranslationsFromDb();

        if (isset($translations[$lang][$key]['text'])) {
            $text = $translations[$lang][$key]['text'];
            if (!empty($text)) {
                return $text;
            }
        }

        // Fallback to English
        if ($lang !== 'en' && isset($translations['en'][$key]['text'])) {
            return $translations['en'][$key]['text'];
        }

        return $default ?: $key;
    }
}

if (!function_exists('getCurrentLanguage')) {
    /**
     * Get current language from session
     */
    function getCurrentLanguage(): string
    {
        return $_SESSION['language'] ?? 'fr';
    }
}

if (!function_exists('setCurrentLanguage')) {
    /**
     * Set current language in session
     */
    function setCurrentLanguage(string $lang): void
    {
        if (in_array($lang, ['en', 'fr'])) {
            $_SESSION['language'] = $lang;
        }
    }
}

if (!function_exists('clearTranslationCache')) {
    /**
     * Clear the translation cache
     */
    function clearTranslationCache(): void
    {
        unset($_SESSION['translations_cache']);
        unset($_SESSION['translations_cache_time']);
    }
}

if (!function_exists('hasTranslation')) {
    /**
     * Check if a translation key exists
     */
    function hasTranslation(string $key, ?string $lang = null): bool
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $translations = getTranslationsFromDb();
        return isset($translations[$lang][$key]) && !empty($translations[$lang][$key]['text']);
    }
}

if (!function_exists('getAllTranslationKeys')) {
    /**
     * Get all translation keys (for debugging/admin)
     */
    function getAllTranslationKeys(): array
    {
        $translations = getTranslationsFromDb();
        return array_keys($translations['en'] ?? []);
    }
}
