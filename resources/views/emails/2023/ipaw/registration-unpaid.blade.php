<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> which will be held on {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p style="color: red;">
    This is a kind reminder to process your registration invoice to complete your delegate registration.
</p>

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
    <br>
    Amount paid: <strong>$ {{ number_format($details['amountPaid'], 2, '.', ',') }}</strong>
    <br>
    Transaction ID: <strong>{{  $details['transactionId'] }}</strong>
</span>
<br><br>
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
<span>&nbsp;</span>

<p>To request changes, kindly respond to this email at the earliest, to rectify your badge.</p>

<h1>GENERAL INFORMATION</h1>

<h3>HOTEL AND ACCOMMODATION</h3>

<p>For your hotel and accommodation, GPCA attendees can avail of special rates by contacting the Hotel at <a href="mailto:reservations@theplazadoha.com" target="_blank">reservations@theplazadoha.com</a> or <a href="mailto:sabdelaziz@theplazadoha.com" target="_blank">sabdelaziz@theplazadoha.com</a> .</p>

<p>Contact person Name: Shady Abdulaziz <br> Contact person Phone Number: +974 3300 0294</p>
    
<h3>ROOM RATES PER NIGHT:</h3>

<ul>
    <li>Premier Room: QAR 550</li>
    <li>Deluxe Room: QAR 600</li>
    <li>Executive Room: QAR 650</li>
    <li>Deluxe Suite: QAR 1200</li>
    <li>Executive Suite: QAR 1400</li>
</ul>

<p>Note: <em>Special rates are subject to availability</em></p>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>