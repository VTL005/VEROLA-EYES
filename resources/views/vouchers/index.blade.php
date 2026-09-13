@extends('layouts.app')


@section(
'title',
'Kho voucher - VELORA Eyes'
)


@push('styles')

<link rel="stylesheet" href="{{ asset('css/voucher-wallet.css') }}">

@endpush


@section('content')

<section class="voucher-wallet-hero">

  <div class="velora-container">

    <span class="voucher-wallet-kicker">
      VELORA PRIVILEGES
    </span>

    <h1>
      Kho voucher
    </h1>

    <p>
      Thu thập voucher dùng ngay hoặc
      lưu trước để sử dụng trong thời gian tới.
    </p>

  </div>

</section>


<section class="voucher-wallet-section">

  <div class="velora-container">


    @if(session('success'))

    <div class="alert alert-success">
      {{ session('success') }}
    </div>

    @endif


    @if(session('error'))

    <div class="alert alert-danger">
      {{ session('error') }}
    </div>

    @endif


    <div class="voucher-wallet-heading">

      <div>

        <span class="voucher-wallet-eyebrow">
          ƯU ĐÃI DÀNH CHO BẠN
        </span>

        <h2>
          Voucher có thể thu thập
        </h2>

        <p>
          Bao gồm voucher đang sử dụng
          và voucher sắp có hiệu lực.
        </p>

      </div>


      <span class="voucher-wallet-count">

        {{ $vouchers->count() }}
        voucher

      </span>

    </div>


    <div class="voucher-wallet-grid">

      @forelse($vouchers as $voucher)

      @php

      $isSaved = $savedVoucherIds
      ->contains($voucher->id);

      $isUpcoming = now()->lessThan(
      $voucher->starts_at
      );

      $remainingUsage =
      $voucher->usage_limit === null
      ? null
      : max(
      0,
      (int) $voucher->usage_limit
      - (int) $voucher->usage_count
      );

      @endphp


      <article class="
                        voucher-wallet-card
                        {{ $isSaved ? 'is-saved' : '' }}
                        {{ $isUpcoming ? 'is-upcoming' : '' }}
                    ">

        <div class="voucher-wallet-ticket-edge">

          <span>
            V
          </span>

        </div>


        <div class="voucher-wallet-content">

          <div class="voucher-wallet-card-top">

            <div>

              <span class="voucher-wallet-brand">
                VELORA EYES
              </span>

              <h3>

                @if(
                $voucher->discount_type
                === 'percentage'
                )

                Giảm
                {{ number_format(
                                            (float) $voucher
                                                ->discount_value,
                                            0,
                                            ',',
                                            '.'
                                        ) }}%

                @else

                Giảm
                {{ number_format(
                                            (float) $voucher
                                                ->discount_value,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                @endif

              </h3>

            </div>


            <div class="voucher-wallet-statuses">

              @if($isUpcoming)

              <span class="
                                            voucher-wallet-status-badge
                                            is-upcoming
                                        ">
                Dùng sau
              </span>



              @endif


              @if($isSaved)

              <span class="voucher-wallet-saved-badge">
                Đã lưu
              </span>

              @endif

            </div>

          </div>


          <div class="voucher-wallet-code">

            <span>
              Mã voucher
            </span>

            <strong>
              {{ $voucher->code }}
            </strong>

          </div>


          <ul class="voucher-wallet-conditions">

            <li>

              Đơn tối thiểu

              <strong>

                {{ number_format(
                                        (float) $voucher
                                            ->minimum_order_amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

              </strong>

            </li>


            @if($isUpcoming)

            <li>

              Có thể dùng từ

              <strong>

                {{ $voucher->starts_at
                                            ->format('d/m/Y H:i') }}

              </strong>

            </li>

            @endif


            <li>

              Hạn sử dụng

              <strong>

                {{ $voucher->ends_at
                                        ->format('d/m/Y H:i') }}

              </strong>

            </li>


            @if($remainingUsage !== null)

            <li>

              Số lượng còn lại

              <strong>
                {{ $remainingUsage }}
              </strong>

            </li>

            @endif

          </ul>


          <div class="voucher-wallet-actions">

            @if($isSaved)

            @if($isUpcoming)

            <button type="button" class="btn btn-outline" disabled>
              Đã lưu - Dùng sau
            </button>

            @else

            <form action="{{ route(
                                            'vouchers.use',
                                            $voucher
                                        ) }}" method="POST">

              @csrf

              <button type="submit" class="btn btn-primary">
                Dùng ngay
              </button>

            </form>

            @endif

            @else

            <form action="{{ route(
                                        'vouchers.claim',
                                        $voucher
                                    ) }}" method="POST">

              @csrf

              <button type="submit" class="btn btn-primary">

                {{ $isUpcoming
                                            ? 'Lưu để dùng sau'
                                            : 'Lưu voucher' }}

              </button>

            </form>

            @endif

          </div>

        </div>

      </article>

      @empty

      <div class="voucher-wallet-empty">

        <div class="voucher-wallet-empty-icon">
          %
        </div>

        <h2>
          Chưa có voucher
        </h2>

        <p>
          Các chương trình ưu đãi mới
          sẽ được hiển thị tại đây.
        </p>

        <a href="{{ route('products.index') }}" class="btn btn-primary">
          Tiếp tục mua sắm
        </a>

      </div>

      @endforelse

    </div>

  </div>

</section>

@endsection