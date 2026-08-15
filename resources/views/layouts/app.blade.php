<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="@yield(
            'meta_description',
            'VELORA Eyes - Kính mắt thời trang và chăm sóc thị lực.'
        )"
    >

    <title>
        @yield('title', 'VELORA Eyes')
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}"
    >

    @stack('styles')

</head>


<body>

    @include('components.header')


    <main>

        @if(session('success'))

            <div
                class="velora-container"
                style="padding-top:20px;"
            >

                <div class="alert alert-success">

                    {{ session('success') }}

                </div>

            </div>

        @endif


        @if(session('error'))

            <div
                class="velora-container"
                style="padding-top:20px;"
            >

                <div class="alert alert-danger">

                    {{ session('error') }}

                </div>

            </div>

        @endif


        @if($errors->any())

            <div
                class="velora-container"
                style="padding-top:20px;"
            >

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


    @stack('scripts')

</body>

</html>