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
