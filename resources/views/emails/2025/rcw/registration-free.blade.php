<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from 4-5 February 2025 at the {{ $details['eventLocation'] }}.</p>

<p class="sub" style="margin-top: 15px;">Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements for your optimal event experience.</p>

<p class="sub" style="margin-top: 15px;">Your registration details as follows:</p>

<p class="sub" style="margin-top: 20px;"><strong>Delegate Information</strong></p>
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

<p class="sub" style="margin-top: 15px;"><strong>HOTEL BOOKING</strong></p>
<p class="sub" style="margin-top: 5px;">We have secured a special discount rate for our attendees during the Workshop. Kindly click the <a href="https://www.gpca.org.ae/wp-content/uploads/2025/01/ROOM-RESERVATION-FORM-GPCA-RC-Codes-Workshop.pdf" target="_blank">link</a> to finalize your booking and send your request to <a href="mailto:reservation@gulfhotelbahrain.com">reservation@gulfhotelbahrain.com</a>.</p>

<p class="sub" style="margin-top: 15px;">To request any updates on your registration details, kindly contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> to rectify your badge. </p>

<p class="sub" style="margin-top: 15px;">For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p class="sub" style="margin-top: 15px;">Kind regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>