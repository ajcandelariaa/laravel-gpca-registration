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

<p>To request changes, kindly respond to this email at the earliest, to rectify your badge.</p>

<h1>GENERAL INFORMATION</h1>

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

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>