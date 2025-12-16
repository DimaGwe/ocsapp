<?php

/**
 * OCS Translation Helper - Database-Driven (Unified)
 *
 * This replaces both the old file-based translation system and provides
 * backward compatibility with existing views that use $t['key'] syntax.
 *
 * Usage:
 *   t('key')                    - Get translation for current language
 *   t('key', 'fr')              - Get French translation
 *   trans('key')                - Alias for t()
 *   getTranslations('en')       - Get all translations as array (backward compatible)
 *   getCurrentLanguage()        - Get current language code
 *   setCurrentLanguage('fr')    - Set current language
 */

/**
 * Load all translations from database
 * Caches in session for performance (5 minute cache)
 */
if (!function_exists('loadTranslationsFromDb')) {
    function loadTranslationsFromDb(bool $forceRefresh = false): array
    {
        // Check session cache first
        if (!$forceRefresh && isset($_SESSION['db_translations']) && isset($_SESSION['db_translations_time'])) {
            // Cache for 5 minutes
            if (time() - $_SESSION['db_translations_time'] < 300) {
                return $_SESSION['db_translations'];
            }
        }

        $translations = ['en' => [], 'fr' => []];

        try {
            $db = \Database::getConnection();
            $stmt = $db->query("SELECT `key`, en, fr, is_html FROM translations");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $translations['en'][$row['key']] = $row['en'] ?? '';
                $translations['fr'][$row['key']] = $row['fr'] ?? '';
            }

            // Cache in session
            $_SESSION['db_translations'] = $translations;
            $_SESSION['db_translations_time'] = time();

        } catch (\PDOException $e) {
            error_log("Translation DB error: " . $e->getMessage());
            // Return empty arrays on error
        }

        return $translations;
    }
}

/**
 * Get all translations for a language (BACKWARD COMPATIBLE)
 * This is what existing views use: $t = getTranslations($lang);
 */
if (!function_exists('getTranslations')) {
    function getTranslations(?string $lang = null): array
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $translations = loadTranslationsFromDb();

        return $translations[$lang] ?? $translations['en'] ?? [];
    }
}

/**
 * Get a single translation by key
 */
if (!function_exists('t')) {
    function t(string $key, ?string $lang = null, string $default = ''): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $translations = loadTranslationsFromDb();

        // Try requested language
        if (isset($translations[$lang][$key]) && !empty($translations[$lang][$key])) {
            return htmlspecialchars($translations[$lang][$key]);
        }

        // Fallback to English
        if ($lang !== 'en' && isset($translations['en'][$key]) && !empty($translations['en'][$key])) {
            return htmlspecialchars($translations['en'][$key]);
        }

        // Return default or key itself
        return $default ?: $key;
    }
}

/**
 * Alias for t()
 */
if (!function_exists('trans')) {
    function trans(string $key, ?string $lang = null, string $default = ''): string
    {
        return t($key, $lang, $default);
    }
}

/**
 * Get translation without HTML escaping (for HTML content)
 */
if (!function_exists('tRaw')) {
    function tRaw(string $key, ?string $lang = null, string $default = ''): string
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $translations = loadTranslationsFromDb();

        if (isset($translations[$lang][$key]) && !empty($translations[$lang][$key])) {
            return $translations[$lang][$key];
        }

        if ($lang !== 'en' && isset($translations['en'][$key]) && !empty($translations['en'][$key])) {
            return $translations['en'][$key];
        }

        return $default ?: $key;
    }
}

/**
 * Get current language from session
 */
if (!function_exists('getCurrentLanguage')) {
    function getCurrentLanguage(): string
    {
        return $_SESSION['language'] ?? 'fr';
    }
}

/**
 * Set current language in session
 */
if (!function_exists('setCurrentLanguage')) {
    function setCurrentLanguage(string $lang): void
    {
        if (in_array($lang, ['en', 'fr'])) {
            $_SESSION['language'] = $lang;
        }
    }
}

/**
 * Clear translation cache (call after updating translations)
 */
if (!function_exists('clearTranslationCache')) {
    function clearTranslationCache(): void
    {
        unset($_SESSION['db_translations']);
        unset($_SESSION['db_translations_time']);
    }
}

/**
 * Check if a translation key exists
 */
if (!function_exists('hasTranslation')) {
    function hasTranslation(string $key, ?string $lang = null): bool
    {
        if ($lang === null) {
            $lang = getCurrentLanguage();
        }

        $translations = loadTranslationsFromDb();
        return isset($translations[$lang][$key]) && !empty($translations[$lang][$key]);
    }
}
