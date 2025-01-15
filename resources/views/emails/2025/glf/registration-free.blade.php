<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from 28 Feburary 2024 at {{ $details['eventLocation'] }}.</p>

<p class="sub" style="margin-top: 15px;">Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements for your optimal event experience.</p>

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

<p class="sub" style="margin-top: 15px;">For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p class="sub" style="margin-top: 15px;">Best regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>