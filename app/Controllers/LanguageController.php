<?php

namespace App\Controllers;

class LanguageController {

    /**
     * Switch language for current session
     */
    public function switch(): void {
        $lang = post('lang') ?? get('lang') ?? 'en';

        // Validate language
        if (!in_array($lang, ['en', 'fr'])) {
            $lang = 'en';
        }

        // Set language in session
        setCurrentLanguage($lang);

        // Clear translation cache to reload with new language
        clearTranslationCache();

        // Handle AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'language' => $lang,
                'message' => $lang === 'fr' ? 'Langue changée en français' : 'Language changed to English'
            ]);
            exit;
        }

        // Regular request - redirect back
        setFlash('success', $lang === 'fr' ? 'Langue changée en français' : 'Language changed to English');
        back();
    }

    /**
     * Get current language (API endpoint)
     */
    public function current(): void {
        header('Content-Type: application/json');
        echo json_encode([
            'language' => getCurrentLanguage()
        ]);
        exit;
    }
}
