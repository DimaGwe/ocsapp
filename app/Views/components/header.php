<?php
use App\Helpers\VisitorTracker;
VisitorTracker::track();
/**
 * OCSAPP Header Component - With Role-Based Account Routing
 * Last Updated: November 10, 2025
 */

// Get current language and location from session
$currentLang = $_SESSION['language'] ?? 'fr';

// Store's physical location (fixed, displayed in top banner)
$storeLocation = 'Kirkland, QC';

// Get translations first (needed for default location text)
$t = getTranslations($currentLang);

// User's delivery location (for delivery zone, user-selected)
$defaultLocationText = $t['select_location'] ?? ($currentLang === 'fr' ? 'Choisir votre emplacement' : 'Select your location');
$userDeliveryLocation = $_SESSION['location'] ?? $defaultLocationText;

$cartCount = $cartCount ?? 0;

// FIXED: Get role-based account URL
$accountDashboardUrl = accountUrl(); // Uses new helper function
?>

<!-- Skip navigation link (WCAG 2.1 SC 2.4.1) -->
<style>
.skip-link { position: absolute; left: -9999px; top: auto; width: 1px; height: 1px; overflow: hidden; }
.skip-link:focus { position: fixed; top: 12px; left: 12px; z-index: 99999; width: auto; height: auto; padding: 10px 18px; background: #00b207; color: #fff; font-size: 14px; font-weight: 600; border-radius: 6px; text-decoration: none; outline: 3px solid #fff; }
</style>
<a href="#main-content" class="skip-link">
    <?= ($currentLang === 'fr') ? 'Aller au contenu principal' : 'Skip to main content' ?>
</a>

<!-- Beta Notice (Modal + Banner) -->
<?php include __DIR__ . '/beta-notice.php'; ?>

<!-- Top Banner -->
<div class="top-banner">
    <?= $t['store_location'] ?>: <?= htmlspecialchars($storeLocation) ?> |
    <?= $t['need_help'] ?>: <a href="tel:+15147463789">+1 (514) 746-3789</a>
</div>

<!-- Header -->
<header class="header">
    <div class="logo-section">
        <a href="<?= url('home') ?>" class="logo">
            <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP Logo" class="logo-img">
            <span>OCSAPP</span>
        </a>
        <button class="location-selector" id="locationBtn" type="button" aria-label="<?= $t['choose_location'] ?>">
            <span>📍</span>
            <span id="currentLocationText"><?= htmlspecialchars($userDeliveryLocation) ?></span>
            <span>▼</span>
        </button>
    </div>

    <form class="search-bar" action="<?= url('search') ?>" method="GET" role="search">
        <input type="search" 
               name="q" 
               placeholder="<?= $t['search_placeholder'] ?>"
               aria-label="<?= $t['search_placeholder'] ?>">
        <button type="submit"><?= $t['search_btn'] ?></button>
    </form>

    <div class="header-actions">
        <!-- Language Selector - notranslate prevents browser auto-translate issues -->
        <div class="language-selector notranslate" translate="no">
            <button class="language-btn"
                    id="languageBtn"
                    type="button"
                    aria-label="Select language"
                    aria-expanded="false">
                <span class="notranslate" translate="no"><?= strtoupper($currentLang) ?></span>
                <span>▼</span>
            </button>
            <div class="language-dropdown" id="languageDropdown" role="menu">
                <div class="language-option <?= $currentLang === 'en' ? 'selected' : '' ?>"
                     data-lang="en"
                     role="menuitem"
                     tabindex="0">
                    <span>🇺🇸</span>
                    <span class="notranslate" translate="no">English</span>
                </div>
                <div class="language-option <?= $currentLang === 'fr' ? 'selected' : '' ?>"
                     data-lang="fr"
                     role="menuitem"
                     tabindex="0">
                    <span>🇫🇷</span>
                    <span class="notranslate" translate="no">Français</span>
                </div>
            </div>
        </div>

        <!-- Cart Button (Desktop Only) -->
        <a href="<?= url('cart') ?>" class="btn-cart desktop-only" aria-label="<?= $t['cart'] ?>">
            <span class="cart-icon">🛒</span>
            <span class="cart-count" id="cartCount"><?= $cartCount ?></span>
        </a>

        <!-- Sign In / Account - FIXED: Now role-aware -->
        <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
            <?php
            $userRole = $_SESSION['user']['role'] ?? 'buyer';
            $isAdminTier = in_array($userRole, ['super_admin', 'admin', 'admin_staff']);
            ?>
            <a href="<?= $accountDashboardUrl ?>" class="sign-in-btn">
                <?php if (hasRole('seller')): ?>
                    <?= $t['seller_dashboard'] ?? 'Dashboard' ?>
                <?php elseif ($isAdminTier): ?>
                    <?= $t['admin_panel'] ?? 'Admin' ?>
                <?php else: ?>
                    <?= $t['account'] ?>
                <?php endif; ?>
            </a>
        <?php else: ?>
            <a href="<?= url('login') ?>" class="sign-in-btn"><?= $t['sign_in'] ?></a>
        <?php endif; ?>
    </div>
</header>

<!-- Location Autocomplete Styles -->
<style>
.location-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    max-height: 250px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 4px;
}
.suggestion-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.15s;
}
.suggestion-item:last-child { border-bottom: none; }
.suggestion-item:hover { background: #f0fdf4; }
.suggestion-icon { margin-right: 10px; font-size: 16px; }
.suggestion-text {
    font-size: 13px;
    color: #374151;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

<!-- Modern Location Modal -->
<div class="location-modal-overlay" id="locationModalOverlay">
    <div class="location-modal" id="locationModal">
        <!-- Modal Header -->
        <div class="location-modal-header">
            <div class="location-modal-title">
                <i class="fas fa-location-dot"></i>
                <span><?= $t['choose_location'] ?? 'Choose Your Location' ?></span>
            </div>
            <button type="button" class="location-modal-close" id="locationModalClose" aria-label="Close">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="location-modal-body">
            <!-- Search Input -->
            <div class="location-search-wrapper">
                <i class="fas fa-magnifying-glass location-search-icon"></i>
                <input
                    type="text"
                    class="location-search-input"
                    id="locationSearchInput"
                    placeholder="<?= $t['location_placeholder'] ?? 'Enter street, city, or postal code' ?>"
                    autocomplete="off"
                    aria-label="<?= $t['search_location'] ?? 'Search for your address' ?>">

                <!-- Autocomplete Suggestions -->
                <div class="location-suggestions" id="locationSuggestions"></div>
            </div>

            <!-- Use Current Location Button -->
            <button type="button" class="use-location-btn" id="useCurrentLocationBtn">
                <i class="fas fa-location-crosshairs"></i>
                <span><?= $t['use_current_location'] ?? 'Use Current Location' ?></span>
            </button>

            <!-- Divider -->
            <div class="location-divider">
                <span><?= $t['recent_locations'] ?? 'Recent Locations' ?></span>
            </div>

            <!-- Recent Locations -->
            <div class="recent-locations-section">
                <div id="recentLocationsContainer">
                    <!-- Dynamically populated -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Location Modal CSS & JS -->
<link rel="stylesheet" href="<?= asset('css/components/location-modal.css') ?>">
<script src="<?= asset('js/location-modal.js') ?>"></script>

<!-- Load Beta Notice CSS -->
<link rel="stylesheet" href="<?= asset('css/beta-notice.css') ?>">

<!-- Mobile Bottom Navigation - FIXED: Now role-aware -->
<nav class="mobile-bottom-nav" aria-label="Mobile navigation">
    <a href="<?= url('/') ?>" class="nav-item" aria-label="<?= $t['home'] ?>">
        <div class="nav-icon">🏠</div>
        <div class="nav-label"><?= $t['home'] ?></div>
    </a>
    <a href="<?= url('categories') ?>" class="nav-item" aria-label="<?= $t['categories'] ?>">
        <div class="nav-icon">☰</div>
        <div class="nav-label"><?= $t['categories'] ?></div>
    </a>
    <a href="<?= url('shops') ?>" class="nav-item" aria-label="<?= $t['shops'] ?>">
        <div class="nav-icon">🏪</div>
        <div class="nav-label"><?= $t['shops'] ?></div>
    </a>
    
    <!-- FIXED: Account link now role-aware -->
    <a href="<?= isLoggedIn() ? $accountDashboardUrl : url('login') ?>" 
       class="nav-item"
       aria-label="<?= isLoggedIn() ? $t['account'] : $t['sign_in'] ?>">
        <div class="nav-icon">👤</div>
        <div class="nav-label">
            <?php if (isLoggedIn()): ?>
                <?php if (hasRole('seller')): ?>
                    <?= $t['dashboard'] ?? 'Dashboard' ?>
                <?php elseif (hasRole('admin')): ?>
                    <?= $t['admin'] ?? 'Admin' ?>
                <?php else: ?>
                    <?= $t['account'] ?>
                <?php endif; ?>
            <?php else: ?>
                <?= $t['sign_in'] ?>
            <?php endif; ?>
        </div>
    </a>
    
    <a href="<?= url('cart') ?>" class="nav-item cart-nav-item" aria-label="<?= $t['cart'] ?>">
        <div class="nav-icon">
            🛒
            <span class="mobile-cart-badge" id="mobileCartCount"><?= $cartCount ?></span>
        </div>
        <div class="nav-label"><?= $t['cart'] ?></div>
    </a>
</nav>
<div id="main-content" tabindex="-1"></div>

<script>
/**
 * OCSAPP Header JavaScript - With Geolocation Fix
 */
(function() {
    'use strict';

    const config = {
        currentLang: '<?= $currentLang ?>',
        urls: {
            setLanguage: '<?= url("set-language") ?>',
            setLocation: '<?= url("set-location") ?>',
            cartCount: '<?= url("cart/count") ?>'
        },
        text: {
            fr: {
                geoNotSupported: 'La géolocalisation n\'est pas supportée',
                detecting: 'Détection...',
                locationError: 'Erreur lors de la définition de l\'emplacement',
                geoError: 'Impossible de détecter votre position',
                langError: 'Erreur de changement de langue',
                reverseGeoError: 'Impossible d\'obtenir l\'adresse'
            },
            en: {
                geoNotSupported: 'Geolocation is not supported',
                detecting: 'Detecting...',
                locationError: 'Error setting location',
                geoError: 'Unable to detect your location',
                langError: 'Failed to change language',
                reverseGeoError: 'Unable to get address'
            }
        }
    };

    const t = config.text[config.currentLang];

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    async function postJSON(url, data) {
        const csrfToken = getCsrfToken();

        // Include CSRF token in data payload
        const dataWithToken = {
            ...data,
            _csrf_token: csrfToken
        };

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(dataWithToken)
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return response.json();
    }

    /**
     * Reverse Geocode: Convert coordinates to address
     * Uses Nominatim (OpenStreetMap) API - Free, no API key needed
     */
    async function reverseGeocode(latitude, longitude) {
        try {
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`;
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'User-Agent': 'OCSAPP/1.0'
                }
            });

            if (!response.ok) {
                throw new Error('Geocoding failed');
            }

            const data = await response.json();
            
            // Extract meaningful location name
            const address = data.address || {};
            
            // Priority: neighborhood > suburb > city > county > state
            const locationName = 
                address.neighbourhood || 
                address.suburb || 
                address.city || 
                address.town || 
                address.village || 
                address.municipality ||
                address.county || 
                address.state ||
                'Unknown Location';
            
            return {
                name: locationName,
                fullAddress: data.display_name,
                city: address.city || address.town || address.village,
                country: address.country,
                latitude: latitude,
                longitude: longitude
            };
        } catch (error) {
            console.error('Reverse geocoding error:', error);
            throw error;
        }
    }

    // LANGUAGE SELECTOR
    const langBtn = document.getElementById('languageBtn');
    const langDropdown = document.getElementById('languageDropdown');
    const languageOptions = document.querySelectorAll('.language-option');
    
    if (langBtn && langDropdown) {
        langBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = langDropdown.classList.toggle('active');
            langBtn.setAttribute('aria-expanded', isActive);
        });
        
        languageOptions.forEach(option => {
            option.addEventListener('click', async function() {
                const newLang = this.dataset.lang;
                
                if (newLang === config.currentLang) {
                    langDropdown.classList.remove('active');
                    langBtn.setAttribute('aria-expanded', 'false');
                    return;
                }
                
                try {
                    this.style.opacity = '0.5';
                    this.style.pointerEvents = 'none';
                    
                    let response;
                    try {
                        response = await postJSON(config.urls.setLanguage, { language: newLang });
                    } catch (jsonError) {
                        const csrfToken = getCsrfToken();
                        const formData = new URLSearchParams({
                            language: newLang,
                            _csrf_token: csrfToken
                        });
                        
                        const fallbackResponse = await fetch(config.urls.setLanguage, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData.toString()
                        });
                        
                        if (!fallbackResponse.ok) {
                            throw new Error('Both request methods failed');
                        }
                        
                        response = await fallbackResponse.json();
                    }
                    
                    if (response && response.success !== false) {
                        window.location.reload();
                    } else {
                        throw new Error(response.message || 'Language change failed');
                    }
                } catch (error) {
                    console.error('Language switch error:', error);
                    alert(t.langError);
                    this.style.opacity = '1';
                    this.style.pointerEvents = 'auto';
                }
            });
        });
        
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.language-selector')) {
                langDropdown.classList.remove('active');
                langBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }
    
    // LOCATION SELECTOR - Handled by location-modal.js
    
    // CART COUNT
    async function loadCartCount() {
        try {
            const response = await fetch(config.urls.cartCount);
            if (!response.ok) throw new Error('Failed to fetch cart count');
            
            const data = await response.json();
            
            if (data.count > 0) {
                updateCartDisplay(data.count);
            }
        } catch (error) {
            console.log('Cart count not loaded:', error.message);
        }
    }

    function updateCartDisplay(count) {
        const desktopCart = document.getElementById('cartCount');
        if (desktopCart) {
            desktopCart.textContent = count;
            desktopCart.style.display = 'flex';
        }
        
        const mobileBadge = document.getElementById('mobileCartCount');
        if (mobileBadge) {
            mobileBadge.textContent = count;
            mobileBadge.style.display = 'block';
        }
    }

    loadCartCount();
    window.updateCartDisplay = updateCartDisplay;

})();
</script>

<!-- Cookie Consent Banner -->
<?php if (empty($_COOKIE['cookie_consent'])): ?>
<?php
$_cookieFr = ($currentLang === 'fr');
$_cookieText   = $t['cookie_banner_prefix']    ?? ($_cookieFr ? 'Nous utilisons des témoins pour améliorer votre expérience et assurer le bon fonctionnement du site. En continuant à utiliser OCSAPP Marketplace, vous acceptez notre' : 'We use cookies to improve your experience and ensure the site works properly. By continuing to use OCSAPP Marketplace, you agree to our');
$_cookieLink   = $t['cookie_policy_link_text'] ?? ($_cookieFr ? 'Politique des témoins' : 'Cookie Policy');
$_cookieAccept = $t['cookie_accept']           ?? ($_cookieFr ? 'Accepter' : 'Accept');
$_cookieDecline= $t['cookie_decline']          ?? ($_cookieFr ? 'Refuser'  : 'Decline');
?>
<div id="cookieBanner" style="position:fixed;bottom:0;left:0;right:0;z-index:9999;background:#1a1a1a;color:#fff;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;box-shadow:0 -4px 16px rgba(0,0,0,.3);">
    <p style="margin:0;font-size:14px;line-height:1.5;flex:1;min-width:200px;">
        <?= $_cookieText ?> <a href="<?= url('cookies') ?>" style="color:#00b207;"><?= $_cookieLink ?></a>.
    </p>
    <div style="display:flex;gap:10px;flex-shrink:0;">
        <button onclick="acceptCookies()" style="padding:10px 24px;background:#00b207;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;"><?= $_cookieAccept ?></button>
        <button onclick="declineCookies()" style="padding:10px 16px;background:transparent;color:#aaa;border:1px solid #555;border-radius:8px;font-size:14px;cursor:pointer;"><?= $_cookieDecline ?></button>
    </div>
</div>
<script>
function acceptCookies() {
    document.cookie = "cookie_consent=accepted; max-age=" + (365*24*3600) + "; path=/; SameSite=Lax";
    document.getElementById('cookieBanner').style.display = 'none';
}
function declineCookies() {
    document.cookie = "cookie_consent=declined; max-age=" + (365*24*3600) + "; path=/; SameSite=Lax";
    document.getElementById('cookieBanner').style.display = 'none';
}
</script>
<?php endif; ?>