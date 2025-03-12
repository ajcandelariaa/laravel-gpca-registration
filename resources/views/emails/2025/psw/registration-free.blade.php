<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 20px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 20px;">Thank you for registering to attend the GPSN <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from April 30 to May 01, 2025, at {{ $details['eventLocation'] }}.</p>

<p class="sub" style="margin-top: 20px;"><strong>Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements.</strong></p>

<p class="sub" style="margin-top: 20px;"><strong>Your registration details as follows:</strong></p>
<p class="sub">Full name: {{  $details['name'] }}</p>
<p class="sub">Job title: {{  $details['jobTitle'] }}</p>
<p class="sub">Company name: {{  $details['companyName'] }}</p>
@if ($sendInvoice)
<p class="sub">Amount paid: $ {{ number_format($details['amountPaid'], 2, '.', ',') }}</p>
@endif
<p class="sub">Transaction ID: {{  $details['transactionId'] }}</p>

@if ($sendInvoice)
<br>
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
@endif

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon your arrival, kindly make your way to the registration desk to collect your event badge. This badge will serve as your access pass, allowing entry to all sessions and networking events scheduled throughout the day.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Hotel booking</strong></p>
<p class="sub" style="margin-top: 5px;">For the hotel accommodation, guests can send an email to reservations.pullmandubaidowntown@accor.com with details in order for the hotel’s reservations team to make the reservation in the system and send the booking confirmation along with the SecurePay Link (link for online card payment).</p>

<ul class="event-list">
    <li style="margin-top: 5px;">	Guests should mention the booking name <strong>“GPCA Process Safety Workshop”</strong> and booking code <a href="https://accorce2.oraclehospitality.eu-frankfurt-1.ocs.oraclecloud.com/OPERA9/opera/operacloud/faces/opera-cloud-index/OperaCloud"><strong>“2504GULFPE_001”</strong></a> to avail of the special rates.</li>
    <li>	AED 820 – AED 870+++ per night includes breakfast and internet.</li>
    <li>Special hotel rate includes breakfast and internet</li>
    <li>	All room reservations must be completed at the latest by 31<sup>st</sup> March 2025.</li>
</ul>

<p class="sub" style="margin-top: 20px;">Best regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>