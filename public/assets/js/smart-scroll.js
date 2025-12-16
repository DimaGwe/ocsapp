/**
 * Smart Scroll - Intelligent Grid/Scroll Mode Toggle
 * Enables horizontal scrolling only when items exceed visible area
 */

(function() {
  'use strict';

  // Configuration: items per row based on screen size
  const getItemsPerRow = () => {
    const width = window.innerWidth;
    if (width >= 1200) return 4; // Desktop
    if (width >= 769) return 3;  // Tablet
    return 2; // Mobile
  };

  // Calculate if scrolling is needed
  const needsScrolling = (container) => {
    const grid = container.querySelector('.products-scroll-grid');
    if (!grid) return false;

    const items = grid.children.length;
    const itemsPerRow = getItemsPerRow();

    // If items fit in one full row or less, no scrolling needed
    // For example: 4 items per row, 6 items = 2 rows (4 + 2), no scroll needed
    // But 10+ items = more rows, enable scrolling
    const threshold = itemsPerRow * 2; // Show up to 2 rows without scrolling

    return items > threshold;
  };

  // Toggle scroll mode for a container
  const updateScrollMode = (container) => {
    const grid = container.querySelector('.products-scroll-grid');
    if (!grid) return;

    if (needsScrolling(container)) {
      grid.classList.add('scroll-mode');
      updateScrollButtons(container);
    } else {
      grid.classList.remove('scroll-mode');
    }
  };

  // Update scroll button visibility and state
  const updateScrollButtons = (container) => {
    const grid = container.querySelector('.products-scroll-grid');
    const leftBtn = container.querySelector('.scroll-btn-left');
    const rightBtn = container.querySelector('.scroll-btn-right');

    if (!grid || !leftBtn || !rightBtn) return;

    const isAtStart = grid.scrollLeft <= 0;
    const isAtEnd = grid.scrollLeft + grid.clientWidth >= grid.scrollWidth - 1;

    // Disable/enable buttons based on scroll position
    leftBtn.style.opacity = isAtStart ? '0.3' : '1';
    leftBtn.style.pointerEvents = isAtStart ? 'none' : 'auto';

    rightBtn.style.opacity = isAtEnd ? '0.3' : '1';
    rightBtn.style.pointerEvents = isAtEnd ? 'none' : 'auto';
  };

  // Scroll by one row (itemsPerRow items)
  const scrollByRow = (grid, direction) => {
    const itemsPerRow = getItemsPerRow();
    const firstCard = grid.querySelector('.product-card, .category-card, .shop-card, .brand-card');
    if (!firstCard) return;

    const cardWidth = firstCard.offsetWidth;
    const gap = 24; // Match CSS gap
    const scrollAmount = (cardWidth + gap) * itemsPerRow;

    grid.scrollBy({
      left: direction === 'right' ? scrollAmount : -scrollAmount,
      behavior: 'smooth'
    });
  };

  // Setup scroll buttons for a container
  const setupScrollButtons = (container) => {
    const grid = container.querySelector('.products-scroll-grid');
    const leftBtn = container.querySelector('.scroll-btn-left');
    const rightBtn = container.querySelector('.scroll-btn-right');

    if (!grid || !leftBtn || !rightBtn) return;

    // Click handlers
    leftBtn.addEventListener('click', () => scrollByRow(grid, 'left'));
    rightBtn.addEventListener('click', () => scrollByRow(grid, 'right'));

    // Update button states on scroll
    grid.addEventListener('scroll', () => updateScrollButtons(container));

    // Initial button state
    updateScrollButtons(container);
  };

  // Initialize all scroll containers
  const initScrollContainers = () => {
    const containers = document.querySelectorAll('.products-scroll-container');

    containers.forEach(container => {
      updateScrollMode(container);
      setupScrollButtons(container);
    });
  };

  // Re-evaluate on window resize
  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      initScrollContainers();
    }, 250);
  });

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollContainers);
  } else {
    initScrollContainers();
  }

  // Re-initialize after dynamic content loads (for AJAX)
  window.reinitSmartScroll = initScrollContainers;

})();
