{{-- <x-mail::message>
# Introduction

<h1>Good Day {{ $details['name'] }}</h1>

<x-mail::button :url="''">
Button Text
</x-mail::button>

<p>Job Title: {{ $details['job_title'] }}</p>
<p>Company: {{ $details['company_name'] }}</p>

Regards,<br>
GPCA
</x-mail::message> --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Dear {{ $details['name'] }}</p>
    <p>Greetings from GPCA!</p>
    <p>Thank you for registering to attend the {{ $details['eventName'] }} to be held from {{ $details['startDate'] }} to {{ $details['endDate'] }} {{ $details['year'] }} at the {{ $details['location'] }}.</p>
    <p>Please note that your registration is confirmed but admission to the forum will only be granted upon confirmation of payment receipt. Payment can be made via credit card or bank transfer.</p>
    <p>A summary of your booking confirmation is given below. Please collect your badge from the FAST-TRACK DELEGATE COUNTER that will be clearly sign posted in the registration area when you arrive at the event. </p>

    <a href="{{ $details['invoiceLink'] }}" target="_blank">Download Invoice</a>

    <p>Scan the QR Code below for your badge or click this <a href="{{ $details['badgeLink'] }}" target="_blank">link</a></p>
    {!! QrCode::size(400)->generate($details['badgeLink']); !!}
    <br>
    
    <p>Kind Regards,</p>
    <p>GPCA Team</p>
</body>
</html>