/*
 * Small, compositor-friendly interactions for the customer-facing showcase.
 * They only enhance precise pointers and never replace a normal interaction.
 */
(() => {
  'use strict';

  const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  if (reducedMotion) return;

  const init = () => {
    const progress = document.createElement('div');
    progress.className = 'velora-scroll-progress';
    progress.setAttribute('aria-hidden', 'true');
    document.body.appendChild(progress);

    let scrollFrame = null;
    const updateProgress = () => {
      const available = document.documentElement.scrollHeight - window.innerHeight;
      const ratio = available > 0 ? Math.min(1, window.scrollY / available) : 0;
      progress.style.transform = `scaleX(${ratio})`;
      scrollFrame = null;
    };
    window.addEventListener('scroll', () => {
      if (!scrollFrame) scrollFrame = window.requestAnimationFrame(updateProgress);
    }, { passive: true });
    updateProgress();

    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;

    document.querySelectorAll('.vl-home-page .vl-hero-actions .vl-btn, .vl-home-page .vl-warranty-box .vl-btn').forEach((element) => {
      element.classList.add('velora-magnetic');
      element.addEventListener('pointermove', (event) => {
        const rect = element.getBoundingClientRect();
        const x = ((event.clientX - rect.left) / rect.width - .5) * 7;
        const y = ((event.clientY - rect.top) / rect.height - .5) * 5;
        element.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0)`;
      });
      element.addEventListener('pointerleave', () => { element.style.transform = ''; });
    });

    document.querySelectorAll('.vl-home-page .home-category-featured, .vl-home-page .home-new-product-card--primary').forEach((card) => {
      card.classList.add('velora-tilt-card');
      card.addEventListener('pointermove', (event) => {
        const rect = card.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - .5;
        const y = (event.clientY - rect.top) / rect.height - .5;
        card.style.setProperty('--velora-tilt-x', `${(-y * 3).toFixed(2)}deg`);
        card.style.setProperty('--velora-tilt-y', `${(x * 3).toFixed(2)}deg`);
        card.classList.add('velora-tilt-active');
      });
      card.addEventListener('pointerleave', () => {
        card.classList.remove('velora-tilt-active');
        card.style.removeProperty('--velora-tilt-x');
        card.style.removeProperty('--velora-tilt-y');
      });
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
