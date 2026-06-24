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
      <div class="location-suggestion-item" data-name="${suggestion.name}" data-address="${suggestion.address}">
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
        selectLocation(name, address);
      });
    });
  }

  function selectLocation(name, address) {
    // Update session
    updateLocation(name);

    // Save to recent locations
    saveToRecentLocations(name, address);

    // Save current location to localStorage (persistent across pages)
    saveCurrentLocation(name, address);

    // Update UI
    const currentLocationText = document.getElementById('currentLocationText');
    if (currentLocationText) {
      currentLocationText.textContent = name;
    }

    // Close modal
    closeModal();

    // Optional: Reload page to update content
    // window.location.reload();
  }

  async function updateLocation(location) {
    try {
      const basePath = getBasePath();
      const response = await fetch(`${basePath}/set-location`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          location: location
        })
      });

      if (response.ok) {
        console.log('Location updated:', location);
      }
    } catch (error) {
      console.error('Error updating location:', error);
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

          // Get location name from server response
          const locationName = data.location || 'Your Location';

          // Successfully select location
          await selectLocation(locationName, `${latitude}, ${longitude}`);

          // Reset button after success
          currentLocationBtn.innerHTML = '<i class="fas fa-check"></i> Location Set!';
          currentLocationBtn.style.background = 'linear-gradient(135deg, #4CAF50 0%, #45a049 100%)';

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
        enableHighAccuracy: false, // Changed to false for faster response
        timeout: 5000, // Reduced timeout
        maximumAge: 30000 // Allow cached position up to 30 seconds old
      }
    );
  }

  function saveToRecentLocations(name, address) {
    let recent = getRecentLocations();

    // Remove if already exists
    recent = recent.filter(loc => loc.name !== name);

    // Add to beginning
    recent.unshift({ name, address, timestamp: Date.now() });

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
      <div class="recent-location-item" data-name="${loc.name}" data-address="${loc.address}">
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
        selectLocation(name, address);
      });
    });
  }

  // Current location persistence functions
  function saveCurrentLocation(name, address) {
    try {
      localStorage.setItem(CURRENT_LOCATION_KEY, JSON.stringify({
        name,
        address,
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

          // Also update session in background
          updateLocation(saved.name);
        }
      }
    }
  }

})();
