<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}</p>

<p class="sub" style="margin-top: 15px; color: red;">Please note that your registration is not yet confirmed. Kindly settle your payment through bank transfer prior to the event to avoid any inconvenience onsite. <span style="text-decoration: underline"><strong><em>NO BADGE</em></strong></span> will be issued unless payment has been settled and confirmed.</p>

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

<p class="sub" style="margin-top: 15px;">To request any updates on your registration details, kindly contact <a href="mailto:forumregistration@gpca.org.ae">forumregistration@gpca.org.ae</a> to rectify your badge. </p>

<p class="sub" style="margin-top: 15px;">For any event related queries, please feel free to reach out to the following team members:</p>

<p class="sub" style="margin-top: 15px;"><strong>For sponsorship, exhibition, registration and membership inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Sales Team</strong></p>
<p class="sub">Email: <a href="mailto:sales@gpca.org.ae">sales@gpca.org.ae</a></p>
<p class="sub">Telephone: +971 4 451 0666 ext. 122</p>

<p class="sub" style="margin-top: 15px;"><strong>For conference program and speaking inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Nakul Jain</strong></p>
<p class="sub"><em>Conference Producer</em></p>
<p class="sub">Email: <a href="mailto:nakul@gpca.org.ae">nakul@gpca.org.ae</a></p>
<p class="sub">Telephone: +971 4 451 0666 ext. 127</p>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">Youtube</a>. </p>

<p class="sub" style="margin-top: 15px;">Thank you and we look forward to welcoming you to this exciting and insightful gathering.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>