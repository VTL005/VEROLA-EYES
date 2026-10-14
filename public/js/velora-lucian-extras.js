(() => {
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function initPillTabs() {
    document.querySelectorAll('[data-velora-pill-tabs]').forEach((tabs) => {
      const active = tabs.querySelector('.order-filter-tab.active');
      if (!active) return;
      const indicator = document.createElement('span');
      indicator.className = 'velora-pill-tabs__indicator';
      tabs.append(indicator);
      const place = () => {
        const tabBox = tabs.getBoundingClientRect();
        const activeBox = active.getBoundingClientRect();
        indicator.style.width = `${activeBox.width}px`;
        indicator.style.transform = `translateX(${activeBox.left - tabBox.left + tabs.scrollLeft}px)`;
      };
      place();
      window.addEventListener('resize', place, { passive: true });
      tabs.addEventListener('scroll', place, { passive: true });
    });
  }

  function initAccordion() {
    document.querySelectorAll('[data-velora-accordion]').forEach((item) => {
      item.addEventListener('toggle', () => {
        if (!item.open) return;
        item.closest('.customer-chat-faq-list')?.querySelectorAll('[data-velora-accordion][open]').forEach((other) => {
          if (other !== item) other.open = false;
        });
      });
    });
  }

  function streamAiMessage(node) {
    if (!node || node.dataset.veloraStreamed === 'true' || reducedMotion) return;
    const content = node.textContent.trim();
    if (!content || content.length > 900) return;
    node.dataset.veloraStreamed = 'true';
    node.textContent = '';
    node.classList.add('velora-streaming');
    let index = 0;
    const step = () => {
      node.textContent += content.slice(index, index + 3);
      index += 3;
      if (index < content.length) window.setTimeout(step, 14);
      else node.classList.remove('velora-streaming');
    };
    step();
  }

  function initAiStreaming() {
    document.querySelectorAll('[data-velora-stream]').forEach(streamAiMessage);
    const messages = document.getElementById('customerChatMessages');
    if (!messages || !window.MutationObserver) return;
    new MutationObserver((records) => records.forEach((record) => record.addedNodes.forEach((node) => {
      if (!(node instanceof Element)) return;
      node.querySelectorAll?.('[data-velora-stream]').forEach(streamAiMessage);
    }))).observe(messages, { childList: true, subtree: true });
  }

  function launchConfetti() {
    const section = document.querySelector('[data-velora-confetti]');
    if (!section || reducedMotion) return;
    const colors = ['#caa66b', '#174654', '#5caa9d', '#e8d6ad', '#b95955'];
    for (let index = 0; index < 26; index += 1) {
      const piece = document.createElement('span');
      piece.className = 'velora-confetti-piece';
      piece.style.left = `${34 + Math.random() * 32}%`;
      piece.style.background = colors[index % colors.length];
      piece.style.setProperty('--velora-confetti-x', `${-360 + Math.random() * 720}px`);
      piece.style.animationDelay = `${Math.random() * .4}s`;
      document.body.append(piece);
      piece.addEventListener('animationend', () => piece.remove(), { once: true });
    }
  }

  function initNotifications() {
    document.querySelectorAll('[data-velora-notifications]').forEach((notification) => {
      const trigger = notification.querySelector('button');
      const menu = notification.querySelector('[id]');
      if (!trigger || !menu) return;
      const close = () => { menu.hidden = true; trigger.setAttribute('aria-expanded', 'false'); };
      trigger.addEventListener('click', () => {
        const nextOpen = menu.hidden;
        close();
        if (nextOpen) { menu.hidden = false; trigger.setAttribute('aria-expanded', 'true'); }
      });
      document.addEventListener('click', (event) => { if (!notification.contains(event.target)) close(); });
      document.addEventListener('keydown', (event) => { if (event.key === 'Escape') close(); });
    });
  }

  function initAdminTour() {
    const trigger = document.querySelector('[data-velora-tour-trigger]');
    if (!trigger) return;
    const steps = [
      ['[data-velora-tour="dashboard"]', 'Tổng quan', 'Theo dõi các chỉ số chính và những đầu việc cần xử lý trong ngày.'],
      ['[data-velora-tour="chat"]', 'Hộp thư tư vấn', 'Mở để xem hội thoại và tin nhắn khách hàng chưa đọc.'],
      ['[data-velora-tour="orders"]', 'Quản lý đơn hàng', 'Xử lý đơn theo trạng thái, từ chờ xác nhận tới hoàn tất.'],
      ['[data-velora-tour="reviews"]', 'Phản hồi đánh giá', 'Trả lời công khai các đánh giá đang chờ phản hồi từ VELORA.'],
    ];
    trigger.addEventListener('click', () => {
      let stepIndex = 0;
      const scrim = document.createElement('div'); scrim.className = 'velora-tour-scrim';
      const highlight = document.createElement('div'); highlight.className = 'velora-tour-highlight';
      const card = document.createElement('section'); card.className = 'velora-tour-card'; card.setAttribute('role', 'dialog'); card.setAttribute('aria-modal', 'true');
      document.body.append(scrim, highlight, card);
      const finish = () => { scrim.remove(); highlight.remove(); card.remove(); trigger.focus(); };
      const render = () => {
        const [selector, title, copy] = steps[stepIndex];
        const target = document.querySelector(selector);
        if (!target) { finish(); return; }
        target.scrollIntoView({ block: 'center', behavior: reducedMotion ? 'auto' : 'smooth' });
        const box = target.getBoundingClientRect();
        highlight.style.top = `${Math.max(6, box.top - 5)}px`; highlight.style.left = `${Math.max(6, box.left - 5)}px`; highlight.style.width = `${box.width + 10}px`; highlight.style.height = `${box.height + 10}px`;
        card.innerHTML = `<span>BƯỚC ${stepIndex + 1}/${steps.length}</span><h2>${title}</h2><p>${copy}</p><div class="velora-tour-card__actions"><button type="button" class="velora-tour-card__skip">Bỏ qua</button><button type="button" class="velora-tour-card__next">${stepIndex === steps.length - 1 ? 'Hoàn tất' : 'Tiếp theo'}</button></div>`;
        card.style.top = `${Math.min(window.innerHeight - 190, Math.max(16, box.top))}px`;
        card.style.left = `${Math.min(window.innerWidth - Math.min(330, window.innerWidth - 32) - 16, Math.max(16, box.right + 18))}px`;
        card.querySelector('.velora-tour-card__skip').addEventListener('click', finish);
        card.querySelector('.velora-tour-card__next').addEventListener('click', () => { stepIndex += 1; stepIndex < steps.length ? render() : finish(); });
      };
      scrim.addEventListener('click', finish);
      render();
    });
  }

  function initProductImageZoom() {
    if (reducedMotion || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    document.querySelectorAll('[data-velora-image-zoom]').forEach((surface) => {
      if (!surface.querySelector('img')) return;
      surface.addEventListener('pointermove', (event) => {
        const box = surface.getBoundingClientRect();
        const x = Math.min(100, Math.max(0, ((event.clientX - box.left) / box.width) * 100));
        const y = Math.min(100, Math.max(0, ((event.clientY - box.top) / box.height) * 100));
        surface.style.setProperty('--velora-zoom-x', `${x}%`);
        surface.style.setProperty('--velora-zoom-y', `${y}%`);
      });
      surface.addEventListener('pointerenter', () => surface.classList.add('is-zooming'));
      surface.addEventListener('pointerleave', () => surface.classList.remove('is-zooming'));
    });
  }

  function initPageSkeleton() {
    if (reducedMotion) return;
    let timeoutId;
    const show = () => {
      if (document.querySelector('.velora-page-skeleton')) return;
      const overlay = document.createElement('div');
      overlay.className = 'velora-page-skeleton';
      overlay.setAttribute('role', 'status');
      overlay.setAttribute('aria-label', 'Đang tải trang');
      overlay.innerHTML = '<section class="velora-page-skeleton__panel"><div class="velora-page-skeleton__brand"><span class="velora-page-skeleton__mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 12s3.2-5 9-5 9 5 9 5-3.2 5-9 5-9-5-9-5Z"></path><circle cx="12" cy="12" r="2.3"></circle></svg></span><span><small>VELORA EYES</small><strong>Haute Optique</strong></span></div><div class="velora-page-skeleton__content"><div class="velora-page-skeleton__copy"><h2 class="velora-page-skeleton__title">Đang chuẩn bị<br>trải nghiệm của bạn</h2><p class="velora-page-skeleton__caption">Tải nội dung và thông tin sản phẩm mới nhất.</p><div class="velora-page-skeleton__lines" aria-hidden="true"><div class="velora-page-skeleton__line"></div><div class="velora-page-skeleton__line"></div><div class="velora-page-skeleton__line"></div></div></div><div class="velora-page-skeleton__preview" aria-hidden="true"><span class="velora-page-skeleton__card"></span><span class="velora-page-skeleton__card"></span></div></div><div class="velora-page-skeleton__progress" aria-hidden="true"></div></section>';
      document.body.setAttribute('aria-busy', 'true');
      document.body.append(overlay);
    };
    const schedule = () => { window.clearTimeout(timeoutId); timeoutId = window.setTimeout(show, 150); };
    document.addEventListener('click', (event) => {
      const anchor = event.target.closest('a[href]');
      if (!anchor || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      const target = anchor.getAttribute('target');
      const href = anchor.href;
      if (target || anchor.hasAttribute('download') || anchor.dataset.noLoader !== undefined || !href || new URL(href).origin !== window.location.origin || href === window.location.href || anchor.getAttribute('href').startsWith('#')) return;
      schedule();
    });
    document.addEventListener('submit', (event) => {
      if (!event.defaultPrevented && !event.target.hasAttribute('data-no-loader')) schedule();
    });
    window.addEventListener('pageshow', () => { window.clearTimeout(timeoutId); document.body.removeAttribute('aria-busy'); document.querySelector('.velora-page-skeleton')?.remove(); });
  }

  window.VeloraUploadWithProgress = function (url, formData, progressPanel) {
    return new Promise((resolve, reject) => {
      const request = new XMLHttpRequest();
      const bar = progressPanel?.querySelector('[data-upload-progress-bar]');
      const label = progressPanel?.querySelector('[data-upload-progress-label]');
      if (progressPanel) progressPanel.hidden = false;
      request.open('POST', url, true);
      request.setRequestHeader('Accept', 'application/json');
      request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      request.upload.addEventListener('progress', (event) => {
        if (!event.lengthComputable) return;
        const percent = Math.min(100, Math.round((event.loaded / event.total) * 100));
        if (bar) bar.style.width = `${percent}%`;
        if (label) label.textContent = `Đang tải ảnh · ${percent}%`;
      });
      request.addEventListener('load', () => {
        if (bar) bar.style.width = '100%';
        if (label) label.textContent = request.status >= 200 && request.status < 300 ? 'Đã tải ảnh lên' : 'Không thể tải ảnh';
        let data = {};
        try { data = JSON.parse(request.responseText || '{}'); } catch (_) { /* response is handled below */ }
        resolve({ ok: request.status >= 200 && request.status < 300, status: request.status, json: async () => data });
      });
      request.addEventListener('error', () => {
        if (label) label.textContent = 'Mất kết nối, vui lòng thử lại';
        reject(new Error('Không thể kết nối để tải hình ảnh.'));
      });
      request.send(formData);
    });
  };

  function initAppointmentAvailability() {
    const calendar = document.querySelector('[data-appointment-availability]');
    const dateInput = document.getElementById('appointment_date');
    const slotInput = document.getElementById('time_slot');
    if (!calendar || !dateInput || !slotInput || !window.fetch) return;

    const status = calendar.querySelector('[data-availability-status]');
    const retry = calendar.querySelector('[data-appointment-retry]');
    let requestedDate = '';
    let controller;

    const setActiveDay = (date) => calendar.querySelectorAll('[data-appointment-date]').forEach((button) => {
      button.classList.toggle('is-active', button.dataset.appointmentDate === date);
    });

    const updateSlots = (slots) => {
      const availability = new Map(slots.map((slot) => [slot.slot, slot]));
      let availableCount = 0;
      [...slotInput.options].forEach((option) => {
        if (!option.value) return;
        const slot = availability.get(option.value);
        const base = option.dataset.baseLabel || option.textContent.trim();
        option.dataset.baseLabel = base;
        option.disabled = !slot || !slot.is_available;
        option.textContent = slot && slot.is_available
          ? `${base} · còn ${slot.remaining} chỗ`
          : `${base} · đã kín`;
        if (slot?.is_available) availableCount += 1;
      });
      if (slotInput.selectedOptions[0]?.disabled) slotInput.value = '';
      status.textContent = availableCount ? `${availableCount} khung giờ còn chỗ` : 'Ngày này đã hết chỗ';
    };

    const loadAvailability = async (date) => {
      if (!date) return;
      requestedDate = date;
      controller?.abort();
      controller = new AbortController();
      status.textContent = 'Đang kiểm tra chỗ trống…';
      retry.hidden = true;
      try {
        const url = new URL(calendar.dataset.availabilityUrl, window.location.origin);
        url.searchParams.set('date', date);
        const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: controller.signal });
        if (!response.ok) throw new Error('availability');
        const data = await response.json();
        if (requestedDate !== date) return;
        updateSlots(data.slots || []);
      } catch (error) {
        if (error.name === 'AbortError') return;
        status.textContent = 'Chưa thể tải lịch trống';
        retry.hidden = false;
      }
    };

    calendar.addEventListener('click', (event) => {
      const day = event.target.closest('[data-appointment-date]');
      if (!day) return;
      dateInput.value = day.dataset.appointmentDate;
      setActiveDay(dateInput.value);
      loadAvailability(dateInput.value);
    });
    dateInput.addEventListener('change', () => {
      setActiveDay(dateInput.value);
      loadAvailability(dateInput.value);
    });
    retry.addEventListener('click', () => loadAvailability(requestedDate || dateInput.value));
    if (dateInput.value) {
      setActiveDay(dateInput.value);
      loadAvailability(dateInput.value);
    }
  }

  function initUndoSnackbars() {
    document.querySelectorAll('[data-velora-undo-snackbar]').forEach((snackbar) => {
      const expiresAt = Number(snackbar.dataset.expiresAt || 0) * 1000;
      const countdown = snackbar.querySelector('[data-undo-countdown]');
      const tick = () => {
        const remaining = Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000));
        if (countdown) countdown.textContent = `${remaining}s`;
        if (!remaining) { snackbar.remove(); return; }
        window.setTimeout(tick, 250);
      };
      tick();
    });
  }

  function initImageDropzones() {
    document.querySelectorAll('#create-image-form, #edit-image-upload-form').forEach((form) => {
      const input = form.querySelector('input[type="file"]');
      const progress = form.querySelector('.velora-upload-progress');
      if (!input) return;
      form.classList.add('velora-upload-dropzone');
      const setSelection = (files) => {
        if (!files?.length) return;
        if (progress) {
          progress.hidden = false;
          const label = progress.querySelector('[data-upload-progress-label]');
          const bar = progress.querySelector('[data-upload-progress-bar]');
          if (label) label.textContent = `Đã chọn ${files.length} ảnh · sẵn sàng tải`;
          if (bar) bar.style.width = '0%';
        }
      };
      input.addEventListener('change', () => setSelection(input.files));
      ['dragenter', 'dragover'].forEach((type) => form.addEventListener(type, (event) => {
        event.preventDefault();
        form.classList.add('is-dragging');
      }));
      ['dragleave', 'drop'].forEach((type) => form.addEventListener(type, (event) => {
        event.preventDefault();
        form.classList.remove('is-dragging');
      }));
      form.addEventListener('drop', (event) => {
        const files = event.dataTransfer?.files;
        if (!files?.length) return;
        try { input.files = files; } catch (_) { return; }
        setSelection(files);
      });
    });
  }

  function initFloatingFields() {
    document.querySelectorAll('.profile-form-card form .form-group').forEach((group) => {
      const input = group.querySelector('input.form-control, select.form-control, textarea.form-control');
      const label = group.querySelector('label.form-label');
      if (!input || !label) return;
      group.classList.add('velora-floating-field');
      const sync = () => group.classList.toggle('has-value', Boolean(input.value));
      input.addEventListener('input', sync);
      input.addEventListener('change', sync);
      sync();
    });
  }

  function initLoginFeedback() {
    document.querySelectorAll('[data-velora-login-feedback]').forEach((form) => {
      if (form.querySelector('.input-error')) form.classList.add('has-login-error');
      form.addEventListener('submit', () => {
        if (!form.checkValidity()) return;
        const button = form.querySelector('button[type="submit"]');
        if (!button) return;
        button.disabled = true;
        button.dataset.originalLabel = button.textContent;
        button.textContent = 'Đang xác thực…';
      });
    });
  }

  function initAdminSidebarCollapse() {
    const trigger = document.querySelector('[data-admin-sidebar-collapse]');
    if (!trigger || window.innerWidth <= 820) return;
    const key = 'velora-admin-sidebar-collapsed';
    const sync = (collapsed) => {
      document.body.classList.toggle('admin-sidebar-collapsed', collapsed);
      trigger.setAttribute('aria-pressed', String(collapsed));
      trigger.setAttribute('aria-label', collapsed ? 'Mở rộng menu quản trị' : 'Thu gọn menu quản trị');
      trigger.querySelector('i')?.classList.toggle('bi-layout-sidebar-inset', !collapsed);
      trigger.querySelector('i')?.classList.toggle('bi-layout-sidebar-inset-reverse', collapsed);
    };
    document.querySelectorAll('.admin-sidebar .admin-nav a').forEach((link) => {
      const label = link.textContent.replace(/\s+/g, ' ').trim();
      link.dataset.sidebarLabel = label;
      link.setAttribute('aria-label', label);
    });
    sync(window.localStorage.getItem(key) === '1');
    trigger.addEventListener('click', () => {
      const next = !document.body.classList.contains('admin-sidebar-collapsed');
      window.localStorage.setItem(key, next ? '1' : '0');
      sync(next);
    });
  }

  function initUnderlineTabs() {
    document.querySelectorAll('[data-velora-underline-tabs]').forEach((tabs) => {
      const active = tabs.querySelector('.active');
      if (!active) return;
      const line = document.createElement('span');
      line.className = 'velora-underline-tabs__indicator';
      tabs.append(line);
      const place = () => {
        const box = tabs.getBoundingClientRect();
        const item = active.getBoundingClientRect();
        line.style.width = `${item.width}px`;
        line.style.transform = `translateX(${item.left - box.left + tabs.scrollLeft}px)`;
      };
      place(); window.addEventListener('resize', place, { passive: true }); tabs.addEventListener('scroll', place, { passive: true });
    });
  }

  function initVariantColorChips() {
    const colors = { den: '#182331', black: '#182331', trang: '#f7f7f4', white: '#f7f7f4', nau: '#6d4735', brown: '#6d4735', vang: '#c99a3c', gold: '#c99a3c', bac: '#c4cbd2', silver: '#c4cbd2', hong: '#d88b9f', pink: '#d88b9f', xanh: '#4f8e82', blue: '#5484b5', xam: '#8d949c', gray: '#8d949c', trong: '#eaf4f8', clear: '#eaf4f8' };
    const slug = (value) => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim();
    const decorate = (cell) => {
      if (cell.dataset.decorated) return;
      cell.dataset.decorated = 'true';
      const name = cell.dataset.variantColor || cell.textContent.trim();
      const swatch = document.createElement('span'); swatch.className = 'velora-variant-swatch'; swatch.style.background = colors[slug(name)] || '#9ba8b5'; swatch.setAttribute('aria-hidden', 'true');
      cell.prepend(swatch);
    };
    const run = () => document.querySelectorAll('.velora-variant-color').forEach(decorate);
    run();
    new MutationObserver(run).observe(document.body, { childList: true, subtree: true });
  }

  function initAppointmentQrScanner() {
    document.querySelectorAll('[data-appointment-checkin]').forEach((checkin) => {
      const button = checkin.querySelector('[data-qr-scan]');
      const result = checkin.querySelector('[data-qr-scan-result]');
      const code = checkin.dataset.appointmentCode;
      if (!button || !result) return;
      button.addEventListener('click', async () => {
        if (!('BarcodeDetector' in window) || !navigator.mediaDevices?.getUserMedia) {
          result.textContent = 'Trình duyệt này chưa hỗ trợ quét QR. Vui lòng đối chiếu mã lịch bên cạnh.';
          return;
        }
        let stream;
        const dialog = document.createElement('dialog');
        dialog.className = 'velora-qr-scanner';
        dialog.innerHTML = '<button type="button" aria-label="Đóng camera">×</button><h2>Quét mã lịch hẹn</h2><video autoplay playsinline></video><p>Đưa mã QR của khách vào khung hình.</p>';
        document.body.append(dialog);
        const close = () => { stream?.getTracks().forEach((track) => track.stop()); dialog.close(); };
        dialog.querySelector('button').addEventListener('click', close);
        dialog.addEventListener('close', () => dialog.remove());
        try {
          stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
          const video = dialog.querySelector('video'); video.srcObject = stream; dialog.showModal();
          const detector = new BarcodeDetector({ formats: ['qr_code'] });
          const scan = async () => {
            if (!dialog.open) return;
            try {
              const codes = await detector.detect(video);
              const matched = codes.find((item) => item.rawValue === code);
              if (matched) { result.textContent = 'Đã nhận đúng mã lịch hẹn.'; result.classList.add('is-success'); close(); return; }
            } catch (_) { /* camera frame is not ready yet */ }
            window.requestAnimationFrame(scan);
          };
          video.addEventListener('playing', scan, { once: true });
        } catch (_) {
          result.textContent = 'Không thể mở camera. Bạn vẫn có thể đối chiếu mã lịch thủ công.';
          dialog.remove();
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initPillTabs(); initAccordion(); initAiStreaming(); launchConfetti(); initNotifications(); initAdminTour(); initProductImageZoom(); initPageSkeleton(); initAppointmentAvailability(); initUndoSnackbars(); initImageDropzones(); initFloatingFields(); initLoginFeedback(); initAdminSidebarCollapse(); initUnderlineTabs(); initVariantColorChips(); initAppointmentQrScanner();
  });
})();
