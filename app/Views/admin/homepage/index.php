<?php
/**
 * OCSAPP Admin - Homepage Settings
 * File: app/Views/admin/homepage/index.php
 */

$pageTitle = $pageTitle ?? 'Homepage Settings';
$currentPage = $currentPage ?? 'homepage-settings';

ob_start();
?>

<style>
  /* Page Header */
  .homepage-header {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .homepage-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 12px;
  }

  /* Tabs */
  .settings-tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid var(--border);
    margin-bottom: 32px;
    overflow-x: auto;
  }

  .tab-btn {
    padding: 14px 24px;
    background: none;
    border: none;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-600);
    cursor: pointer;
    transition: all var(--transition-base);
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .tab-btn:hover {
    color: var(--primary);
    background: var(--gray-50);
  }

  .tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
  }

  .tab-content {
    display: none;
  }

  .tab-content.active {
    display: block;
  }

  /* Settings Card */
  .settings-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
  }

  .settings-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* Form Groups */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
  }

  .form-group {
    margin-bottom: 0;
  }

  .form-group label {
    display: block;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
    font-size: 14px;
  }

  .form-group input[type="text"],
  .form-group input[type="number"],
  .form-group textarea,
  .form-group select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    transition: all var(--transition-base);
  }

  .form-group textarea {
    min-height: 100px;
    resize: vertical;
  }

  .form-group input:focus,
  .form-group textarea:focus,
  .form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-hint {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 6px;
  }

  /* Toggle Switch */
  .toggle-group {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    margin-bottom: 12px;
    transition: all var(--transition-base);
  }

  .toggle-group:hover {
    background: #e8f5e9;
  }

  .toggle-info {
    flex: 1;
  }

  .toggle-label {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .toggle-desc {
    font-size: 12px;
    color: var(--gray-600);
    margin-top: 4px;
  }

  .toggle-switch {
    position: relative;
    width: 52px;
    height: 28px;
    flex-shrink: 0;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .3s;
    border-radius: 28px;
  }

  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
  }

  .toggle-switch input:checked + .toggle-slider {
    background-color: var(--primary);
  }

  .toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(24px);
  }

  /* Action Buttons */
  .action-buttons {
    display: flex;
    gap: 12px;
    padding-top: 24px;
    border-top: 2px solid var(--border);
    margin-top: 32px;
  }

  .btn-save {
    padding: 14px 32px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-save:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-reset {
    padding: 14px 32px;
    background: white;
    color: #ef4444;
    border: 2px solid #ef4444;
    border-radius: var(--radius-md);
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-reset:hover {
    background: #ef4444;
    color: white;
  }

  /* Info Banner */
  .info-banner {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: start;
    gap: 12px;
  }

  .info-banner i {
    color: #3b82f6;
    font-size: 20px;
    margin-top: 2px;
  }

  .info-banner-content p {
    margin: 0;
    font-size: 14px;
    color: #1e40af;
    line-height: 1.6;
  }

  /* Language Tabs */
  .lang-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }

  .lang-tab {
    padding: 8px 16px;
    background: var(--gray-100);
    border: 2px solid transparent;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .lang-tab.active {
    background: white;
    border-color: var(--primary);
    color: var(--primary);
  }

  .lang-content {
    display: none;
  }

  .lang-content.active {
    display: block;
  }
</style>

<!-- Page Header -->
<div class="homepage-header">
  <div>
    <h1>
      <i class="fas fa-home" style="color: var(--primary);"></i>
      Homepage Settings
    </h1>
    <p style="color: var(--gray-600); margin-top: 8px;">
      Control your homepage layout, content, and SEO settings
    </p>
  </div>
</div>

<!-- Info Banner -->
<div class="info-banner">
  <i class="fas fa-lightbulb"></i>
  <div class="info-banner-content">
    <p><strong>Quick Tip:</strong> Use the toggles to show/hide sections, customize titles to override defaults, and manage featured content. Changes take effect immediately on your homepage.</p>
  </div>
</div>

<!-- Main Form -->
<form method="POST" action="<?= url('admin/homepage/update') ?>" id="homepageForm">
  <?= csrfField() ?>

  <!-- Tabs Navigation -->
  <div class="settings-tabs">
    <button type="button" class="tab-btn active" data-tab="visibility">
      <i class="fas fa-eye"></i> Section Visibility
    </button>
    <button type="button" class="tab-btn" data-tab="titles">
      <i class="fas fa-heading"></i> Section Titles
    </button>
    <button type="button" class="tab-btn" data-tab="display">
      <i class="fas fa-sliders-h"></i> Display Settings
    </button>
    <button type="button" class="tab-btn" data-tab="seo">
      <i class="fas fa-search"></i> SEO Settings
    </button>
  </div>

  <!-- Tab 1: Section Visibility -->
  <div class="tab-content active" id="tab-visibility">
    <div class="settings-card">
      <h3>
        <i class="fas fa-toggle-on"></i>
        Show/Hide Homepage Sections
      </h3>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-images" style="color: var(--primary);"></i>
            Hero Slider
          </div>
          <div class="toggle-desc">Main banner carousel at the top of the homepage</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_hero_slider" value="1" <?= $settings['show_hero_slider'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-tag" style="color: #f59e0b;"></i>
            Promo Banner
          </div>
          <div class="toggle-desc">Promotional discount banner with featured products</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_promo_banner" value="1" <?= $settings['show_promo_banner'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-th-large" style="color: #8b5cf6;"></i>
            Popular Categories
          </div>
          <div class="toggle-desc">Showcase main product categories with icons</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_categories" value="1" <?= $settings['show_categories'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-fire" style="color: #ef4444;"></i>
            Best Sellers
          </div>
          <div class="toggle-desc">Display top-selling products</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_best_sellers" value="1" <?= $settings['show_best_sellers'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-store" style="color: #06b6d4;"></i>
            Popular Shops
          </div>
          <div class="toggle-desc">Feature top vendors and their shops</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_popular_shops" value="1" <?= $settings['show_popular_shops'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-percentage" style="color: #ec4899;"></i>
            Deals Section
          </div>
          <div class="toggle-desc">Highlight products with special discounts</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_deals" value="1" <?= $settings['show_deals'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-building" style="color: #10b981;"></i>
            Virtual Mall
          </div>
          <div class="toggle-desc">Show shop categories (Grocery, Food Court, etc.)</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_virtual_mall" value="1" <?= $settings['show_virtual_mall'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="toggle-group">
        <div class="toggle-info">
          <div class="toggle-label">
            <i class="fas fa-leaf" style="color: #22c55e;"></i>
            Sustainability Section
          </div>
          <div class="toggle-desc">Eco-friendly delivery information banner</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="show_sustainability" value="1" <?= $settings['show_sustainability'] ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
      </div>
    </div>
  </div>

  <!-- Tab 2: Section Titles -->
  <div class="tab-content" id="tab-titles">
    <div class="settings-card">
      <h3>
        <i class="fas fa-heading"></i>
        Customize Section Titles & Descriptions
      </h3>
      <p style="color: var(--gray-600); margin-bottom: 24px; font-size: 14px;">
        Leave blank to use default translations. Custom titles will override the default text.
      </p>

      <!-- Popular Categories -->
      <div style="margin-bottom: 32px; padding-bottom: 32px; border-bottom: 2px solid var(--border);">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--dark);">
          <i class="fas fa-th-large" style="color: #8b5cf6;"></i> Popular Categories
        </h4>

        <div class="lang-tabs">
          <button type="button" class="lang-tab active" data-lang="en" data-section="categories">🇬🇧 English</button>
          <button type="button" class="lang-tab" data-lang="fr" data-section="categories">🇫🇷 Français</button>
        </div>

        <div class="lang-content active" id="categories-en">
          <div class="form-group">
            <label>Title (English)</label>
            <input type="text" name="categories_title_en" value="<?= htmlspecialchars($settings['categories_title_en'] ?? '') ?>" placeholder="Popular Categories">
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label>Description (English)</label>
            <textarea name="categories_desc_en" placeholder="Shop by category"><?= htmlspecialchars($settings['categories_desc_en'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="lang-content" id="categories-fr">
          <div class="form-group">
            <label>Titre (Français)</label>
            <input type="text" name="categories_title_fr" value="<?= htmlspecialchars($settings['categories_title_fr'] ?? '') ?>" placeholder="Catégories Populaires">
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label>Description (Français)</label>
            <textarea name="categories_desc_fr" placeholder="Acheter par catégorie"><?= htmlspecialchars($settings['categories_desc_fr'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Best Sellers -->
      <div style="margin-bottom: 32px; padding-bottom: 32px; border-bottom: 2px solid var(--border);">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--dark);">
          <i class="fas fa-fire" style="color: #ef4444;"></i> Best Sellers
        </h4>

        <div class="lang-tabs">
          <button type="button" class="lang-tab active" data-lang="en" data-section="bestsellers">🇬🇧 English</button>
          <button type="button" class="lang-tab" data-lang="fr" data-section="bestsellers">🇫🇷 Français</button>
        </div>

        <div class="lang-content active" id="bestsellers-en">
          <div class="form-group">
            <label>Title (English)</label>
            <input type="text" name="best_sellers_title_en" value="<?= htmlspecialchars($settings['best_sellers_title_en'] ?? '') ?>" placeholder="Best Sellers">
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label>Description (English)</label>
            <textarea name="best_sellers_desc_en" placeholder="Our most popular products"><?= htmlspecialchars($settings['best_sellers_desc_en'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="lang-content" id="bestsellers-fr">
          <div class="form-group">
            <label>Titre (Français)</label>
            <input type="text" name="best_sellers_title_fr" value="<?= htmlspecialchars($settings['best_sellers_title_fr'] ?? '') ?>" placeholder="Meilleures Ventes">
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label>Description (Français)</label>
            <textarea name="best_sellers_desc_fr" placeholder="Nos produits les plus populaires"><?= htmlspecialchars($settings['best_sellers_desc_fr'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Popular Shops -->
      <div style="margin-bottom: 32px; padding-bottom: 32px; border-bottom: 2px solid var(--border);">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--dark);">
          <i class="fas fa-store" style="color: #06b6d4;"></i> Popular Shops
        </h4>

        <div class="lang-tabs">
          <button type="button" class="lang-tab active" data-lang="en" data-section="shops">🇬🇧 English</button>
          <button type="button" class="lang-tab" data-lang="fr" data-section="shops">🇫🇷 Français</button>
        </div>

        <div class="lang-content active" id="shops-en">
          <div class="form-group">
            <label>Title (English)</label>
            <input type="text" name="popular_shops_title_en" value="<?= htmlspecialchars($settings['popular_shops_title_en'] ?? '') ?>" placeholder="Popular Shops">
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label>Description (English)</label>
            <textarea name="popular_shops_desc_en" placeholder="Shop from trusted vendors"><?= htmlspecialchars($settings['popular_shops_desc_en'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="lang-content" id="shops-fr">
          <div class="form-group">
            <label>Titre (Français)</label>
            <input type="text" name="popular_shops_title_fr" value="<?= htmlspecialchars($settings['popular_shops_title_fr'] ?? '') ?>" placeholder="Boutiques Populaires">
          </div>
          <div class="form-group" style="margin-top: 12px;">
            <label>Description (Français)</label>
            <textarea name="popular_shops_desc_fr" placeholder="Achetez auprès de vendeurs de confiance"><?= htmlspecialchars($settings['popular_shops_desc_fr'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Deals -->
      <div style="margin-bottom: 32px; padding-bottom: 32px; border-bottom: 2px solid var(--border);">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--dark);">
          <i class="fas fa-percentage" style="color: #ec4899;"></i> Deals Section
        </h4>

        <div class="lang-tabs">
          <button type="button" class="lang-tab active" data-lang="en" data-section="deals">🇬🇧 English</button>
          <button type="button" class="lang-tab" data-lang="fr" data-section="deals">🇫🇷 Français</button>
        </div>

        <div class="lang-content active" id="deals-en">
          <div class="form-group">
            <label>Title (English)</label>
            <input type="text" name="deals_title_en" value="<?= htmlspecialchars($settings['deals_title_en'] ?? '') ?>" placeholder="Hot Deals">
          </div>
        </div>

        <div class="lang-content" id="deals-fr">
          <div class="form-group">
            <label>Titre (Français)</label>
            <input type="text" name="deals_title_fr" value="<?= htmlspecialchars($settings['deals_title_fr'] ?? '') ?>" placeholder="Offres Spéciales">
          </div>
        </div>
      </div>

      <!-- Virtual Mall -->
      <div style="margin-bottom: 0;">
        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: var(--dark);">
          <i class="fas fa-building" style="color: #10b981;"></i> Virtual Mall
        </h4>

        <div class="lang-tabs">
          <button type="button" class="lang-tab active" data-lang="en" data-section="mall">🇬🇧 English</button>
          <button type="button" class="lang-tab" data-lang="fr" data-section="mall">🇫🇷 Français</button>
        </div>

        <div class="lang-content active" id="mall-en">
          <div class="form-group">
            <label>Title (English)</label>
            <input type="text" name="virtual_mall_title_en" value="<?= htmlspecialchars($settings['virtual_mall_title_en'] ?? '') ?>" placeholder="OCSAPP Virtual Mall">
          </div>
        </div>

        <div class="lang-content" id="mall-fr">
          <div class="form-group">
            <label>Titre (Français)</label>
            <input type="text" name="virtual_mall_title_fr" value="<?= htmlspecialchars($settings['virtual_mall_title_fr'] ?? '') ?>" placeholder="Centre Commercial Virtuel OCSAPP">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 3: Display Settings -->
  <div class="tab-content" id="tab-display">
    <div class="settings-card">
      <h3>
        <i class="fas fa-sliders-h"></i>
        Display Settings & Counts
      </h3>
      <p style="color: var(--gray-600); margin-bottom: 24px; font-size: 14px;">
        Control how many items to display in each section
      </p>

      <div class="form-grid">
        <div class="form-group">
          <label>
            <i class="fas fa-th-large" style="color: #8b5cf6;"></i>
            Categories to Display
          </label>
          <input type="number" name="categories_display_count" value="<?= $settings['categories_display_count'] ?>" min="4" max="20" step="1">
          <p class="form-hint">Recommended: 8 categories</p>
        </div>

        <div class="form-group">
          <label>
            <i class="fas fa-fire" style="color: #ef4444;"></i>
            Best Sellers to Display
          </label>
          <input type="number" name="best_sellers_display_count" value="<?= $settings['best_sellers_display_count'] ?>" min="4" max="20" step="1">
          <p class="form-hint">Recommended: 8 products</p>
        </div>

        <div class="form-group">
          <label>
            <i class="fas fa-store" style="color: #06b6d4;"></i>
            Popular Shops to Display
          </label>
          <input type="number" name="popular_shops_display_count" value="<?= $settings['popular_shops_display_count'] ?>" min="3" max="12" step="1">
          <p class="form-hint">Recommended: 6 shops</p>
        </div>

        <div class="form-group">
          <label>
            <i class="fas fa-percentage" style="color: #ec4899;"></i>
            Deals to Display
          </label>
          <input type="number" name="deals_display_count" value="<?= $settings['deals_display_count'] ?>" min="4" max="12" step="1">
          <p class="form-hint">Recommended: 4 deals</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 4: SEO Settings -->
  <div class="tab-content" id="tab-seo">
    <div class="settings-card">
      <h3>
        <i class="fas fa-search"></i>
        SEO & Meta Information
      </h3>
      <p style="color: var(--gray-600); margin-bottom: 24px; font-size: 14px;">
        Optimize your homepage for search engines
      </p>

      <div class="lang-tabs">
        <button type="button" class="lang-tab active" data-lang="en" data-section="seo">🇬🇧 English</button>
        <button type="button" class="lang-tab" data-lang="fr" data-section="seo">🇫🇷 Français</button>
      </div>

      <div class="lang-content active" id="seo-en">
        <div class="form-group">
          <label>Meta Title (English)</label>
          <input type="text" name="meta_title_en" value="<?= htmlspecialchars($settings['meta_title_en'] ?? '') ?>" placeholder="OCSAPP – Zero-Emission Grocery Delivery" maxlength="60">
          <p class="form-hint">Recommended: 50-60 characters</p>
        </div>

        <div class="form-group" style="margin-top: 16px;">
          <label>Meta Description (English)</label>
          <textarea name="meta_description_en" rows="3" maxlength="160" placeholder="Shop groceries, restaurants, stores & more on OCSAPP Marketplace"><?= htmlspecialchars($settings['meta_description_en'] ?? '') ?></textarea>
          <p class="form-hint">Recommended: 150-160 characters</p>
        </div>

        <div class="form-group" style="margin-top: 16px;">
          <label>Meta Keywords (English)</label>
          <input type="text" name="meta_keywords_en" value="<?= htmlspecialchars($settings['meta_keywords_en'] ?? '') ?>" placeholder="grocery delivery, online shopping, marketplace">
          <p class="form-hint">Comma-separated keywords (e.g., grocery, delivery, marketplace)</p>
        </div>
      </div>

      <div class="lang-content" id="seo-fr">
        <div class="form-group">
          <label>Méta Titre (Français)</label>
          <input type="text" name="meta_title_fr" value="<?= htmlspecialchars($settings['meta_title_fr'] ?? '') ?>" placeholder="OCSAPP – Livraison Zéro Émission" maxlength="60">
          <p class="form-hint">Recommandé: 50-60 caractères</p>
        </div>

        <div class="form-group" style="margin-top: 16px;">
          <label>Méta Description (Français)</label>
          <textarea name="meta_description_fr" rows="3" maxlength="160" placeholder="Achetez épicerie, restaurants, magasins et plus sur OCSAPP Marketplace"><?= htmlspecialchars($settings['meta_description_fr'] ?? '') ?></textarea>
          <p class="form-hint">Recommandé: 150-160 caractères</p>
        </div>

        <div class="form-group" style="margin-top: 16px;">
          <label>Mots-clés Méta (Français)</label>
          <input type="text" name="meta_keywords_fr" value="<?= htmlspecialchars($settings['meta_keywords_fr'] ?? '') ?>" placeholder="livraison épicerie, achats en ligne, marché">
          <p class="form-hint">Mots-clés séparés par des virgules</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="action-buttons">
    <button type="submit" class="btn-save">
      <i class="fas fa-save"></i>
      Save All Changes
    </button>

    <button type="button" class="btn-reset" onclick="confirmReset()">
      <i class="fas fa-undo"></i>
      Reset to Defaults
    </button>
  </div>
</form>

<!-- Reset Confirmation Form (Hidden) -->
<form method="POST" action="<?= url('admin/homepage/reset') ?>" id="resetForm" style="display: none;">
  <?= csrfField() ?>
</form>

<script>
// Tab Switching
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    // Remove active from all tabs and content
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    // Add active to clicked tab
    btn.classList.add('active');
    const tabId = 'tab-' + btn.dataset.tab;
    document.getElementById(tabId).classList.add('active');
  });
});

// Language Tab Switching
document.querySelectorAll('.lang-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    const section = btn.dataset.section;
    const lang = btn.dataset.lang;

    // Remove active from this section's tabs
    btn.parentElement.querySelectorAll('.lang-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Show correct language content
    const enContent = document.getElementById(section + '-en');
    const frContent = document.getElementById(section + '-fr');

    if (lang === 'en') {
      enContent?.classList.add('active');
      frContent?.classList.remove('active');
    } else {
      enContent?.classList.remove('active');
      frContent?.classList.add('active');
    }
  });
});

// Reset Confirmation
function confirmReset() {
  if (confirm('Are you sure you want to reset all homepage settings to defaults? This action cannot be undone.')) {
    document.getElementById('resetForm').submit();
  }
}

// Form Validation
document.getElementById('homepageForm').addEventListener('submit', (e) => {
  const counts = {
    categories: parseInt(document.querySelector('[name="categories_display_count"]').value),
    bestsellers: parseInt(document.querySelector('[name="best_sellers_display_count"]').value),
    shops: parseInt(document.querySelector('[name="popular_shops_display_count"]').value),
    deals: parseInt(document.querySelector('[name="deals_display_count"]').value)
  };

  // Validate counts
  if (counts.categories < 4 || counts.categories > 20) {
    alert('Categories display count must be between 4 and 20');
    e.preventDefault();
    return;
  }

  if (counts.bestsellers < 4 || counts.bestsellers > 20) {
    alert('Best sellers display count must be between 4 and 20');
    e.preventDefault();
    return;
  }

  if (counts.shops < 3 || counts.shops > 12) {
    alert('Popular shops display count must be between 3 and 12');
    e.preventDefault();
    return;
  }

  if (counts.deals < 4 || counts.deals > 12) {
    alert('Deals display count must be between 4 and 12');
    e.preventDefault();
    return;
  }
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
