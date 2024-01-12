<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from March 5 to 6 2024 at Pullman Dubai Downtown.</p>

<p class="sub" style="margin-top: 15px;"><strong>Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements.</strong></p>

<p class="title" style="margin-top: 20px;">Delegate Information:</p>
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

<p class="sub" style="margin-top: 20px; text-decoration: underline"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon your arrival, kindly make your way to the registration desk to collect your event badge. This badge will serve as your access pass, allowing entry to all sessions and networking events scheduled throughout the day.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline"><strong>Hotel booking</strong></p>
<p class="sub" style="margin-top: 5px;">For the hotel accommodation, guests can send an email to reservations.pullmandubaidowntown@accor.com with details in order for the hotel’s reservations team to make the reservation in the system and send the booking confirmation along with the SecurePay Link (link for online card payment).</p>
<ul class="event-list">
<li style="margin-top: 5px;">Guests should mention the booking name <strong>“GPCA RC14001 Workshop”</strong> and booking code <strong>“2403GULFPE”</strong> to avail of the special rates.</li>
<li>AED 670 – AED 720+++ per night includes breakfast and internet.</li>
<li>All room reservations must be completed latest by 20<sup>th</sup> February 2024</li>
</ul>

<p class="sub" style="margin-top: 15px;">For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p class="sub" style="margin-top: 15px;">Best regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>