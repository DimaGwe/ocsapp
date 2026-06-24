/**
 * Promo Banner Image Slider
 * Automatically rotates product images in the promo banner
 */

(function() {
  'use strict';

  const container = document.querySelector('.promo-slides-container');
  if (!container) return;

  const slides = container.querySelectorAll('.promo-slide');
  if (slides.length <= 1) return; // No need to animate if only one slide

  let currentIndex = 0;
  const slideInterval = 2000; // 2 seconds per slide

  function rotateSlides() {
    // Remove active and next classes from all slides
    slides.forEach(slide => {
      slide.classList.remove('active', 'next');
    });

    // Calculate next index
    const nextIndex = (currentIndex + 1) % slides.length;

    // Add classes
    slides[currentIndex].classList.add('active');
    slides[nextIndex].classList.add('next');

    // Update current index
    currentIndex = nextIndex;
  }

  // Start automatic rotation
  setInterval(rotateSlides, slideInterval);

  // Initial setup
  slides[0].classList.add('active');
  if (slides.length > 1) {
    slides[1].classList.add('next');
  }
})();
