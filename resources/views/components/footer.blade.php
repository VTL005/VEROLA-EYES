<footer class="site-footer">

    <div class="velora-container footer-main">

        <div>

            <h3 class="footer-title">
                VELORA EYES
            </h3>

            <p>
                Kính mắt thời trang, tư vấn thị lực
                và trải nghiệm mua sắm hiện đại.
            </p>

            <p class="mb-0">
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

    </div>


    <div class="footer-bottom">

        <div class="velora-container">

            © {{ date('Y') }} VELORA Eyes.
            All rights reserved.

        </div>

    </div>

</footer>