<x-mail::message>
<a href="https://www.marriott.com/event-reservations/reservation-link.mi?id=1751879868394&key=GRP&app=resvlink" target="_blank">
    <img src="http://gpca.org.ae/conferences/anc/wp-content/uploads/2025/07/email-notif-banner.png">
</a>
    
<p class="sub" style="margin-top: 15px;">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="http://gpca.org.ae/conferences/anc/operational-excellence-workshop/">3<sup>rd</sup> Operational Excellence Workshop</a>, taking place on 29 September in the {{ $details['eventLocation'] }}. By registering as a workshop attendee, you are subject to the terms and conditions outlined in the invoice.</p>

<p class="sub" style="margin-top: 15px;"><strong>Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements.</strong></p>

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

<p class="sub" style="margin-top: 15px;">To request updates on your registration details, contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> before 1<sup>st</sup> September to ensure your badge information is accurate.</p>

<p class="sub" style="margin-top: 20px;"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon arrival, please proceed to the registration desk located in the Foyer to collect your event badge. Kindly present your ID or email confirmation for verification.</p>

<p class="sub" style="margin-top: 20px;">For any event-related queries, please reach out to the following team members:</p>

<p class="sub" style="margin-top: 10px;"><strong>Sponsorship, Exhibition, and Delegate Inquiries: </strong></p>

<ul class="event-list">
    <li style="margin-top: 5px;">Salman Khan and Jerry Rodrigues</li>
    <li>Email: <a href="mailto:salman@gpca.org.ae">salman@gpca.org.ae</a>, <a href="mailto:jerry@gpca.org.ae">jerry@gpca.org.ae</a></li>
    <li>Telephone: +971 4 451 0666 ext 103 & 106</li>
</ul>

<p class="sub" style="margin-top: 20px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 20px;">Best Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>