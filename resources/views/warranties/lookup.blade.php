@extends('layouts.app')


@section('title', 'Tra cứu bảo hành - VELORA Eyes')


@section('content')

<section class="warranty-lookup-section">

    <div class="velora-container">

        <div class="warranty-lookup-wrapper">


            <div class="warranty-lookup-intro">

                <span class="hero-kicker">
                    WARRANTY LOOKUP
                </span>

                <h1>
                    Tra cứu bảo hành
                </h1>

                <p>
                    Nhập mã bảo hành điện tử
                    để kiểm tra thời hạn và trạng thái
                    bảo hành sản phẩm VELORA Eyes.
                </p>


                <div class="warranty-lookup-example">

                    <span>
                        Ví dụ mã bảo hành
                    </span>

                    <strong>
                        BH-VLR-000001
                    </strong>

                </div>

            </div>



            <div class="warranty-lookup-card">

                <h2>
                    Kiểm tra bảo hành
                </h2>


                <p>
                    Mã bảo hành được cung cấp
                    trên thẻ bảo hành điện tử.
                </p>


                @if(session('error'))

                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>

                @endif


                <form
                    action="{{ route(
                        'warranties.lookup'
                    ) }}"
                    method="POST"
                >

                    @csrf


                    <div class="form-group">

                        <label
                            for="warranty_code"
                            class="form-label"
                        >
                            Mã bảo hành
                        </label>


                        <input
                            type="text"
                            id="warranty_code"
                            name="warranty_code"
                            class="form-control @error('warranty_code') input-error @enderror"
                            value="{{ old('warranty_code') }}"
                            placeholder="BH-VLR-000001"
                            autocomplete="off"
                            autofocus
                        >


                        @error('warranty_code')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                        style="width:100%;"
                    >
                        Tra cứu bảo hành
                    </button>

                </form>

            </div>

        </div>

    </div>

</section>

@endsection