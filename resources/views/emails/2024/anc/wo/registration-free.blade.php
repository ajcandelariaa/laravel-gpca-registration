<x-mail::message>
<img src="http://gpca.org.ae/conferences/anc/wp-content/uploads/2024/06/email-notif-banner6.jpg">
    
<p class="sub" style="margin-top: 15px;">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the Operational Excellence in the GCC Agri-Nutrients Industry Workshop, taking place on 10 September 2024 at the {{ $details['eventLocation'] }}</p>

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

<p class="sub" style="margin-top: 15px;">To request any update on your registration details or would like to upgrade your pass to attend the full event for three days, kindly contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a>.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>INVITATION LETTER</strong></p>

<p class="sub" style="margin-top: 5px;">Non-GCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> must process their own visa. For your visa eligibility and visa application guidelines, please visit <a href="https://ksavisa.sa/" target="_blank">https://ksavisa.sa/</a>.</p>

<p class="sub" style="margin-top: 15px;">Invitation letter is available on request, only once payment has been made. Please contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> for further assistance.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>HOTEL BOOKING</strong></p>

<p class="sub" style="margin-top: 5px;">For the hotel accommodation, please click the booking <a href="https://www.marriott.com/event-reservations/reservation-link.mi?id=1717601167832&key=GRP&app=resvlink" target="_blank">link</a> to avail the special hotel rate in JW Marriot Hotel, Riyadh, Saudi Arabia. </p>

<ul class="event-list">
    <li style="margin-top: 5px;">One Deluxe guest room – SAR 1,500++ per night</li>
    <li>Special hotel rate is only valid from September 09 to September 12, 2024</li>
    <li>Special hotel rate includes breakfast and internet.</li>
    <li>Above rate is subject to availability.</li>
</ul>

<p class="sub" style="margin-top: 15px;">If you have any questions or need assistance with the booking link kindly coordinate with Shady Rabea <a href="mailto:Shady.Ahmed2@marriott.com">Shady.Ahmed2@marriott.com</a>, +966 583916980.</p>

<p class="sub" style="margin-top: 15px;">For any event related queries, please feel free to reach out to the following team members:</p>

<p class="sub" style="margin-top: 15px;"><strong>For sponsorship, exhibition and membership inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Faheem Chowdhury</strong></p>
<p class="sub"><em>Head of Events</em></p>
<p class="sub">Email: <a href="mailto:faheem@gpca.org.ae">faheem@gpca.org.ae</a></p>
<p class="sub">Mob: +971 58 969 5448</p>

<p class="sub" style="margin-top: 15px;"><strong>For marketing related inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Jhoanna Kilat</strong></p>
<p class="sub"><em>Marketing Coordinator</em></p>
<p class="sub">Email: <a href="mailto:jhoanna@gpca.org.ae">jhoanna@gpca.org.ae</a></p>
<p class="sub">Tel: +971 4 451 0666 ext. 151</p>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 15px;">Thank you, and we look forward to welcoming you in Riyadh for the {{ $details['eventName'] }}.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>