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

  @stack('styles')

</head>


<body>

  @include('components.header')

  {{-- =========================================================
    WISHLIST TOAST
========================================================= --}}

  @if(session('wishlist_success'))

  <div class="wishlist-toast" role="status" aria-live="polite">

    <span class="wishlist-toast-icon">
      ✓
    </span>


    <span class="wishlist-toast-message">
      {{ session('wishlist_success') }}
    </span>

  </div>

  @endif
  <main>

    @if(session('success'))

    <div class="velora-container" style="padding-top:20px;">

      <div class="alert alert-success">

        {{ session('success') }}

      </div>

    </div>

    @endif


    @if(session('error'))

    <div class="velora-container" style="padding-top:20px;">

      <div class="alert alert-danger">

        {{ session('error') }}

      </div>

    </div>

    @endif


    @if($errors->any())

    <div class="velora-container" style="padding-top:20px;">

      <div class="alert alert-danger">

        <strong>
          Vui lòng kiểm tra lại thông tin:
        </strong>

        <ul>

          @foreach($errors->all() as $error)

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
========================================================= --}}

@auth

    @if (
        auth()->user()->isCustomer()
        && Route::has('customer.chat.index')
        && !request()->routeIs('customer.chat.*')
    )

        <a
            href="{{ route('customer.chat.index') }}"
            class="customer-floating-chat"
            aria-label="Tư vấn trực tuyến"
            title="Tư vấn trực tuyến"
        >

            <span class="customer-floating-chat-icon">

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"
                    ></path>

                    <path d="M8 9h8"></path>
                    <path d="M8 13h5"></path>
                </svg>

            </span>

            <span class="customer-floating-chat-text">
                Tư vấn
            </span>

        </a>

    @endif

@endauth

@vite('resources/js/app.js')
@stack('scripts')

</body>

</html>