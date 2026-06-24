/**
 * Hero Slider Functionality
 */
(function() {
  let currentSlide = 0;
  let autoSlideInterval;
  const slides = document.querySelectorAll('.slide');
  const totalSlides = slides.length;

  if (totalSlides === 0) return; // Exit if no slides

  // Create dots
  const dotsContainer = document.querySelector('.hero-dots');
  if (dotsContainer) {
    slides.forEach((_, index) => {
      const dot = document.createElement('button');
      dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
      dot.addEventListener('click', () => goToSlide(index));
      dotsContainer.appendChild(dot);
    });
  }

  const dots = dotsContainer ? dotsContainer.querySelectorAll('button') : [];

  // Set background images
  slides.forEach(slide => {
    const bgImage = slide.getAttribute('data-bg');
    if (bgImage) {
      slide.style.backgroundImage = `url('${bgImage}')`;
    }
  });

  // Update slide and dots
  function updateSlide() {
    // Remove active class from all slides and dots
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    // Add active class to current slide and dot
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) {
      dots[currentSlide].classList.add('active');
    }
  }

  // Go to specific slide
  function goToSlide(index) {
    currentSlide = index;
    updateSlide();
    resetAutoSlide();
  }

  // Next slide
  function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateSlide();
    resetAutoSlide();
  }

  // Previous slide
  function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateSlide();
    resetAutoSlide();
  }

  // Auto slide
  function startAutoSlide() {
    autoSlideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
  }

  function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
  }

  // Event listeners for navigation buttons
  const prevBtn = document.querySelector('.hero-nav.prev');
  const nextBtn = document.querySelector('.hero-nav.next');

  if (prevBtn) {
    prevBtn.addEventListener('click', prevSlide);
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', nextSlide);
  }

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') prevSlide();
    if (e.key === 'ArrowRight') nextSlide();
  });

  // Touch/swipe support
  let touchStartX = 0;
  let touchEndX = 0;
  const slider = document.querySelector('.hero-slider');

  if (slider) {
    slider.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    slider.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, { passive: true });
  }

  function handleSwipe() {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;

    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) {
        nextSlide(); // Swipe left
      } else {
        prevSlide(); // Swipe right
      }
    }
  }

  // Pause on hover
  if (slider) {
    slider.addEventListener('mouseenter', () => {
      clearInterval(autoSlideInterval);
    });

    slider.addEventListener('mouseleave', () => {
      startAutoSlide();
    });
  }

  // Initialize
  updateSlide();
  startAutoSlide();
})();

/**
 * Newsletter Subscription
 */
(function() {
  const newsletterForm = document.querySelector('.newsletter-form');

  if (!newsletterForm) return;

  newsletterForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const emailInput = document.getElementById('newsletterEmail');
    const submitBtn = this.querySelector('.subscribe-btn');
    const email = emailInput.value.trim();

    if (!email) {
      alert('Please enter your email address');
      return;
    }

    // Disable button and show loading state
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Subscribing...';

    const consentBox = document.getElementById('newsletterConsent');
    if (consentBox && !consentBox.checked) {
      alert('Please check the consent box to subscribe');
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      return;
    }

    try {
      const response = await fetch(window.OCSAPP_CONFIG.urls.newsletter, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, consent: consentBox ? consentBox.checked : true })
      });

      const data = await response.json();

      if (data.success) {
        alert(data.message);
        emailInput.value = ''; // Clear the input
      } else {
        alert(data.message || 'An error occurred. Please try again.');
      }
    } catch (error) {
      console.error('Newsletter subscription error:', error);
      alert('An error occurred. Please try again later.');
    } finally {
      // Re-enable button
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });
})();

/**
 * Add to Cart Functionality
 * Shows auth popup for guests, allows cart add for logged-in users
 */
(function() {
  // Store the last clicked button for use after auth popup
  let lastClickedBtn = null;

  /**
   * Direct add to cart function (called after auth popup "Continue as Guest")
   */
  window.addToCartDirect = function(productId, quantity = 1) {
    const cartAddUrl = window.OCSAPP_CONFIG?.urls?.cartAdd || '/cart/add';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch(cartAddUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: quantity,
        _csrf_token: csrfToken
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update cart count in header
        const cartCount = document.getElementById('cartCount');
        const mobileCartCount = document.getElementById('mobileCartCount');
        if (cartCount) {
          cartCount.textContent = data.cart_count || (parseInt(cartCount.textContent) + 1);
          cartCount.style.display = 'flex';
        }
        if (mobileCartCount) {
          mobileCartCount.textContent = data.cart_count || (parseInt(mobileCartCount.textContent) + 1);
          mobileCartCount.style.display = 'block';
        }

        // Show brief success message
        if (lastClickedBtn) {
          const originalHtml = lastClickedBtn.innerHTML;
          lastClickedBtn.innerHTML = '<i class="fas fa-check"></i> Added!';
          lastClickedBtn.style.background = '#16a34a';
          setTimeout(() => {
            lastClickedBtn.innerHTML = originalHtml;
            lastClickedBtn.style.background = '';
            lastClickedBtn.disabled = false;
            lastClickedBtn = null;
          }, 1500);
        }
      } else {
        alert(data.message || 'Error adding to cart');
        if (lastClickedBtn) {
          lastClickedBtn.disabled = false;
          lastClickedBtn = null;
        }
      }
    })
    .catch(error => {
      console.error('Add to cart error:', error);
      alert('Error adding to cart. Please try again.');
      if (lastClickedBtn) {
        lastClickedBtn.disabled = false;
        lastClickedBtn = null;
      }
    });
  };

  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.add-to-cart');
    if (!btn || btn.disabled) return;

    const productId = btn.dataset.productId;
    if (!productId) return;

    // Check if user is logged in
    const isLoggedIn = window.OCSAPP_CONFIG?.isLoggedIn === true ||
                       window.OCSAPP_CONFIG?.isLoggedIn === 'true';

    // If not logged in, show auth popup
    if (!isLoggedIn && typeof showAuthPopup === 'function') {
      e.preventDefault();
      e.stopPropagation();
      lastClickedBtn = btn;

      // Store product info for "Continue as Guest" action
      window.pendingCartProduct = {
        productId: productId,
        quantity: 1
      };

      showAuthPopup();
      return;
    }

    // User is logged in - proceed with add to cart
    btn.disabled = true;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    const cartAddUrl = window.OCSAPP_CONFIG?.urls?.cartAdd || '/cart/add';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch(cartAddUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: 1,
        _csrf_token: csrfToken
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update cart count in header
        const cartCount = document.getElementById('cartCount');
        const mobileCartCount = document.getElementById('mobileCartCount');
        if (cartCount) {
          cartCount.textContent = data.cart_count || (parseInt(cartCount.textContent) + 1);
          cartCount.style.display = 'flex';
        }
        if (mobileCartCount) {
          mobileCartCount.textContent = data.cart_count || (parseInt(mobileCartCount.textContent) + 1);
          mobileCartCount.style.display = 'block';
        }

        // Show success feedback
        btn.innerHTML = '<i class="fas fa-check"></i> Added!';
        btn.style.background = '#16a34a';

        setTimeout(() => {
          btn.innerHTML = originalHtml;
          btn.style.background = '';
          btn.disabled = false;
        }, 1500);
      } else {
        alert(data.message || 'Error adding to cart');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
      }
    })
    .catch(error => {
      console.error('Add to cart error:', error);
      alert('Error adding to cart. Please try again.');
      btn.innerHTML = originalHtml;
      btn.disabled = false;
    });
  });
})();
