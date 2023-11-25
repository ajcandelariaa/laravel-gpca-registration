<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ $badgeName }}</title>

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: Tahoma, sans-serif;
            text-align: left;
            color: #232564;
        }

        @page {
            margin: 0;
            padding: 0;
        }

        p {
            margin: 0;
            padding: 0;
        }


        /* front badge */
        .front {
            float: left;
            position: relative;
        }

        .details {
            position: absolute;
            top: 30%;
            left: 0%;
        }

        .name {
            font-weight: bold;
            font-size: 16pt;
        }

        .job-title {
            font-weight: bold;
            font-size: 16pt;
        }

        .company-name {
            font-weight: bold;
            font-size: 16pt;
        }

        .qr-code {
            position: absolute;
            top: 60%;
            left: 33%;
            background-color: #ffffff;
            padding: 5px;
        }

        /* .badge-type {
            position: absolute;
            bottom: 5%;
            left: 0%;
            padding: 5px 10px;
        } */

        .back {
            float: right;
        }

        .seat-number {
            position: absolute;
            top: 60%;
            left: 18%;
        }

        .seat-number p{
            font-weight: bold;
            font-size: 12pt;
        }
    </style>
</head>

<body>
    <div class="badge">
        <div class="front" style="width: {{ $finalWidth / 2 }}; height: {{ $finalHeight }};">
            <div class="details">
                <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
                <p class="job-title">{{ $job_title }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>

            <div class="qr-code">
                <img src="data:image/png;base64, {!! base64_encode(
                    QrCode::format('png')->size(80)->generate($scanDelegateUrl),
                ) !!} ">
            </div>

            @if ($seatNumber != null)
                <div class="seat-number">
                    <p>{{ $seatNumber }}</p>
                </div>
            @endif
            {{-- <div class="badge-type" style="background-color:{{ $frontTextBGColor }}">
                <span style="color: {{ $frontTextColor }}">{{ $frontText }}</span>
            </div> --}}
        </div>

        <div class="back"style="width: {{ $finalWidth / 2 }}; height: {{ $finalHeight }}"></div>
    </div>
</body>

</html>
