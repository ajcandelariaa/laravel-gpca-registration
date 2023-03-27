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
    <p>Thank you for registering to attend the 16th GPCA Annual Forum to be held from Tuesday 6 December to Thursday 8 December 2022 at the Hilton Riyadh hotel in Riyadh KSA.</p>
    <p>Please note that your registration is confirmed but admission to the forum will only be granted upon confirmation of payment receipt. Payment can be made via credit card or bank transfer.</p>
    <p>A summary of your booking confirmation is given below. Please collect your badge from the FAST-TRACK DELEGATE COUNTER that will be clearly sign posted in the registration area when you arrive at the event. </p>
</body>
</html>