<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p><strong>Your registration has been confirmed. Please find below the summary of your booking confirmation.</strong></p>

<p>Upon your  arrival at the event, you can collect your badge from the fast-track delegate counter or from the main registration counter situated in exhibition hall 4 to 6 at the concourse.</p>

{{-- <p>Use the below QRCode to print your own badge onsite:</p> --}}

{{-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($details['badgeLink'])) !!}" /> --}}

<br><br>

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

<x-mail::button :url="'https://www.gpcaforum.com/conference-at-a-glance/'" color="registration">
Conference at a glance
</x-mail::button>
<span>&nbsp;</span>

<p>To request any changes, kindly respond to this email at the earliest to rectify your badge.</p>

<h2>GENERAL INFORMATION</h2>

<span><strong>Networking day: 4 December 2023</strong></span><br><span>Delegates can start collecting their badges on 4<sup>th</sup> December at the designed registration desk onsite.</span>

<span>All registered delegates are invited to join the networking dinner on 4<sup>th</sup> December. Join us for an evening filled with meaningful conversations and the opportunity to establish valuable connections with industry peers.</span>

<span><strong>Qatar Visa</strong></span><br><span><strong>For travellers with passports issued by India, Pakistan, Iran, Thailand, and Ukraine</strong> who will apply for the E-visa through Hayya App and booked their accommodation through our travel partner, please approach Cozmo Travel to obtain DQ voucher as a requirement for visa application. Other nationalities may be added to this requirement at the discretion of the State of Qatar.</span>

<span><strong>For other non GCC nationalities</strong> who will apply visa through Hayya App and booked their accommodation through our travel partner, please approach Cozmo Travel to obtain hotel confirmation letter as a requirement for visa application.</span>

<span><strong>Nationals of the Gulf Cooperation Council countries (Bahrain, Kuwait, Oman, Saudi Arabia, and United Arab Emirates)</strong> do not require a visa to enter Qatar.</span>

<span><strong>Hotel Accomodation</strong></span><br><span>Experience a seamless and stress-free event journey with our expert travel partner. For more information on travel and accommodation please contact</span>
<br><br>
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
<br>
GPCA Team
</x-mail::message>