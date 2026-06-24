<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

/**
 * Admin Homepage Settings Controller
 * Manages homepage layout, sections, and SEO settings
 */
class AdminHomepageController {

    public function __construct() {
        // Super admin only - homepage settings are restricted
        AuthMiddleware::superAdmin();
    }

    /**
     * Display homepage settings dashboard
     */
    public function index(): void {
        try {
            $db = \Database::getConnection();

            // Get homepage settings (singleton - always ID 1)
            $stmt = $db->prepare("SELECT * FROM homepage_settings WHERE id = 1");
            $stmt->execute();
            $settings = $stmt->fetch(\PDO::FETCH_ASSOC);

            // If no settings exist, create default
            if (!$settings) {
                $this->createDefaultSettings();
                // Re-query after creating defaults
                $stmt = $db->prepare("SELECT * FROM homepage_settings WHERE id = 1");
                $stmt->execute();
                $settings = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            // Get available categories for featured selection
            $categoriesStmt = $db->query("SELECT id, name, image FROM categories WHERE is_active = 1 ORDER BY name");
            $categories = $categoriesStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get available shops for featured selection
            $shopsStmt = $db->query("SELECT id, company_name as shop_name, logo FROM vendors WHERE status = 'active' ORDER BY company_name");
            $shops = $shopsStmt->fetchAll(\PDO::FETCH_ASSOC);

            view('admin/homepage/index', [
                'settings' => $settings,
                'categories' => $categories,
                'shops' => $shops,
                'pageTitle' => 'Homepage Settings',
                'currentPage' => 'homepage-settings',
            ]);

        } catch (\PDOException $e) {
            logger("Homepage settings error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading homepage settings');
            redirect(url('admin/dashboard'));
        }
    }

    /**
     * Update homepage settings
     */
    public function update(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        try {
            $db = \Database::getConnection();

            // Section Visibility (checkboxes)
            $show_hero_slider = post('show_hero_slider') ? 1 : 0;
            $show_promo_banner = post('show_promo_banner') ? 1 : 0;
            $show_categories = post('show_categories') ? 1 : 0;
            $show_best_sellers = post('show_best_sellers') ? 1 : 0;
            $show_popular_shops = post('show_popular_shops') ? 1 : 0;
            $show_deals = post('show_deals') ? 1 : 0;
            $show_virtual_mall = post('show_virtual_mall') ? 1 : 0;
            $show_sustainability = post('show_sustainability') ? 1 : 0;

            // Section Titles
            $categories_title_en = post('categories_title_en');
            $categories_title_fr = post('categories_title_fr');
            $best_sellers_title_en = post('best_sellers_title_en');
            $best_sellers_title_fr = post('best_sellers_title_fr');
            $popular_shops_title_en = post('popular_shops_title_en');
            $popular_shops_title_fr = post('popular_shops_title_fr');
            $deals_title_en = post('deals_title_en');
            $deals_title_fr = post('deals_title_fr');
            $virtual_mall_title_en = post('virtual_mall_title_en');
            $virtual_mall_title_fr = post('virtual_mall_title_fr');

            // Section Descriptions
            $categories_desc_en = post('categories_desc_en');
            $categories_desc_fr = post('categories_desc_fr');
            $best_sellers_desc_en = post('best_sellers_desc_en');
            $best_sellers_desc_fr = post('best_sellers_desc_fr');
            $popular_shops_desc_en = post('popular_shops_desc_en');
            $popular_shops_desc_fr = post('popular_shops_desc_fr');

            // Display Counts
            $categories_display_count = (int)post('categories_display_count', 8);
            $best_sellers_display_count = (int)post('best_sellers_display_count', 8);
            $popular_shops_display_count = (int)post('popular_shops_display_count', 6);
            $deals_display_count = (int)post('deals_display_count', 4);

            // Featured Content (JSON arrays)
            $featured_categories = post('featured_categories');
            $featured_shops = post('featured_shops');

            // SEO Settings
            $meta_title_en = post('meta_title_en');
            $meta_title_fr = post('meta_title_fr');
            $meta_description_en = post('meta_description_en');
            $meta_description_fr = post('meta_description_fr');
            $meta_keywords_en = post('meta_keywords_en');
            $meta_keywords_fr = post('meta_keywords_fr');

            $stmt = $db->prepare("
                UPDATE homepage_settings
                SET show_hero_slider = ?,
                    show_promo_banner = ?,
                    show_categories = ?,
                    show_best_sellers = ?,
                    show_popular_shops = ?,
                    show_deals = ?,
                    show_virtual_mall = ?,
                    show_sustainability = ?,
                    categories_title_en = ?,
                    categories_title_fr = ?,
                    best_sellers_title_en = ?,
                    best_sellers_title_fr = ?,
                    popular_shops_title_en = ?,
                    popular_shops_title_fr = ?,
                    deals_title_en = ?,
                    deals_title_fr = ?,
                    virtual_mall_title_en = ?,
                    virtual_mall_title_fr = ?,
                    categories_desc_en = ?,
                    categories_desc_fr = ?,
                    best_sellers_desc_en = ?,
                    best_sellers_desc_fr = ?,
                    popular_shops_desc_en = ?,
                    popular_shops_desc_fr = ?,
                    categories_display_count = ?,
                    best_sellers_display_count = ?,
                    popular_shops_display_count = ?,
                    deals_display_count = ?,
                    featured_categories = ?,
                    featured_shops = ?,
                    meta_title_en = ?,
                    meta_title_fr = ?,
                    meta_description_en = ?,
                    meta_description_fr = ?,
                    meta_keywords_en = ?,
                    meta_keywords_fr = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = 1
            ");

            $stmt->execute([
                $show_hero_slider,
                $show_promo_banner,
                $show_categories,
                $show_best_sellers,
                $show_popular_shops,
                $show_deals,
                $show_virtual_mall,
                $show_sustainability,
                $categories_title_en,
                $categories_title_fr,
                $best_sellers_title_en,
                $best_sellers_title_fr,
                $popular_shops_title_en,
                $popular_shops_title_fr,
                $deals_title_en,
                $deals_title_fr,
                $virtual_mall_title_en,
                $virtual_mall_title_fr,
                $categories_desc_en,
                $categories_desc_fr,
                $best_sellers_desc_en,
                $best_sellers_desc_fr,
                $popular_shops_desc_en,
                $popular_shops_desc_fr,
                $categories_display_count,
                $best_sellers_display_count,
                $popular_shops_display_count,
                $deals_display_count,
                $featured_categories,
                $featured_shops,
                $meta_title_en,
                $meta_title_fr,
                $meta_description_en,
                $meta_description_fr,
                $meta_keywords_en,
                $meta_keywords_fr,
            ]);

            // Clear cache so helper functions get fresh data
            clearHomepageCache();

            setFlash('success', 'Homepage settings updated successfully!');
            redirect(url('admin/homepage'));

        } catch (\PDOException $e) {
            logger("Homepage settings update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating homepage settings: ' . $e->getMessage());
            back();
        }
    }

    /**
     * Create default settings if none exist
     */
    private function createDefaultSettings(): void {
        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                INSERT INTO homepage_settings (
                    id,
                    show_hero_slider,
                    show_promo_banner,
                    show_categories,
                    show_best_sellers,
                    show_popular_shops,
                    show_deals,
                    show_virtual_mall,
                    show_sustainability,
                    section_order,
                    categories_display_count,
                    best_sellers_display_count,
                    popular_shops_display_count,
                    deals_display_count
                ) VALUES (
                    1, 1, 1, 1, 1, 1, 1, 1, 1,
                    '[\"hero_slider\",\"promo_banner\",\"categories\",\"best_sellers\",\"popular_shops\",\"deals\",\"virtual_mall\",\"sustainability\"]',
                    8, 8, 6, 4
                )
            ");

            $stmt->execute();

        } catch (\PDOException $e) {
            logger("Create default settings error: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Reset settings to defaults
     */
    public function reset(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                UPDATE homepage_settings
                SET show_hero_slider = 1,
                    show_promo_banner = 1,
                    show_categories = 1,
                    show_best_sellers = 1,
                    show_popular_shops = 1,
                    show_deals = 1,
                    show_virtual_mall = 1,
                    show_sustainability = 1,
                    categories_title_en = NULL,
                    categories_title_fr = NULL,
                    best_sellers_title_en = NULL,
                    best_sellers_title_fr = NULL,
                    popular_shops_title_en = NULL,
                    popular_shops_title_fr = NULL,
                    deals_title_en = NULL,
                    deals_title_fr = NULL,
                    virtual_mall_title_en = NULL,
                    virtual_mall_title_fr = NULL,
                    categories_desc_en = NULL,
                    categories_desc_fr = NULL,
                    best_sellers_desc_en = NULL,
                    best_sellers_desc_fr = NULL,
                    popular_shops_desc_en = NULL,
                    popular_shops_desc_fr = NULL,
                    categories_display_count = 8,
                    best_sellers_display_count = 8,
                    popular_shops_display_count = 6,
                    deals_display_count = 4,
                    featured_categories = NULL,
                    featured_shops = NULL,
                    meta_title_en = NULL,
                    meta_title_fr = NULL,
                    meta_description_en = NULL,
                    meta_description_fr = NULL,
                    meta_keywords_en = NULL,
                    meta_keywords_fr = NULL,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = 1
            ");

            $stmt->execute();

            // Clear cache so helper functions get fresh data
            clearHomepageCache();

            setFlash('success', 'Homepage settings reset to defaults successfully!');
            redirect(url('admin/homepage'));

        } catch (\PDOException $e) {
            logger("Homepage settings reset error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error resetting homepage settings');
            back();
        }
    }
}
