<?php
use App\Helpers\VisitorTracker;
VisitorTracker::track();
/**
 * OCS Header Component - With Role-Based Account Routing
 * Last Updated: November 10, 2025
 */

// Get current language and location from session
$currentLang = $_SESSION['language'] ?? 'en';

// Store's physical location (admin-set, always displayed)
$storeLocation = getSetting('store_location', 'Kirkland, QC');

// User's delivery location (for delivery zone, user-selected)
$userDeliveryLocation = $_SESSION['location'] ?? 'Select your location';

$cartCount = $cartCount ?? 0;

// Get translations
$t = getTranslations($currentLang);

// FIXED: Get role-based account URL
$accountDashboardUrl = accountUrl(); // Uses new helper function
?>

<!-- Header -->
<header class="header">
    <div class="logo-section">
        <a href="<?= url('/') ?>" class="logo">
            <img src="<?= asset('images/logo.png') ?>" alt="OCS Logo" class="logo-img">
            <span>OCS</span>
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
        <!-- Language Selector -->
        <div class="language-selector">
            <button class="language-btn" 
                    id="languageBtn" 
                    type="button"
                    aria-label="Select language"
                    aria-expanded="false">
                <span><?= strtoupper($currentLang) ?></span>
                <span>▼</span>
            </button>
            <div class="language-dropdown" id="languageDropdown" role="menu">
                <div class="language-option <?= $currentLang === 'en' ? 'selected' : '' ?>" 
                     data-lang="en"
                     role="menuitem"
                     tabindex="0">
                    <span>🇺🇸</span>
                    <span>English</span>
                </div>
                <div class="language-option <?= $currentLang === 'fr' ? 'selected' : '' ?>" 
                     data-lang="fr"
                     role="menuitem"
                     tabindex="0">
                    <span>🇫🇷</span>
                    <span>Français</span>
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
            <a href="<?= $accountDashboardUrl ?>" class="sign-in-btn">
                <?php if (hasRole('seller')): ?>
                    <?= $t['seller_dashboard'] ?? 'Dashboard' ?>
                <?php elseif (hasRole('admin')): ?>
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

<!-- Location Popover -->
<div class="popover" id="locationPopover" style="display:none;" role="dialog" aria-labelledby="locationPopoverTitle">
    <h4 id="locationPopoverTitle"><?= $t['choose_location'] ?></h4>
    <div class="field">
        <input type="text" 
               placeholder="<?= $t['search_location'] ?>" 
               id="locSearchInput"
               aria-label="<?= $t['search_location'] ?>">
    </div>
    <div class="or-divider"><span><?= $t['or'] ?></span></div>
    <button type="button" class="detect-btn" id="detectBtn">
        📍 <?= $t['detect_location'] ?>
    </button>
    <div class="radius">
        <label for="radiusRange">
            <?= $t['delivery_radius'] ?>: <span id="radiusLabel">5</span> km
        </label>
        <input type="range" 
               id="radiusRange" 
               min="1" 
               max="20" 
               value="5"
               aria-label="<?= $t['delivery_radius'] ?>">
    </div>
</div>

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

