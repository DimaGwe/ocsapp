/**
 * Modern Location Selector Modal
 * With autocomplete, GPS detection, and recent locations
 */

(function() {
  'use strict';

  // Configuration
  const RECENT_LOCATIONS_KEY = 'ocs_recent_locations';
  const CURRENT_LOCATION_KEY = 'ocs_current_location';
  const MAX_RECENT_LOCATIONS = 5;

  // Get base path - works for both local (/ocsapp/public) and production (/)
  const getBasePath = () => {
    const basePath = document.querySelector('meta[name="base-path"]')?.content;
    if (basePath) return basePath;

    // Fallback: detect from current path
    const path = window.location.pathname;
    if (path.includes('/ocsapp/public')) return '/ocsapp/public';
    return '';
  };

  // Elements
  let modalOverlay, modal, searchInput, suggestionsContainer;
  let currentLocationBtn, recentLocationsContainer;

  // Initialize when DOM is ready
  document.addEventListener('DOMContentLoaded', init);

  function init() {
    // Get elements
    modalOverlay = document.getElementById('locationModalOverlay');
    modal = document.getElementById('locationModal');
    searchInput = document.getElementById('locationSearchInput');
    suggestionsContainer = document.getElementById('locationSuggestions');
    currentLocationBtn = document.getElementById('useCurrentLocationBtn');
    recentLocationsContainer = document.getElementById('recentLocationsContainer');

    if (!modalOverlay || !modal || !searchInput) {
      console.warn('Location modal elements not found');
      return;
    }

    // Event listeners
    setupEventListeners();

    // Load recent locations
    loadRecentLocations();

    // Load saved location from localStorage (persistent across pages)
    loadSavedLocation();
  }

  function setupEventListeners() {
    // Open modal when clicking location selector
    const locationBtn = document.getElementById('locationBtn');
    if (locationBtn) {
      locationBtn.addEventListener('click', openModal);
    }

    // Close modal
    const closeBtn = document.getElementById('locationModalClose');
    if (closeBtn) {
      closeBtn.addEventListener('click', closeModal);
    }

    // Click outside to close
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) {
        closeModal();
      }
    });

    // Escape key to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
        closeModal();
      }
    });

    // Search input - autocomplete
    searchInput.addEventListener('input', handleSearch);
    searchInput.addEventListener('focus', () => {
      if (suggestionsContainer.children.length > 0) {
        suggestionsContainer.classList.add('active');
      }
    });

    // Use current location
    if (currentLocationBtn) {
      currentLocationBtn.addEventListener('click', useCurrentLocation);
    }
  }

  function openModal() {
    modalOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Focus search input after animation
    setTimeout(() => {
      searchInput.focus();
    }, 300);
  }

  function closeModal() {
    modalOverlay.classList.remove('active');
    document.body.style.overflow = '';
    searchInput.value = '';
    suggestionsContainer.classList.remove('active');
    suggestionsContainer.innerHTML = '';
  }

  // Search handler with debounce
  let searchTimeout;
  function handleSearch(e) {
    const query = e.target.value.trim();

    clearTimeout(searchTimeout);

    if (query.length < 2) {
      suggestionsContainer.classList.remove('active');
      suggestionsContainer.innerHTML = '';
      return;
    }

    // Show loading
    suggestionsContainer.innerHTML = '<div style="padding: 16px; text-align: center; color: #9ca3af;">Searching...</div>';
    suggestionsContainer.classList.add('active');

    // Debounce search
    searchTimeout = setTimeout(() => {
      performSearch(query);
    }, 300);
  }

  async function performSearch(query) {
    try {
      // Use server-side proxy to avoid CORS issues
      const basePath = getBasePath();
      const response = await fetch(`${basePath}/api/location/search`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          query: query
        })
      });

      if (!response.ok) throw new Error('Geocoding request failed');

      const data = await response.json();

      if (!data.success || !data.results) {
        throw new Error('Invalid response format');
      }

      // Data is already formatted by the server
      displaySuggestions(data.results);
    } catch (error) {
      console.error('Search error:', error);
      suggestionsContainer.innerHTML = '<div style="padding: 16px; text-align: center; color: #ef4444;">Error loading suggestions. Please try again.</div>';
    }
  }

  function displaySuggestions(suggestions) {
    if (suggestions.length === 0) {
      suggestionsContainer.innerHTML = '<div style="padding: 16px; text-align: center; color: #9ca3af;">No locations found</div>';
      return;
    }

    suggestionsContainer.innerHTML = suggestions.map(suggestion => `
      <div class="location-suggestion-item" data-name="${suggestion.name}" data-address="${suggestion.address}" data-lat="${suggestion.lat ?? ''}" data-lon="${suggestion.lon ?? ''}">
        <i class="fas fa-map-marker-alt"></i>
        <div class="location-suggestion-content">
          <div class="location-suggestion-main">${suggestion.name}</div>
          <div class="location-suggestion-sub">${suggestion.address}</div>
        </div>
      </div>
    `).join('');

    // Add click listeners to suggestions
    suggestionsContainer.querySelectorAll('.location-suggestion-item').forEach(item => {
      item.addEventListener('click', () => {
        const name = item.dataset.name;
        const address = item.dataset.address;
        const lat = item.dataset.lat ? parseFloat(item.dataset.lat) : null;
        const lon = item.dataset.lon ? parseFloat(item.dataset.lon) : null;
        selectLocation(name, address, lat, lon);
      });
    });
  }

  async function selectLocation(name, address, lat, lon) {
    // Save to recent locations / localStorage (persistent across pages)
    saveToRecentLocations(name, address, lat, lon);
    saveCurrentLocation(name, address, lat, lon);

    // Update UI
    const currentLocationText = document.getElementById('currentLocationText');
    if (currentLocationText) {
      currentLocationText.textContent = name;
    }

    // Close modal
    closeModal();

    // Update session, then reload so server-rendered results (e.g. the
    // radius-filtered /shops list) reflect the new location. If the save
    // itself fails (network blip, expired session, etc.), tell the user
    // instead of silently leaving the page unfiltered while the UI implies
    // the location was set - this is the one path every entry point
    // (search, recent locations, current-location) funnels through.
    const updated = await updateLocation(name, lat, lon);
    if (updated) {
      // Briefly show exactly what was captured before reloading - an instant
      // reload made it impossible to tell whether it actually worked (this
      // is what prompted the confirmation: a fast refresh looked like nothing
      // happened even though the save succeeded).
      if (currentLocationText && address) {
        currentLocationText.textContent = '';
        const icon = document.createElement('i');
        icon.className = 'fas fa-check';
        icon.style.color = '#22c55e';
        currentLocationText.appendChild(icon);
        currentLocationText.appendChild(document.createTextNode(' ' + address));
      }
      setTimeout(() => window.location.reload(), 1200);
    } else {
      alert('We detected your location but could not save it to your session. Please try again.');
    }
    return updated;
  }

  function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
  }

  async function updateLocation(location, lat, lon) {
    try {
      const basePath = getBasePath();
      const csrfToken = getCsrfToken();
      const payload = { location, _csrf_token: csrfToken };
      if (typeof lat === 'number' && typeof lon === 'number' && !isNaN(lat) && !isNaN(lon)) {
        payload.latitude = lat;
        payload.longitude = lon;
        payload.radius = 20;
      }

      const response = await fetch(`${basePath}/set-location`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });

      if (response.ok) {
        console.log('Location updated:', location);
        return true;
      }
      console.error('Error updating location: server returned', response.status);
      return false;
    } catch (error) {
      console.error('Error updating location:', error);
      return false;
    }
  }

  function useCurrentLocation() {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser');
      return;
    }

    // Show loading
    currentLocationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
    currentLocationBtn.disabled = true;

    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const { latitude, longitude } = position.coords;
        console.log('✅ Got coordinates:', latitude, longitude);

        try {
          // Reverse geocode using our server-side proxy (avoids CORS)
          const basePath = getBasePath();
          const response = await fetch(`${basePath}/api/location/reverse-geocode`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              lat: latitude,
              lon: longitude
            })
          });

          if (!response.ok) throw new Error('Reverse geocoding failed');

          const data = await response.json();
          console.log('📍 Geocoded:', data);

          // Get location name + physical address from server response
          const locationName = data.location || 'Your Location';
          const streetAddress = data.street_address || `${latitude}, ${longitude}`;

          // Select location - this reloads the page on success, or alerts
          // and returns false if the session save itself failed
          const updated = await selectLocation(locationName, streetAddress, latitude, longitude);

          if (updated) {
            // Page is reloading; button feedback below won't be seen, but
            // harmless to set anyway in case the reload is slow.
            currentLocationBtn.innerHTML = '<i class="fas fa-check"></i> Location Set!';
            currentLocationBtn.style.background = 'linear-gradient(135deg, #4CAF50 0%, #45a049 100%)';
          } else {
            currentLocationBtn.innerHTML = '<i class="fas fa-location-crosshairs"></i> Use Current Location';
            currentLocationBtn.disabled = false;
          }

          setTimeout(() => {
            currentLocationBtn.innerHTML = '<i class="fas fa-location-crosshairs"></i> Use Current Location';
            currentLocationBtn.style.background = '';
            currentLocationBtn.disabled = false;
          }, 2000);

        } catch (error) {
          console.error('Geocoding error:', error);
          alert('Unable to save your location. Please try again.');
          currentLocationBtn.innerHTML = '<i class="fas fa-location-crosshairs"></i> Use Current Location';
          currentLocationBtn.disabled = false;
        }
      },
      (error) => {
        console.error('Geolocation error:', error);
        let errorMessage = 'Unable to access your location.';

        switch(error.code) {
          case error.PERMISSION_DENIED:
            errorMessage = 'Location permission denied. Please allow location access in your browser settings.';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage = 'Location information unavailable. Please try again.';
            break;
          case error.TIMEOUT:
            errorMessage = 'Location request timed out. Please try again.';
            break;
        }

        alert(errorMessage);
        currentLocationBtn.innerHTML = '<i class="fas fa-location-crosshairs"></i> Use Current Location';
        currentLocationBtn.disabled = false;
      },
      {
        enableHighAccuracy: true, // Use GPS when available - accuracy matters for delivery
        timeout: 10000, // GPS lock can take longer than a network-position fix
        maximumAge: 30000 // Allow cached position up to 30 seconds old
      }
    );
  }

  function saveToRecentLocations(name, address, lat, lon) {
    let recent = getRecentLocations();

    // Remove if already exists
    recent = recent.filter(loc => loc.name !== name);

    // Add to beginning
    recent.unshift({ name, address, lat: lat ?? null, lon: lon ?? null, timestamp: Date.now() });

    // Keep only max recent
    recent = recent.slice(0, MAX_RECENT_LOCATIONS);

    // Save to localStorage
    localStorage.setItem(RECENT_LOCATIONS_KEY, JSON.stringify(recent));

    // Reload display
    loadRecentLocations();
  }

  function getRecentLocations() {
    try {
      const stored = localStorage.getItem(RECENT_LOCATIONS_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (error) {
      console.error('Error loading recent locations:', error);
      return [];
    }
  }

  function loadRecentLocations() {
    if (!recentLocationsContainer) return;

    const recent = getRecentLocations();

    if (recent.length === 0) {
      recentLocationsContainer.innerHTML = '<div style="padding: 16px; text-align: center; color: #9ca3af; font-size: 14px;">No recent locations</div>';
      return;
    }

    recentLocationsContainer.innerHTML = recent.map(loc => `
      <div class="recent-location-item" data-name="${loc.name}" data-address="${loc.address}" data-lat="${loc.lat ?? ''}" data-lon="${loc.lon ?? ''}">
        <i class="fas fa-clock-rotate-left"></i>
        <div class="recent-location-content">
          <div class="recent-location-name">${loc.name}</div>
          <div class="recent-location-address">${loc.address}</div>
        </div>
      </div>
    `).join('');

    // Add click listeners
    recentLocationsContainer.querySelectorAll('.recent-location-item').forEach(item => {
      item.addEventListener('click', () => {
        const name = item.dataset.name;
        const address = item.dataset.address;
        const lat = item.dataset.lat ? parseFloat(item.dataset.lat) : null;
        const lon = item.dataset.lon ? parseFloat(item.dataset.lon) : null;
        selectLocation(name, address, lat, lon);
      });
    });
  }

  // Current location persistence functions
  function saveCurrentLocation(name, address, lat, lon) {
    try {
      localStorage.setItem(CURRENT_LOCATION_KEY, JSON.stringify({
        name,
        address,
        lat: lat ?? null,
        lon: lon ?? null,
        timestamp: Date.now()
      }));
      console.log('✅ Location saved to localStorage:', name);
    } catch (error) {
      console.error('Error saving current location:', error);
    }
  }

  function getCurrentLocation() {
    try {
      const stored = localStorage.getItem(CURRENT_LOCATION_KEY);
      return stored ? JSON.parse(stored) : null;
    } catch (error) {
      console.error('Error loading current location:', error);
      return null;
    }
  }

  function loadSavedLocation() {
    const saved = getCurrentLocation();
    if (saved && saved.name) {
      const currentLocationText = document.getElementById('currentLocationText');
      if (currentLocationText) {
        // Only update if the text is still the default placeholder
        const currentText = currentLocationText.textContent.trim();
        const isPlaceholder = currentText.includes('Select your location') ||
                             currentText.includes('Choisir votre emplacement');

        if (isPlaceholder) {
          currentLocationText.textContent = saved.name;
          console.log('📍 Loaded saved location from localStorage:', saved.name);

          // Also update session in background (no reload - this already runs
          // on every page load, so reloading here would loop)
          updateLocation(saved.name, saved.lat, saved.lon);
        }
      }
    }
  }

})();
