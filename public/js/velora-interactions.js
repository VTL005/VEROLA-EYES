(() => {
  'use strict';

  const getRegion = () => {
    let region = document.querySelector('.velora-toast-region');
    if (!region) {
      region = document.createElement('div');
      region.className = 'velora-toast-region';
      region.setAttribute('aria-live', 'polite');
      region.setAttribute('aria-label', 'Thông báo');
      document.body.appendChild(region);
    }
    return region;
  };

  const icons = { success: '✓', error: '!', info: 'i' };
  const showToast = (message, type = 'info') => {
    if (!message) return;
    const toast = document.createElement('div');
    toast.className = `velora-toast velora-toast--${type}`;
    toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
    toast.innerHTML = `<span class="velora-toast__icon" aria-hidden="true">${icons[type] || icons.info}</span><span class="velora-toast__message"></span><button class="velora-toast__close" type="button" aria-label="Đóng thông báo">×</button>`;
    toast.querySelector('.velora-toast__message').textContent = message;
    const remove = () => toast.remove();
    toast.querySelector('button').addEventListener('click', remove);
    getRegion().appendChild(toast);
    window.setTimeout(remove, type === 'error' ? 7000 : 4500);
  };

  window.VeloraToast = { show: showToast };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-velora-toast]').forEach((source) => {
      const message = source.textContent.trim();
      const type = source.dataset.toastType || 'info';
      source.remove();
      showToast(message, type);
    });

    document.querySelectorAll('[data-copy-value]').forEach((button) => {
      button.addEventListener('click', async () => {
        const value = button.dataset.copyValue || '';
        try {
          await navigator.clipboard.writeText(value);
          button.textContent = 'Đã sao chép';
          button.setAttribute('aria-label', 'Đã sao chép mã');
          button.classList.add('is-copied');
          button.closest('[data-velora-copy]')?.classList.add('is-copied');
          showToast('Đã sao chép mã vào bộ nhớ tạm.', 'success');
          window.setTimeout(() => {
            button.textContent = 'Sao chép';
            button.setAttribute('aria-label', 'Sao chép mã');
            button.classList.remove('is-copied');
            button.closest('[data-velora-copy]')?.classList.remove('is-copied');
          }, 1800);
        } catch (_) {
          showToast('Không thể sao chép mã. Bạn có thể sao chép thủ công.', 'error');
        }
      });
    });

    document.querySelectorAll('[data-velora-count]').forEach((node) => {
      const target = Number(node.dataset.veloraCount);
      if (!Number.isFinite(target) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      const prefix = node.dataset.countPrefix || '';
      const suffix = node.dataset.countSuffix || '';
      const startedAt = performance.now();
      const duration = 520;
      const format = new Intl.NumberFormat('vi-VN');
      const animate = (now) => {
        const progress = Math.min(1, (now - startedAt) / duration);
        node.textContent = prefix + format.format(Math.round(target * (1 - Math.pow(1 - progress, 3)))) + suffix;
        if (progress < 1) requestAnimationFrame(animate);
      };
      node.textContent = prefix + '0' + suffix;
      requestAnimationFrame(animate);
    });

    document.querySelectorAll('[data-lens-comparison]').forEach((comparison) => {
      const range = comparison.querySelector('[data-lens-comparison-range]');
      const scene = comparison.querySelector('.lens-comparison-scene');
      const output = comparison.querySelector('[data-lens-comparison-output]');
      if (!range || !scene || !output) return;

      const updateComparison = () => {
        const value = range.value;
        scene.style.setProperty('--lens-compare-position', `${value}%`);
        output.textContent = `Đang xem ${value}% lớp lọc ấm`;
      };

      range.addEventListener('input', updateComparison);
      updateComparison();
    });
  });
})();
