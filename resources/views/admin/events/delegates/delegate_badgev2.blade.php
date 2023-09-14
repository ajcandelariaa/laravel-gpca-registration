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
            /* font-style: italic; */
        }

        .company-name{
            margin-top: -15px;
            font-weight: bold
        }

        .middle{
            position: relative;
            top: 72%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* margin-left: 70px; */
        }
    </style>
</head>

<body style="width: {{ $finalWidth }}; height: {{ $finalHeight }}">
    <div class="badges">
        <div class="badge front" style="width: {{ $finalWidth/2 }}; height: {{ $finalHeight }}">
            <div class="middle" style="width: {{ $finalWidth/2 }}">
                <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
                <p class="job-title">{{ $job_title }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>
        </div>
    
        <div class="badge back" style="width: {{ $finalWidth/2 }}; height: {{ $finalHeight }}">
            <div class="middle" style="width: {{ $finalWidth/2 }}">
                <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
                <p class="job-title">{{ $job_title }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>
        </div>
    </div>
</body>

</html>
