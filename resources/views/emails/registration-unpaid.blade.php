<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> which will be held on {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p style="color: red;">This is a kind reminder to process your registration invoice to complete your delegate registration.</p>

<p>To take advantage of the early bird discount, please ensure to settle your invoice on or before {{ $details['earlyBirdValidityDate'] }}. Standard rate will be applied after {{ $details['earlyBirdValidityDate'] }}.</p>

<p>
    Note: <strong><em>NO BADGE</em></strong> will be issued unless payment has been settled and confirmed. Onsite payment is available on a <strong><em>CASH BASIS</em></strong> only.
</p>

<p>Your registration details as follows:</p>

<span>
    Delegate Full name: <strong>{{  $details['name'] }}</strong>
    <br>
    Job title: <strong>{{  $details['jobTitle'] }}</strong>
    <br>
    Company name: <strong>{{  $details['companyName'] }}</strong>
    @if ($sendInvoice)
    <br>
    Amount paid: <strong>$ {{ number_format($details['amountPaid'], 2, '.', ',') }}</strong>
    @endif
    <br>
    Transaction ID: <strong>{{  $details['transactionId'] }}</strong>
</span>
@if ($sendInvoice)
<br><br>
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
<span>&nbsp;</span>
@else
<br><br>
@endif

<p>To request changes, kindly respond to this email at the earliest, to rectify your badge.</p>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>