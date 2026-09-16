/**
 * VELORA EYES - Motion Engine
 * Single IntersectionObserver scroll reveal & header scroll state
 * Progressive enhancement: zero content loss if JS fails
 */
(function () {
  'use strict';

  // Respect user preference for reduced motion
  var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) {
    return;
  }

  function initMotion() {
    var docEl = document.documentElement;

    // Header scroll state (lightweight RAF, passive scroll)
    var siteHeader = document.querySelector('.site-header');
    if (siteHeader) {
      var ticking = false;
      window.addEventListener('scroll', function () {
        if (!ticking) {
          window.requestAnimationFrame(function () {
            if (window.scrollY > 24) {
              siteHeader.classList.add('is-scrolled');
            } else {
              siteHeader.classList.remove('is-scrolled');
            }
            ticking = false;
          });
          ticking = true;
        }
      }, { passive: true });
    }

    // Check if on homepage
    var isHome = !!document.querySelector('.vl-home-page');
    if (isHome) {
      docEl.classList.add('is-vl-home');
    }

    // Scroll reveal observer - Single unified observer
    if (!('IntersectionObserver' in window)) {
      return; // Fallback: elements remain 100% visible
    }

    // Build reveal element list
    var selector = '.vl-reveal, [data-home-reveal], [data-home-reveal-item], .about-reveal, [data-reveal]';
    if (isHome) {
      selector += ', .vl-home-page .home-category-featured, .vl-home-page .home-category-secondary, .vl-home-page .home-new-product-reveal, .vl-home-page .home-new-products__primary, .vl-home-page .home-new-products__secondary-grid > .product-card, .vl-home-page .home-sale-product-reveal, .site-footer .footer-main > div';
    }

    var reveals = document.querySelectorAll(selector);
    if (!reveals.length) {
      return;
    }

    // Activate motion readiness only when observer is verified
    docEl.classList.add('vl-motion-ready');

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.12,
      rootMargin: '0px 0px -8% 0px'
    });

    reveals.forEach(function (el) {
      // Support custom data-home-reveal-delay if specified
      var customDelay = el.getAttribute('data-home-reveal-delay');
      if (customDelay) {
        el.style.setProperty('--reveal-delay', customDelay + 'ms');
      }
      observer.observe(el);
    });

    // Floating chat entrance (one-time smooth arrival on homepage)
    if (isHome) {
      var chatBtn = document.querySelector('.customer-floating-chat');
      if (chatBtn) {
        window.setTimeout(function () {
          chatBtn.classList.add('is-entered');
        }, 450);
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMotion);
  } else {
    initMotion();
  }
})();
