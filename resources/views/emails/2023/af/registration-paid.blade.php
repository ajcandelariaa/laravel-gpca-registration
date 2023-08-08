<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p><strong>Your registration has been confirmed. Please find below the summary of your booking confirmation.</strong></p>

<p>Upon your  arrival at the event, you can collect your badge from the fast-track delegate counter or from the main registration counter situated in exhibition hall 4 to 6 at concourse.</p>

{{-- <p>Use the below QRCode to print your own badge onsite:</p> --}}

{{-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($details['badgeLink'])) !!}" /> --}}

<br><br>

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

<h3>GENERAL INFORMATION</h3>

<h4><strong>Qatar Visa</strong></h4>
<p>Please see the <a href="https://visitqatar.com/intl-en/practical-info/visas">link</a> for further information on how to obtain the visa. Kindly notify the GPCA office if you require any assistance.</p>

<h4><strong>HOTEL ACCOMODATION</strong></h4>
<p>Experience a seamless and stress-free event journey with our expert travel partner. For more information on travel and accommodation please contact</p>
<br>
<span>Uchita Mhatre</span><br>
<span>+971553079469</span><br>
<span>umhatre@cozmotravel.com</span>
<br><br>
<span>Mohamed Reda Sabbah</span><br>
<span>+971558236738</span><br>
<span>r.sabbah@cozmotravel.com</span>
<br><br>
<span>Aziz Tinwala</span><br>
<span>+971547502008</span><br>
<span>t.aziz@cozmotravel.com</span>
<br>
<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>