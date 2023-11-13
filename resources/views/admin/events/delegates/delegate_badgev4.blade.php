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
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-align: center;
        }

        @page {
            margin: 0;
            padding: 0;
        }

        .badges {
            position: relative;
        }

        .front {
            position: absolute;
            left: 30%;
        }

        .back {
            position: absolute;
            left: 67%;

            /* plus 1 mamaya if nagkulang */
        }

        .name {
            font-size: 12pt;
        }

        .job-title {
            margin-top: -12px;
            font-size: 12pt;
        }

        .company-name {
            margin-top: -12px;
            font-weight: bold;
            font-size: 12pt;
        }

        .middle {
            width: 270px;
            margin-top: 310px;
            height: 150px; /** exact px height of the delegate details **/
            /* border: 1px solid black; */
            position: absolute;
        }

        .container{
            position: relative;
            top: 38%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body style="width: {{ $finalWidth }}; height: {{ $finalHeight }}">
    <div class="badges">
        <div class="front middle">
            <div class="container">
                <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
                <p class="job-title">{{ $job_title }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>
        </div>
        

        <div class="back middle">
            <div class="container">
                <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
                <p class="job-title">{{ $job_title }}</p>
                <p class="company-name">{{ $companyName }}</p>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>
</html>
