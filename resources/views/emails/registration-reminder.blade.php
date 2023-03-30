<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Reminder</title>
</head>
<body>
    <p>Dear {{ $details['name'] }}</p>
    <p>Greetings from GPCA!</p>
    <p>This is to remind your payment for your registration to attend the {{ $details['eventName'] }} to be held from {{ $details['startDate'] }} to {{ $details['endDate'] }} {{ $details['year'] }} at the {{ $details['location'] }}.</p>

    <br>
    
    <p>Kind Regards,</p>
    <p>GPCA Team</p>
</body>
</html>