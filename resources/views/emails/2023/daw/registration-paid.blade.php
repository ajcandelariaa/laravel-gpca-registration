<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registration on the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> which will take place on {{ $details['eventDates'] }} at the prestigious{{ $details['eventLocation'] }}.</p>

<p>
    Your registration has been confirmed, and we are pleased to provide you with the booking confirmation below for your reference.
</p>

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

<p>For further information about the workshop agenda, speakers, and other related details, please feel free to reach out to Aastha at aastha@gpca.org.ae or contact us via phone at +971 4 451 0666 ext. 104.</p>

<p>We look forward to welcoming you to the workshop.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>