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

<p>To collect your badge onsite, please present your business card as a reference to facilitate the verification process. To request changes, kindly respond to this email at the earliest, to rectify your badge. </p>

<h3>GENERAL INFORMATION</h3>

<h4><strong>Qatar Visa</strong></h4>
<p>Please see the <a href="https://visitqatar.com/intl-en/practical-info/visas">link</a> for further information on how to obtain the visa. Kindly notify the GPCA office if you require any assistance.</p>

<h4><strong>Hotel booking</strong></h4>
<p>For the hotel accommodation, please click the <a href="https://www.marriott.com/event-reservations/reservation-link.mi?id=1688641689519&key=GRP&app=resvlink">hotel booking reservation</a> where we have special rates.</p>

<ul>
    <li>QAR 825 - QAR 2,000++ per night includes breakfast and internet.</li>
    <li>Special rates are subject to availability.</li>
    <li>All rates are valid till August 15, 2023.</li>
</ul>

<h4><strong>Airlines</strong></h4>
<p>For your flight bookings, please click the below link to avail the special rates.</p>
<p>Preferred Airline – <a href="https://www.qatarairways.com/en/corporate-travel/qmice/qmice-attendees-book.html?promocode=GPCAGR23">Qatar Airways</a></p>

<h4><strong>Offer Details:</strong></h4>
<ul>
    <li>Offer period: until 19 September 2023 (available for booking) </li>
    <li>Travel period: <strong>from 7 to 29 September 2023</strong> </li>
    <li>Offer only valid when using the promo code <strong>GPCAGR23</strong> and travelling to <strong>DOH</strong></li>
</ul>

<h4><strong>How to Book</strong></h4>
<p><strong>Step 1</strong>: Select your origin airport to <strong>DOH</strong> and your travel dates between <strong>from 7 to 29 Sept 2023</strong></p>
<p><strong>Step 2</strong>: Click show flights to book and choose your flights.</p>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>