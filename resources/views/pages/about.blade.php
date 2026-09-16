@extends('layouts.app')


@section(
'title',
'Câu chuyện VELORA - VELORA Eyes'
)


@push('styles')

<link rel="stylesheet" href="{{ asset('css/about-page.css') }}">

@endpush


@section('content')

<main class="about-page">

  <section class="about-hero">

    <div class="velora-container about-hero-inner">

      <div class="about-hero-copy">

        <span class="about-eyebrow about-reveal about-reveal-kicker">
          CÂU CHUYỆN VELORA
        </span>

        <h1 class="about-hero-title">
          <span class="about-reveal about-reveal-line" style="--reveal-delay: 100ms;">VELORA EYES</span>
          <span class="about-reveal about-reveal-line" style="--reveal-delay: 240ms;">Rất là phong cách.</span>
        </h1>

        <p class="about-reveal about-reveal-p" style="--reveal-delay: 380ms;">
          VELORA EYES tin rằng kính mắt không chỉ hỗ trợ
          thị lực mà còn thể hiện phong cách và cá tính
          riêng của mỗi người.
        </p>

        <div class="about-hero-actions about-reveal about-reveal-actions" style="--reveal-delay: 520ms;">

          <a href="{{ route('products.index') }}" class="btn btn-primary">
            Khám phá sản phẩm
          </a>

          <a href="tel:19001900" class="btn btn-outline about-outline-button">
            Gọi 1900 1900
          </a>

        </div>

      </div>


      <div class="about-hero-mark about-reveal about-reveal-motif" aria-hidden="true" style="--reveal-delay: 180ms;">

        <span class="about-mark-letter">
          V
        </span>

        <span class="about-mark-caption">
          VISION · STYLE · CARE
        </span>

      </div>

    </div>

  </section>


  <section class="about-story-section">

    <div class="velora-container about-story-grid">

      <div class="about-reveal about-reveal-up">

        <span class="about-eyebrow">
          CHÚNG TÔI LÀ AI
        </span>

        <h2>
          VELORA bắt đầu từ một điều giản dị
        </h2>

      </div>


      <div class="about-story-copy">

        <p class="about-reveal about-reveal-p" style="--reveal-delay: 120ms;">
          Một chiếc kính phù hợp cần mang lại cả sự thoải mái,
          tầm nhìn tốt và cảm giác tự tin khi sử dụng mỗi ngày.
          Vì vậy, VELORA hướng đến trải nghiệm mua kính rõ ràng,
          thuận tiện và gần gũi hơn.
        </p>

        <p class="about-reveal about-reveal-p" style="--reveal-delay: 260ms;">
          Từ lúc lựa chọn kiểu dáng, thông số phù hợp cho đến
          khi nhận hàng và sử dụng sản phẩm, chúng tôi mong muốn
          đồng hành cùng khách hàng bằng sự chỉn chu trong từng
          bước nhỏ.
        </p>

      </div>

    </div>

  </section>


  <section class="about-values-section">

    <div class="velora-container">

      <div class="about-section-heading about-reveal about-reveal-up">

        <span class="about-eyebrow">
          GIÁ TRỊ VELORA
        </span>

        <h2>
          Điều chúng tôi luôn theo đuổi
        </h2>

      </div>


      <div class="about-values-grid">

        <article class="about-value-card about-reveal about-reveal-card" style="--reveal-delay: 100ms;">

          <span class="about-value-number">
            01
          </span>

          <h3>
            Tư vấn tận tâm
          </h3>

          <p>
            Đặt nhu cầu thực tế và trải nghiệm của khách hàng
            làm trung tâm trong quá trình lựa chọn kính.
          </p>

        </article>


        <article class="about-value-card about-reveal about-reveal-card" style="--reveal-delay: 240ms;">

          <span class="about-value-number">
            02
          </span>

          <h3>
            Sản phẩm chỉn chu
          </h3>

          <p>
            Chú trọng kiểu dáng, thông tin rõ ràng và sự phù hợp
            để mỗi lựa chọn đều đáng tin cậy.
          </p>

        </article>


        <article class="about-value-card about-reveal about-reveal-card" style="--reveal-delay: 380ms;">

          <span class="about-value-number">
            03
          </span>

          <h3>
            Đồng hành dài lâu
          </h3>

          <p>
            Hỗ trợ đơn hàng, bảo hành điện tử và chăm sóc khách
            hàng xuyên suốt sau khi mua sắm.
          </p>

        </article>

      </div>

    </div>

  </section>


  <section class="about-contact-section">

    <div class="velora-container">

      <div class="about-contact-card about-reveal about-reveal-up">

        <div>

          <span class="about-eyebrow">
            GẶP GỠ VELORA
          </span>

          <h2>
            Chúng tôi luôn sẵn sàng lắng nghe bạn
          </h2>

          <p>
            41A – Trường Đại học Tài nguyên và Môi trường
            Hà Nội, Phú Diễn, Bắc Từ Liêm, Hà Nội.
          </p>

        </div>


        <a href="tel:19001900" class="about-contact-phone">
          <span>
            Hotline
          </span>

          <strong>
            1900 1900
          </strong>
        </a>

      </div>

    </div>

  </section>

</main>

@endsection