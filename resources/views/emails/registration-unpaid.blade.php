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

{{-- <h1>GENERAL INFORMATION</h1>

@if ($details['eventCategory'] == "IPAW")
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
@else
<h3>INVITATION LETTER</h3>

<p>Non-GCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> must process their tourist visa and can notify the GPCA office for assistance in issuing the Invitation letter. Visa applications for some nationalities can be submitted online at <a href="https://visa.visitsaudi.com" target="_blank">https://visa.visitsaudi.com</a></p>

<p>Once you have determined your eligibility, sign up, create an account, and begin the application process. Upon completion, you will receive an email with a copy of your e-visa and insurance. <br> <a href="https://visa.visitsaudi.com/Login" target="_blank">https://visa.visitsaudi.com/Login</a></p>

<h3>HOTEL AND ACCOMMODATION</h3>

<p>For your hotel and accommodation, GPCA attendees can avail of special rates <a href="https://www.marriott.com/event-reservations/reservation-link.mi?id=1679226955793&key=GRP&app=resvlink" target="_blank">here</a>.</p>

<ul>
    <li>SAR 650++ per night including breakfast and internet.</li>
    <li>Special rates are subject to availability.</li>
    <li>All rates are valid only from May 13 – 18 2023.</li>
</ul>
@endif --}}

@if ($details['eventCategory'] == "RCC")
<h3>GENERAL INFORMATION</h3>

<p>Non – GPCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, must process their own visa. Visa applications can be submitted online at <a href="https://www.evisa.gov.bh/">https://www.evisa.gov.bh/</a></p>

<p>For the hotel accommodation, kindly <a href="http://gpcaresponsiblecare.com/wp-content/uploads/2023/07/The-Ritz-Carlton-Bahrain-Reservation-Form.pdf"><strong>click</strong></a> to download the form and submit your details to Prakash at <a href="mailto:Prakash.ramaiah@ritzcarlton.com">Prakash.ramaiah@ritzcarlton.com</a> or <a href="mailto:chahrazed.jassem@ritzcarlton.com">chahrazed.jassem@ritzcarlton.com</a>. You may also contact the following numbers for any additional inquiries: 973-66388039 or 973-39957141.</p>

@elseif ($details['eventCategory'] == "ANC")
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

@else
@endif

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>