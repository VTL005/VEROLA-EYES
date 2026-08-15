<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <title>
        Nhắc lịch hẹn VELORA Eyes
    </title>
</head>

<body>

    <h1>
        VELORA Eyes
    </h1>


    <h2>
        Nhắc lịch hẹn
    </h2>


    <p>
        Xin chào
        <strong>
            {{ $appointment->customer_name }}
        </strong>,
    </p>


    <p>
        VELORA Eyes xin nhắc bạn về lịch hẹn sắp tới.
    </p>


    <hr>


    <p>

        Mã lịch hẹn:

        <strong>
            {{ $appointment->appointment_code }}
        </strong>

    </p>


    <p>

        Ngày hẹn:

        <strong>
            {{ $appointment
                ->appointment_date
                ->format('d/m/Y') }}
        </strong>

    </p>


    <p>

        Khung giờ:

        <strong>
            {{ $appointment->time_slot }}
        </strong>

    </p>


    <p>

        Dịch vụ:

        <strong>

            @switch($appointment->service_type)

                @case('eye_exam')
                    Đo / khám mắt
                    @break

                @case('recheck')
                    Tái khám
                    @break

                @case('lens_consultation')
                    Tư vấn tròng kính
                    @break

                @case('frame_consultation')
                    Tư vấn gọng kính
                    @break

                @default
                    {{ $appointment->service_type }}

            @endswitch

        </strong>

    </p>


    @if($appointment->note)

        <p>
            Ghi chú:
            {{ $appointment->note }}
        </p>

    @endif


    <hr>


    <p>
        Vui lòng đến đúng giờ để VELORA Eyes có thể phục vụ bạn tốt nhất.
    </p>


    <p>
        Nếu bạn không thể đến theo lịch đã đặt, vui lòng kiểm tra hoặc hủy lịch trên tài khoản của mình.
    </p>


    <p>
        Cảm ơn bạn đã lựa chọn VELORA Eyes.
    </p>

</body>

</html>