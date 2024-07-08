<x-mail::message>
<img src="http://gpca.org.ae/conferences/psc/wp-content/uploads/2024/07/email-notif-banner.jpg">
    
<p class="sub" style="margin-top: 15px;">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from 08-10 October 2024 at the {{ $details['eventLocation'] }}</p>

<p class="sub" style="margin-top: 15px;"><strong>Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements.</strong></p>

<p class="sub" style="margin-top: 20px;"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon your arrival, kindly make your way to the registration desk to collect your event badge located at the Foyer and present your ID or email confirmation. Registration starts at 07:30am onwards.</p>

<p class="sub" style="margin-top: 15px;">Your registration details as follows:</p>

<p class="title" style="margin-top: 20px;">Delegate Information:</p>
<p class="sub">Full name: {{  $details['name'] }}</p>
<p class="sub">Job title: {{  $details['jobTitle'] }}</p>
<p class="sub">Company name: {{  $details['companyName'] }}</p>
@if ($sendInvoice)
<p class="sub">Amount paid: $ {{ number_format($details['amountPaid'], 2, '.', ',') }}</p>
@endif
<p class="sub">Transaction ID: {{  $details['transactionId'] }}</p>

@if ($sendInvoice)
<br>
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
@endif

<p class="sub" style="margin-top: 15px;">To request any updates on your registration details, kindly contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> to rectify your badge. </p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>INVITATION LETTER</strong></p>

<p class="sub" style="margin-top: 5px;">Non-GCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> must process their own visa. For your visa eligibility and visa application guidelines, please visit <a href="https://ksavisa.sa/" target="_blank">https://ksavisa.sa/</a>.</p>

<p class="sub" style="margin-top: 15px;">Invitation letter is available on request, only once payment has been made. Please contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> for further assistance.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>HOTEL BOOKING</strong></p>

<p class="sub" style="margin-top: 5px;">For the hotel accommodation, please see below options where we have special hotel rate during the event. </p>

<ol class="event-list">
    <li style="margin-top: 15px;"><strong style="text-decoration: underline">InterContinental Al Jubail Resort</strong> <em>(<a href="https://maps.app.goo.gl/3fGXkbmbkDeXoJTq8" target="_blank">Location</a>)</em>
        <ul class="event-list">
            <li style="margin-top: 5px">Kindly click the booking <a href="https://www.ihg.com/intercontinental/hotels/us/en/al-jubail/dhahc/hoteldetail?fromRedirect=true&qSrt=sAV&qIta=99502056&icdv=99502056&qSlH=DHAHC&qCpid=958393848&qAAR=IPBTO&qRtP=IPBTO&setPMCookies=true&qSHBrC=IC&qDest=P.O.%2520Box%252010167%252C%2520AL%2520JUBAIL%252C%2520SA&srb_u=1" target="_blank">link</a> to avail the special hotel rate.</li>
            <li>One King guest room – SAR 783++ per night.</li>
            <li>Special hotel rate is only valid from October 06 to October 10, 2024</li>
            <li>Special hotel rate includes breakfast and internet.</li>
            <li>Above rate is subject to availability. </li>
        </ul>
    </li>
    <li style="margin-top: 10px;"><strong style="text-decoration: underline">Courtyard by Marriott Jubail</strong> <em>(<a href="https://maps.app.goo.gl/LJQVY8vmmhsumBGH7" target="_blank">Location</a>)</em>
        <ul class="event-list">
            <li style="margin-top: 5px">Single guest room – SAR 349++ per night.</li>
            <li>Special hotel rate is only valid from October 06 to October 10, 2024</li>
            <li>Special hotel rate includes breakfast and internet.</li>
            <li>Above rate is subject to availability.</li>
            <li>For Bookings kindly contact <a href="mailto:iftikhar.ahmed@marriott.com">iftikhar.ahmed@marriott.com</a> indicating “GPCA Process Safety Conference”. </li>
        </ul>
    </li>
</ol>

<p class="sub" style="margin-top: 15px;">For any event related queries, please feel free to reach out to the following team members:</p>

<p class="sub" style="margin-top: 15px;"><strong>For sponsorship, exhibition and membership inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Faheem Chowdhury</strong></p>
<p class="sub"><em>Head of Events</em></p>
<p class="sub">Email: <a href="mailto:faheem@gpca.org.ae">faheem@gpca.org.ae</a></p>
<p class="sub">Mob: +971 58 969 5448</p>

<p class="sub" style="margin-top: 15px;"><strong>For conference program and speaking inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Mohamed Seraj</strong></p>
<p class="sub"><em>Sr Specialist, Responsible Care<sup>®</sup></em></p>
<p class="sub">Email: <a href="mailto:mohamed@gpca.org.ae">mohamed@gpca.org.ae</a></p>
<p class="sub">Tel: +971 4 451 0666 ext. 121</p>

<p class="sub" style="margin-top: 15px;"><strong>For marketing related inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Jhoanna Kilat</strong></p>
<p class="sub"><em>Marketing Coordinator</em></p>
<p class="sub">Email: <a href="mailto:jhoanna@gpca.org.ae">jhoanna@gpca.org.ae</a></p>
<p class="sub">Tel: +971 4 451 0666 ext. 151</p>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 15px;">Thank you, and we look forward to welcoming you in Al Jubail for the {{ $details['eventName'] }}.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>