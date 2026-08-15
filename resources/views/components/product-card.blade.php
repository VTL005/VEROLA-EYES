<article class="product-card">

    <a
        href="{{ route('products.show', $product) }}"
        class="product-card-image"
    >

        @if($product->primaryImage)

            <img
                src="{{ asset(
                    $product->primaryImage->image_path
                ) }}"
                alt="{{ $product->name }}"
            >

        @else

            <img
                src="{{ asset('images/no-image.png') }}"
                alt="{{ $product->name }}"
            >

        @endif


        @if(
            $product->sale_price
            &&
            $product->sale_price < $product->price
        )

            @php

                $discountPercent = round(
                    (
                        (
                            $product->price
                            - $product->sale_price
                        )
                        / $product->price
                    )
                    * 100
                );

            @endphp


            <span
                class="badge badge-danger"
                style="
                    position:absolute;
                    top:14px;
                    left:14px;
                "
            >
                -{{ $discountPercent }}%
            </span>

        @endif

    </a>


    <div class="product-card-body">

        @if($product->category)

            <a
                href="{{ route(
                    'categories.show',
                    $product->category
                ) }}"
                class="product-category"
            >
                {{ $product->category->name }}
            </a>

        @endif


        <h3 class="product-title">

            <a href="{{ route('products.show', $product) }}">
                {{ $product->name }}
            </a>

        </h3>


        <div class="mb-2">

            @if(
                $product->sale_price
                &&
                $product->sale_price < $product->price
            )

                <span class="product-old-price">

                    {{ number_format(
                        (float) $product->price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </span>


                <span class="product-price">

                    {{ number_format(
                        (float) $product->sale_price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </span>

            @else

                <span class="product-price">

                    {{ number_format(
                        (float) $product->price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </span>

            @endif

        </div>


        @if(
            method_exists($product, 'isReadyForSale')
            && $product->isReadyForSale()
        )

            <span class="badge badge-success mb-2">
                Sẵn sàng đặt hàng
            </span>

        @else

            <span class="badge mb-2">
                Liên hệ tư vấn
            </span>

        @endif


        <div class="mt-2">

            <a
                href="{{ route('products.show', $product) }}"
                class="btn btn-outline"
                style="width:100%;"
            >
                Xem chi tiết
            </a>

        </div>

    </div>

</article>