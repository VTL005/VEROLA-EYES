<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Đánh giá sản phẩm - VELORA Eyes
    </title>
</head>

<body>

    <h1>
        Đánh giá sản phẩm
    </h1>


    <p>

        <a
            href="{{ route(
                'products.show',
                $product
            ) }}"
        >
            ← Quay lại sản phẩm
        </a>

    </p>


    @if($errors->any())

        <div style="color:red;">

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <hr>


    <h2>
        {{ $product->name }}
    </h2>


    <p>
        SKU:
        {{ $product->sku }}
    </p>


    <form
        action="{{ route(
            'reviews.store',
            $product
        ) }}"
        method="POST"
    >

        @csrf


        <div>

            <label>
                Số sao
            </label>

            <br>

            <select
                name="rating"
                required
            >

                <option value="">
                    -- Chọn số sao --
                </option>


                <option
                    value="5"
                    {{ old('rating') == '5'
                        ? 'selected'
                        : '' }}
                >
                    5 sao - Rất tốt
                </option>


                <option
                    value="4"
                    {{ old('rating') == '4'
                        ? 'selected'
                        : '' }}
                >
                    4 sao - Tốt
                </option>


                <option
                    value="3"
                    {{ old('rating') == '3'
                        ? 'selected'
                        : '' }}
                >
                    3 sao - Bình thường
                </option>


                <option
                    value="2"
                    {{ old('rating') == '2'
                        ? 'selected'
                        : '' }}
                >
                    2 sao - Chưa tốt
                </option>


                <option
                    value="1"
                    {{ old('rating') == '1'
                        ? 'selected'
                        : '' }}
                >
                    1 sao - Không hài lòng
                </option>

            </select>

        </div>


        <br>


        <div>

            <label>
                Nội dung đánh giá
            </label>

            <br>

            <textarea
                name="comment"
                rows="6"
                cols="60"
                maxlength="500"
                placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."
            >{{ old('comment') }}</textarea>

            <p>
                Tối đa 500 ký tự.
            </p>

        </div>


        <button type="submit">
            Gửi đánh giá
        </button>

    </form>

</body>

</html>