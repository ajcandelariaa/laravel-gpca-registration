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
    <br>
    Amount paid: <strong>$ {{ number_format($details['amountPaid'], 2, '.', ',') }}</strong>
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

<p>To collect your badge onsite, please present your business card as a reference to facilitate the verification process. To request changes, kindly respond to this email at the earliest, to rectify your badge. </p>

<h3>GENERAL INFORMATION</h3>

<p>Non – GPCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, must process their own visa. Visa applications can be submitted online at <a href="https://www.evisa.gov.bh/">https://www.evisa.gov.bh/</a></p>

<p>For the hotel accommodation, kindly <a href="http://gpcaresponsiblecare.com/wp-content/uploads/2023/07/The-Ritz-Carlton-Bahrain-Reservation-Form.pdf"><strong>click</strong></a> to download the form and submit your details to Prakash at <a href="mailto:Prakash.ramaiah@ritzcarlton.com">Prakash.ramaiah@ritzcarlton.com</a> or <a href="mailto:chahrazed.jassem@ritzcarlton.com">chahrazed.jassem@ritzcarlton.com</a>. You may also contact the following numbers for any additional inquiries: 973-66388039 or 973-39957141.</p>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>