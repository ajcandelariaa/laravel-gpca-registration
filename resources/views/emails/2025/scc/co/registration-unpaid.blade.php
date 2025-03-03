<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms and conditions outlined in the invoice.</p>

<p class="sub" style="margin-top: 15px; color: red;">Please be advised that your registration is not yet confirmed. To avoid any inconvenience during onsite badge collection, please settle your payment or contact our finance team at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a>. If you have registered within the early bird time frame, payment must be completed before the deadline. Otherwise, the registration fee will automatically reflect the standard rate. </p>

<p class="title" style="margin-top: 20px;">Your registration details as follows:</p>
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

<p class="sub" style="margin-top: 15px;">To request updates on your registration details, contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> before 7<sup>th</sup> May to ensure your badge information is accurate.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Collection of Badges</strong></p>

<p class="sub" style="margin-top: 5px;">Upon arrival, please proceed to the registration desk located in the Foyer to collect your event badge. Kindly present your ID or email confirmation for verification.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>About GPCA Gulf SQAS Workshop</strong></p>

<p class="sub" style="margin-top: 5px;">On 26<sup>th</sup> May in conjunction with the 16<sup>th</sup> GPCA Supply Chain Conference there will be a <a href="https://gpca.org.ae/conferences/scc/gulf-sqas-workshop/" target="_blank">Gulf SQAS Workshop</a>, the Workshop is tailored to be the hub for addressing the evolving needs of the Arabian Gulf’s logistics sector, with a focus on long-term sustainability, safety, and quality. Discover how this essential program empowers Logistic Service Providers (LSPs) to assess and continuously enhance their Environment, Health, Safety, Security, and Quality (EHSS&Q) performance. As one of the pioneering regions adopting this system, GPCA is proud to lead the way in establishing Gulf SQAS as the industry standard for land-based logistics operations.</p>

<p class="sub" style="margin-top: 15px;">If you wish to upgrade from a conference pass to a full event pass, the following additional fees will apply:</p>
<p class="sub" style="margin-top: 5px;">Member companies: $200</p>
<p class="sub">Non-member companies registered at the Early Bird rate: $200</p>
<p class="sub">Non-member companies registered at the Standard rate: $400</p>

<p class="sub" style="margin-top: 15px;">For any event-related queries, please reach out to the following team members:</p>

<p class="sub" style="margin-top: 10px;"><strong>Sponsorship, Exhibition, and Delegate Inquiries: </strong></p>

<ul class="event-list">
    <li style="margin-top: 5px;">Salman Khan and Jerry Rodrigues</li>
    <li>Email: <a href="mailto:salman@gpca.org.ae">salman@gpca.org.ae</a>, <a href="mailto:jerry@gpca.org.ae">jerry@gpca.org.ae</a></li>
    <li>Telephone: +971 4 451 0666 ext 103 & 106</li>
</ul>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>