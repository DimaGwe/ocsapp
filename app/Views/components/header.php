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

<!-- Header -->
<header class="header">
    <div class="logo-section">
        <a href="<?= url('/') ?>" class="logo">
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

<!-- Location Popover -->
<div class="popover" id="locationPopover" style="display:none;" role="dialog" aria-labelledby="locationPopoverTitle">
    <h4 id="locationPopoverTitle"><?= $t['choose_location'] ?></h4>
    <div class="field" style="position: relative;">
        <input type="text"
               placeholder="<?= $t['search_location'] ?>"
               id="locSearchInput"
               autocomplete="off"
               aria-label="<?= $t['search_location'] ?>">
        <!-- Autocomplete suggestions -->
        <div id="locationSuggestions" class="location-suggestions" style="display: none;"></div>
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
                            
                            // Show success message
                            detectBtn.textContent = '✓ ' + locationData.name;
                            detectBtn.style.background = '#4CAF50';

                            // Re-enable button after showing success
                            setTimeout(() => {
                                detectBtn.disabled = false;
                                detectBtn.textContent = originalText;
                                detectBtn.style.background = '';
                            }, 2000);
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

    // Location autocomplete
    const locationSuggestions = document.getElementById('locationSuggestions');
    let autocompleteTimeout = null;

    async function searchLocations(query) {
        if (query.length < 3) {
            locationSuggestions.style.display = 'none';
            return;
        }

        try {
            const url = `https://nominatim.openstreetmap.org/search?` + new URLSearchParams({
                q: query,
                format: 'json',
                limit: 5,
                addressdetails: 1,
                countrycodes: 'ca', // Canada only
                viewbox: '-74.5,45.0,-73.0,46.0', // Montreal/Kirkland area bias
                bounded: 0 // Allow results outside viewbox but prioritize inside
            });

            const response = await fetch(url, {
                headers: { 'User-Agent': 'OCSMarketplace/1.0' }
            });

            if (!response.ok) throw new Error('Search failed');

            const results = await response.json();

            if (results.length > 0) {
                locationSuggestions.innerHTML = results.map(r => `
                    <div class="suggestion-item"
                         data-lat="${r.lat}"
                         data-lon="${r.lon}"
                         data-name="${r.address?.city || r.address?.town || r.address?.municipality || r.display_name.split(',')[0]}">
                        <span class="suggestion-icon">📍</span>
                        <span class="suggestion-text">${r.display_name}</span>
                    </div>
                `).join('');
                locationSuggestions.style.display = 'block';
            } else {
                locationSuggestions.style.display = 'none';
            }
        } catch (error) {
            console.error('Autocomplete error:', error);
            locationSuggestions.style.display = 'none';
        }
    }

    async function selectLocation(lat, lon, name) {
        try {
            const response = await postJSON(config.urls.setLocation, {
                location: name,
                latitude: parseFloat(lat),
                longitude: parseFloat(lon),
                radius: radiusRange?.value || 5
            });

            if (response && response.success) {
                if (currentLocationText) {
                    currentLocationText.textContent = name;
                }
                locationPopover.style.display = 'none';
                locationSuggestions.style.display = 'none';
                window.location.reload();
            }
        } catch (error) {
            console.error('Location save error:', error);
            alert('Could not save location');
        }
    }

    if (locSearchInput && locationSuggestions) {
        // Autocomplete on input
        locSearchInput.addEventListener('input', function() {
            clearTimeout(autocompleteTimeout);
            autocompleteTimeout = setTimeout(() => {
                searchLocations(this.value.trim());
            }, 300);
        });

        // Handle suggestion clicks
        locationSuggestions.addEventListener('click', function(e) {
            const item = e.target.closest('.suggestion-item');
            if (item) {
                const lat = item.dataset.lat;
                const lon = item.dataset.lon;
                const name = item.dataset.name;
                locSearchInput.value = name;
                selectLocation(lat, lon, name);
            }
        });

        // Handle Enter key for manual entry
        locSearchInput.addEventListener('keydown', async function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const location = this.value.trim();

                if (!location) {
                    alert('Please enter a location');
                    return;
                }

                // If suggestions are visible, select first one
                const firstSuggestion = locationSuggestions.querySelector('.suggestion-item');
                if (firstSuggestion && locationSuggestions.style.display !== 'none') {
                    firstSuggestion.click();
                    return;
                }

                // Otherwise save manually
                try {
                    const response = await postJSON(config.urls.setLocation, {
                        location: location,
                        radius: radiusRange?.value || 5
                    });

                    if (response && response.success) {
                        if (currentLocationText) {
                            currentLocationText.textContent = location;
                        }
                        locationPopover.style.display = 'none';
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Location update error:', error);
                    alert('Could not save your location');
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