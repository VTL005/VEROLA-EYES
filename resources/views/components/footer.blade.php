<footer class="site-footer">

  <div class="vl-container footer-main">

    <div>

      <h3 class="footer-title">
        VELORA EYES
      </h3>

      <p style="color:#cbd5e1;line-height:1.65;font-size:0.92rem;margin-bottom:10px;">
        Kính mắt thời trang, tư vấn thị lực
        và trải nghiệm mua sắm hiện đại.
      </p>

      <p style="color:var(--vl-champagne);font-size:0.88rem;font-style:italic;" class="mb-0">
        Nhìn rõ hơn. Sống phong cách hơn.
      </p>

    </div>


    <div>

      <h3 class="footer-title">
        Khám phá
      </h3>

      <div class="footer-links">

        <a href="{{ route('home') }}">
          Trang chủ
        </a>

        <a href="{{ route('products.index') }}">
          Sản phẩm
        </a>

        @if(Route::has('warranties.lookup-form'))

        <a href="{{ route('warranties.lookup-form') }}">
          Tra cứu bảo hành
        </a>

        @endif

      </div>

    </div>


    <div>

      <h3 class="footer-title">
        Dịch vụ VELORA
      </h3>

      <div class="footer-links">

        <span>
          Tư vấn chọn kính
        </span>

        <span>
          Đo mắt tại cửa hàng
        </span>

        <span>
          Bảo hành điện tử
        </span>

        <span>
          Hỗ trợ đơn hàng
        </span>

      </div>

    </div>


    <div>

      <h3 class="footer-title">
        Về chúng tôi
      </h3>

      <div class="footer-links footer-contact-links">

        @if(Route::has('about'))

        <a href="{{ route('about') }}" class="footer-story-link">
          <span class="footer-story-text">Câu chuyện VELORA</span>
        </a>

        @else

        <span class="footer-story-link">
          <span class="footer-story-text">Câu chuyện VELORA</span>
        </span>

        @endif

        <a href="tel:19001900" class="footer-hotline-link">
          <svg class="footer-hotline-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
          </svg>
          <span class="footer-hotline-text">Hotline: 1900 1900</span>
        </a>

        <address class="footer-address">
          Địa chỉ: 41A – Trường Đại học
          Tài nguyên và Môi trường Hà Nội,
          Phú Diễn, Bắc Từ Liêm, Hà Nội.
        </address>

      </div>

    </div>

  </div>


  <div class="footer-bottom">

    <div class="vl-container">

      © {{ date('Y') }} VELORA Eyes — Dự án thực hiện bởi Nhóm 14 | Phạm Thị Thúy Mỹ &amp; Vy Thế Long

    </div>

  </div>

</footer>