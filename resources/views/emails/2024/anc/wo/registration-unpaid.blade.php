<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the Workshop on Operational Excellence in the GCC Agri-Nutrients industry taking place on 10 September 2024 at the {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms & condition.</p>

<p class="sub" style="margin-top: 15px; color: red;">Please note that your registration is not yet confirmed. To avoid any inconvenience during onsite badge collection, please settle your payment or contact our finance team at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a>.</p>

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

<p class="sub" style="margin-top: 15px;">Thank you, and we look forward to welcoming you in Riyadh for the {{ $details['eventName'] }}.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>