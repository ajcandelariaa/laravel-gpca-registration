<x-mail::message>
<p class="sub" >Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}</p>

<p class="sub" style="margin-top: 15px;"><strong>Your registration is confirmed and please see below your booking confirmation for your reference. </strong></p>

<p class="sub" style="margin-top: 15px;">To collect your badge onsite, please present your business card as a reference to ease the verification process. Your badge details will appear as the below. To request changes, kindly respond to this email at the earliest, to rectify your badge.</p>

<p class="sub" style="margin-top: 15px;">Your registration details as follows:</p>

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

<p class="sub" style="margin-top: 15px;">To request any updates on your registration details, kindly contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> to rectify your badge. </p>

<p class="sub" style="margin-top: 15px;">For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>