<script>
/**
 * OCS Header JavaScript - With Geolocation Fix
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
    
    // LOCATION SELECTOR
    const locationBtn = document.getElementById('locationBtn');
    const locationPopover = document.getElementById('locationPopover');
    const detectBtn = document.getElementById('detectBtn');
    const radiusRange = document.getElementById('radiusRange');
    const radiusLabel = document.getElementById('radiusLabel');
    const locSearchInput = document.getElementById('locSearchInput');
    const currentLocationText = document.getElementById('currentLocationText');
    
    if (locationBtn && locationPopover) {
        locationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = locationPopover.style.display === 'block';
            locationPopover.style.display = isVisible ? 'none' : 'block';
            
            if (!isVisible) {
                locSearchInput?.focus();
            }
        });
    }
    
    if (radiusRange && radiusLabel) {
        radiusRange.addEventListener('input', function() {
            radiusLabel.textContent = this.value;
        });
    }
    
    if (detectBtn) {
        detectBtn.addEventListener('click', async function() {
            console.log('🖱️ Detect Location clicked');
            
            if (!navigator.geolocation) {
                alert(t.geoNotSupported || 'Geolocation is not supported');
                return;
            }

            const originalText = detectBtn.textContent;
            detectBtn.disabled = true;
            detectBtn.textContent = '📍 ' + (t.detecting || 'Detecting...');

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    try {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        
                        console.log('✅ Got coordinates:', lat, lon);
                        
                        // Get address from coordinates
                        let locationData;
                        try {
                            locationData = await reverseGeocode(lat, lon);
                            console.log('✅ Reverse geocoded:', locationData);
                        } catch (geoError) {
                            console.warn('Reverse geocoding failed, using coordinates:', geoError);
                            locationData = {
                                name: `${lat.toFixed(4)}, ${lon.toFixed(4)}`,
                                latitude: lat,
                                longitude: lon
                            };
                        }
                        
                        // Save to backend
                        console.log('💾 Saving location:', locationData.name);
                        
                        const response = await postJSON(config.urls.setLocation, {
                            location: locationData.name,
                            latitude: lat,
                            longitude: lon,
                            radius: radiusRange?.value || 5,
                            city: locationData.city,
                            country: locationData.country
                        });
                        
                        console.log('📨 Save response:', response);
                        
                        // FIXED: Check response.success properly
                        if (response && response.success) {
                            // Update UI immediately
                            if (currentLocationText) {
                                currentLocationText.textContent = locationData.name;
                            }
                            
                            // Close popover
                            locationPopover.style.display = 'none';
                            
                            // Show success briefly
                            detectBtn.textContent = '✓ ' + locationData.name;
                            detectBtn.style.background = '#4CAF50';
                            
                            // Reload page after short delay to update products
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(response?.message || response?.error || 'Save failed');
                        }
                        
                    } catch (error) {
                        console.error('❌ Location save error:', error);
                        alert('Could not save your location: ' + error.message);
                        detectBtn.disabled = false;
                        detectBtn.textContent = originalText;
                    }
                },
                (error) => {
                    console.error('❌ Geolocation error:', error);
                    let errorMessage = 'Could not detect your location';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = config.currentLang === 'fr' 
                                ? "Permission de localisation refusée" 
                                : "Location permission denied. Please allow location access.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = config.currentLang === 'fr'
                                ? "Position indisponible"
                                : "Location information unavailable";
                            break;
                        case error.TIMEOUT:
                            errorMessage = config.currentLang === 'fr'
                                ? "Délai d'attente dépassé"
                                : "Location request timed out";
                            break;
                    }
                    
                    alert(errorMessage);
                    detectBtn.disabled = false;
                    detectBtn.textContent = originalText;
                }
            );
        });
    }

    if (locSearchInput) {
        locSearchInput.addEventListener('keydown', async function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const location = this.value.trim();
                
                if (!location) {
                    alert('Please enter a location');
                    return;
                }

                console.log('📝 Manual location entry:', location);

                try {
                    const response = await postJSON(config.urls.setLocation, {
                        location: location,
                        radius: radiusRange?.value || 5
                    });
                    
                    console.log('📨 Save response:', response);
                    
                    // FIXED: Check response.success properly
                    if (response && response.success) {
                        // Update UI
                        if (currentLocationText) {
                            currentLocationText.textContent = location;
                        }
                        
                        // Close popover
                        locationPopover.style.display = 'none';
                        
                        // Reload to update products
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        throw new Error(response?.message || response?.error || 'Save failed');
                    }
                    
                } catch (error) {
                    console.error('❌ Location update error:', error);
                    alert('Could not save your location: ' + error.message);
                }
            }
        });
    }
    
    document.addEventListener('click', function(e) {
        if (locationPopover && 
            !e.target.closest('.location-selector') && 
            !e.target.closest('#locationPopover')) {
            locationPopover.style.display = 'none';
        }
    });
    
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