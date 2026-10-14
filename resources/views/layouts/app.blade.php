<!DOCTYPE html>
<html lang="vi">

<head>

  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta name="description" content="@yield(
            'meta_description',
            'VELORA Eyes - Kính mắt thời trang và chăm sóc thị lực.'
        )">

  <title>
    @yield('title', 'VELORA Eyes')
  </title>

  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/velora-design-system.css') }}">
  <link rel="stylesheet" href="{{ asset('css/velora-components.css') }}">
  <link rel="stylesheet" href="{{ asset('css/velora-motion.css') }}">
  <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
  <link rel="stylesheet" href="{{ asset('css/velora-vision-care.css') }}">
  <link rel="stylesheet" href="{{ asset('css/velora-interactions.css') }}">
  <link rel="stylesheet" href="{{ asset('css/velora-showcase-motion.css') }}">
  <link rel="stylesheet" href="{{ asset('css/velora-lucian-extras.css') }}">

  @stack('styles')

</head>


<body>

  @include('components.header')


  {{-- =========================================================
        WISHLIST TOAST
    ========================================================== --}}

  @if (session('wishlist_success'))

  <div class="wishlist-toast" role="status" aria-live="polite" data-velora-toast data-toast-type="success">

    <span class="wishlist-toast-icon">
      ✓
    </span>

    <span class="wishlist-toast-message">
      {{ session('wishlist_success') }}
    </span>

  </div>

  @endif


  <main>

    @if (session('success'))

    <div class="velora-container" style="padding-top:20px;">

      <div class="alert alert-success" data-velora-toast data-toast-type="success">
        {{ session('success') }}
      </div>

    </div>

    @endif


    @if (session('error'))

    <div class="velora-container" style="padding-top:20px;">

      <div class="alert alert-danger" data-velora-toast data-toast-type="error">
        {{ session('error') }}
      </div>

    </div>

    @endif


    @if ($errors->any())

    <div class="velora-container" style="padding-top:20px;">

      <div class="alert alert-danger">

        <strong>
          Vui lòng kiểm tra lại thông tin:
        </strong>

        <ul>

          @foreach ($errors->all() as $error)

          <li>
            {{ $error }}
          </li>

          @endforeach

        </ul>

      </div>

    </div>

    @endif


    @yield('content')

  </main>


  @include('components.footer')


  {{-- =========================================================
        CUSTOMER FLOATING CHAT
    ========================================================== --}}

  @if (
  Route::has('customer.chat.index')
  && ! request()->routeIs('customer.chat.*')
  && (! auth()->check() || auth()->user()->isCustomer())
  )

  @php

  /*
  * Khi đang xem chi tiết sản phẩm,
  * lấy sản phẩm hiện tại từ route.
  */
  $floatingChatProduct =
  request()->routeIs('products.show')
  ? request()->route('product')
  : null;


  /*
  * Route Model Binding thường trả về
  * đối tượng Product.
  *
  * Trường hợp route chỉ trả về ID,
  * hệ thống vẫn xử lý được.
  */
  $floatingChatProductId =
  $floatingChatProduct instanceof \App\Models\Product
  ? $floatingChatProduct->getKey()
  : (
  is_numeric($floatingChatProduct)
  ? (int) $floatingChatProduct
  : null
  );

  $floatingChatUrl = auth()->check()
  ? route(
      'customer.chat.index',
      $floatingChatProductId
          ? ['product' => $floatingChatProductId]
          : []
    )
  : route('login');

  @endphp


  <a href="{{ $floatingChatUrl }}" class="customer-floating-chat" aria-label="Tư vấn trực tuyến" title="Tư vấn trực tuyến" data-velora-tooltip="Tư vấn trực tuyến">

    <span class="customer-floating-chat-icon">

      <svg viewBox="0 0 24 24" aria-hidden="true">

        <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"></path>

        <path d="M8 9h8"></path>

        <path d="M8 13h5"></path>

      </svg>

    </span>


    <span class="customer-floating-chat-text">
      Tư vấn
    </span>

  </a>

  @endif


  <script src="{{ asset('js/velora-motion.js') }}" defer></script>
  <script src="{{ asset('js/adaptive-images.js') }}" defer></script>
  <script src="{{ asset('js/velora-interactions.js') }}" defer></script>
  <script src="{{ asset('js/velora-showcase-motion.js') }}" defer></script>
  <script src="{{ asset('js/velora-lucian-extras.js') }}" defer></script>

  @vite('resources/js/app.js')

  @stack('scripts')

</body>

</html>
