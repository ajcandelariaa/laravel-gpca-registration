<x-mail::message>
<p class="sub" >Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at {{ $details['eventLocation'] }}</p>

<p class="sub" style="margin-top: 15px;"><strong>Your registration has been confirmed, and we are pleased to provide you with the booking confirmation below for your reference. </strong></p>

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

<p class="sub" style="margin-top: 15px; text-decoration: underline;"><strong>Collection of badges</strong></p>

<p class="sub" style="margin-top: 15px;">Upon your arrival, kindly make your way to the registration desk to collect your event badge. This badge will serve as your access pass, allowing entry to all sessions and networking events scheduled throughout the day.</p>

<p class="sub" style="margin-top: 15px;">Best Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>