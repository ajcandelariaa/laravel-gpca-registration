<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Delegate Badge Type</title>

    <style>
        html,
        body {
            margin: 15px;
            padding: 10px;
            font-family: sans-serif;
        }

        .badge{
            width: 450px;
            margin: 0 auto;
            border: 1px solid black;
            text-align: center;
        }

        img {
            width: 100%;
        }

        .name{
            font-weight: bold;
            margin-top: 128px;
            font-size: 18px;
            line-height: 28px;
        }

        .job-title{
            font-style: italic;
            margin-top: 20px;
        }

        .company-name{
            font-weight: bold
        }

        .badge-type{
            color: #fff;
            background-color: #000;
            padding: 16px 0px;
            font-weight: bold;
            margin-top: 128px;
            margin-bottom: 0px;
        }

    </style>
</head>

<body>
    <div class="badge">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/assets/images/reg-banner.png'))) }}" alt="banners">
        <p class="name">{{ $salutation }} {{ $first_name }} {{ $middle_name }} {{ $last_name }}</p>
        <p class="job-title">{{ $job_title }}</p>
        <p class="company-name">{{ $companyName }}</p>
        <p class="badge-type">{{ $badge_type }}</p>
    </div>
</body>

</html>
