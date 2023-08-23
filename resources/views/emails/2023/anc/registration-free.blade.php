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

<p>We look forward to welcoming you to the {{ $details['eventName'] }} to share industry insights, explore networking opportunities, and share valuable industry experiences.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>