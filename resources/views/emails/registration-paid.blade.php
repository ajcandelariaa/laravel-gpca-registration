<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p>
    <strong>Your registration is confirmed and please see below your booking confirmation for your reference.</strong>
</p>

<p>To collect your badge onsite, please present your business card as a reference to ease the verification process. Your badge details will appear as the below. To request changes, kindly respond to this email at the earliest, to rectify your badge.</p>

<span>
    Delegate Full name: <strong>{{  $details['name'] }}</strong>
    <br>
    Job title: <strong>{{  $details['jobTitle'] }}</strong>
    <br>
    Company name: <strong>{{  $details['companyName'] }}</strong>
    <br>
    Amount paid: <strong>{{  $details['amountPaid'] }}</strong>
    <br>
    Transaction ID: <strong>{{  $details['transactionId'] }}</strong>
    <br>
</span>

<br>

<h1>GENERAL INFORMATION</h1>

<h3>INVITATION LETTER</h3>

<p>Non-GCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> must process their tourist visa and can notify the GPCA office for assistance in issuing the Invitation letter. Visa applications for some nationalities can be submitted online at <a href="https://visa.visitsaudi.com" target="_blank">https://visa.visitsaudi.com</a></p>

<p>Once you have determined your eligibility, sign up, create an account, and begin the application process. Upon completion, you will receive an email with a copy of your e-visa and insurance. <br> <a href="https://visa.visitsaudi.com/Login" target="_blank">https://visa.visitsaudi.com/Login</a></p>

<h3>HOTEL AND ACCOMMODATION</h3>

<p>For your hotel and accommodation, GPCA attendees can avail of special rates <a href="https://www.marriott.com/event-reservations/reservation-link.mi?id=1679226955793&key=GRP&app=resvlink" target="_blank">here</a>.</p>

<ul>
    <li>SAR 650++ per night including breakfast and internet.</li>
    <li>Special rates are subject to availability.</li>
    <li>All rates are valid only from May 13 – 18 2023.</li>
</ul>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>