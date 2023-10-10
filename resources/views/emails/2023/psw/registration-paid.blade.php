<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from {{ $details['eventDates'] }} at {{ $details['eventLocation'] }}.</p>

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

<span><strong>Hotel booking</strong></span><br><span>For the hotel accommodation, please click the <a href="https://www.ihg.com/crowneplaza/hotels/us/en/manama/mnaba/hoteldetail?fromRedirect=true&qSrt=sBR&qIta=99801505&icdv=99801505&qSlH=MNABA&qGrpCd=GU1&setPMCookies=true&qSHBrC=CP&qDest=Diplomatic%20Area%20317%2C%20Manama%2C%20BH&srb_u=1" target="_blank">hotel booking reservation</a> where we have special rates.</span>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>