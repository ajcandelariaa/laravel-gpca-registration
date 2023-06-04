<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Delegate Badge Type</title>

    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        @page {
            margin: 0;
            padding: 0;
        }

        .badge{
            text-align: center;
        }

        .front{
            float: left;
        }
        .back{
            float: right;
        }

        .name{
            font-weight: bold;
            font-size: 18px;
        }

        .job-title{
            margin-top: -5px;
            font-style: italic;
        }

        .company-name{
            margin-top: -15px;
            font-weight: bold
        }
        
        .badge-type{
            /* color: #fff;
            background-color: #000; */
            padding: 16px 0px;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 0px;
            text-align: center;
            text-transform: uppercase
        } 

        .link{
            padding: 16px 0px;
            font-weight: bold;
            margin-bottom: 0px;
            text-align: center;
        }

        .header{
            position: absolute;
            top: 0%;
        }

        .middle{
            position: relative;
            top: 64%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .footer{
            position: absolute;
            bottom: 0%;
        }
    </style>
</head>

<body style="width: {{ $finalWidth }}; height: {{ $finalHeight }}">
    <div class="badges">
        <div class="badge front" style="width: {{ $finalWidth/2 }}; height: {{ $finalHeight }}">
            <div class="header" style="width: {{ $finalWidth/2 }}">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($frontBanner)) }}" alt="banners" style="width: {{ $finalWidth/2 }}">
            </div>
            <div class="middle" style="width: {{ $finalWidth/2 }}">
                <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
                <p class="job-title">{{ $job_title }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>
            <div class="footer" style="width: {{ $finalWidth/2 }}">
                <p class="badge-type" style="color: {{ $frontTextColor }}; background-color: {{ $frontTextBGColor }}">{{ $frontText }}</p>
            </div>
        </div>
    
        <div class="badge back" style="width: {{ $finalWidth/2 }}; height: {{ $finalHeight }}">
            <div class="header" style="width: {{ $finalWidth/2 }}">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($backBanner)) }}" alt="banners" style="width: {{ $finalWidth/2 }}">
            </div>
            <div class="middle" style="width: {{ $finalWidth/2 }}">
                <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
                <p class="job-title">{{ $job_title }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>
            {{-- <div class="footer" style="width: {{ $finalWidth/2 }}">
                <p class="link" style="color: {{ $textColor }};">{{ $link }}</p>
            </div> --}}
            <div class="footer" style="width: {{ $finalWidth/2 }}">
                <p class="badge-type" style="color: {{ $backTextColor }}; background-color: {{ $backTextBGColor }}">{{ $backText }}</p>
            </div>
        </div>
    </div>
</body>

</html>
