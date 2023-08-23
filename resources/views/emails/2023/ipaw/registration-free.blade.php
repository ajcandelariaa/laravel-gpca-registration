<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from {{ $details['eventDates'] }} at {{ $details['eventLocation'] }}.</p>

<p>Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements for your optimal event experience.</p>

<span>
    Your registration details are as follows:
    <br>
    Delegate Full name: <strong>{{  $details['name'] }}</strong>
    <br>
    Job title: <strong>{{  $details['jobTitle'] }}</strong>
    <br>
    Company name: <strong>{{  $details['companyName'] }}</strong>
    <br>
    Transaction ID: <strong>{{  $details['transactionId'] }}</strong>
    <br><br>
    If you require further assistance with the confirmation process, feel free to contact us at <a href="mailto:forumregistration@gpca.org.ae">forumregistration@gpca.org.ae</a>. 
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

<p>We look forward to welcoming you to the {{ $details['eventName'] }} to share industry insights, explore networking opportunities, and share valuable industry experiences.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>