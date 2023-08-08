<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> which will be held from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p style="color: red;">
    Kindly note that your registration is not yet confirmed. Please settle your payment through bank transfer prior to the event to avoid any inconvenience onsite.
</p>

<p>To take advantage of the early bird discount, please ensure to settle your invoice on or before {{ $details['earlyBirdValidityDate'] }}. Standard rate will be applied after this day.</p>

<p>Your registration details as follows:</p>

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

<p>To request any changes, kindly respond to this email at the earliest to rectify your badge.</p>

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