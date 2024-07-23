<x-mail::message>
<img src="https://www.gpcaforum.com/wp-content/uploads/2024/07/email-notif-banner.jpg">
    
<p class="sub" style="margin-top: 15px;">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms & condition.</p>

<p class="sub" style="margin-top: 15px; color: red;">Please note that your registration is not yet confirmed. To avoid any inconvenience during onsite badge collection, please settle your payment or contact our finance team at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a>.</p>

<p class="sub" style="margin-top: 20px;"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon your arrival, you can pick up your badge from the fast-track counter or from the relevant registration counter located at the Oman Convention & Exhibition Centre foyer.</p>

<p class="sub" style="margin-top: 15px;">Use this QR code to print your own badge onsite.</p>

<img src="data:image/png;base64, {!! base64_encode(
    QrCode::format('png')->size(200)->generate($details['qrCodeForPrint']),
) !!} " style="margin-top: 10px; display: block;">

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

<p class="sub" style="margin-top: 20px;"><strong>GENERAL INFORMATION</strong></p>

<p class="sub" style="margin-top: 15px; text-decoration: underline;"><strong>Oman Visa</strong></p>

<p class="sub" style="margin-top: 5px;">Non-GCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> must process their own visa. For your visa eligibility and visa application guidelines, please visit <a href="https://evisa.rop.gov.om/en/home?tabId=tyqwi7" target="_blank">Home - Evisa (rop.gov.om)</a>.</p>

<p class="sub" style="margin-top: 15px; text-decoration: underline;"><strong>Hotel Accommodation</strong></p>

<p class="sub" style="margin-top: 5px;">To avail the special hotel rates kindly click <a href="https://www.gpcaforum.com/wp-content/uploads/2024/07/18th-Annual-GPCA-Forum-–-Partner-Hotels.pdf" target="_blank">here</a> for the list of partner hotels in Muscat Oman. Kindly indicate in your accommodation inquiry that you are attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>. </p>

<p class="sub" style="margin-top: 15px;">For any event related queries, please feel free to reach out to the following team members:</p>

<p class="sub" style="margin-top: 15px;"><strong>For sponsorship, exhibition and membership inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Faheem Chowdhury</strong></p>
<p class="sub"><em>Head of Events</em></p>
<p class="sub">Email: <a href="mailto:faheem@gpca.org.ae">faheem@gpca.org.ae</a></p>
<p class="sub">Mob: +971 58 969 5448</p>

<p class="sub" style="margin-top: 15px;"><strong>For conference program and speaking inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Nakul Jain</strong></p>
<p class="sub"><em>Conference Producer</em></p>
<p class="sub">Email: <a href="mailto:nakul@gpca.org.ae">nakul@gpca.org.ae</a></p>
<p class="sub">Tel: +971 4 451 0666 ext. 127</p>

<p class="sub" style="margin-top: 15px;"><strong>For marketing related inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Jhoanna Kilat</strong></p>
<p class="sub"><em>Marketing Coordinator</em></p>
<p class="sub">Email: <a href="mailto:jhoanna@gpca.org.ae">jhoanna@gpca.org.ae</a></p>
<p class="sub">Tel: +971 4 451 0666 ext. 151</p>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 15px;">Thank you, and we look forward to welcoming you in Muscat for the {{ $details['eventName'] }}.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>