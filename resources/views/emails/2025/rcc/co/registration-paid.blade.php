<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place on 14- 15 October 2025 in {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms and conditions outlined in the invoice.</p>

<p class="sub" style="margin-top: 15px;">Your registration has been confirmed, and your delegate pass can be accessed during the <strong><em>conference only</em></strong>. You will have access to the networking break, networking lunch, and the Gala dinner. Please find below the summary of your booking confirmation.</p>

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

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon arrival, please proceed to the registration desk located in the Foyer to collect your event badge. Kindly present your ID or email confirmation for verification.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Hotel Booking</strong></p>
<p class="sub" style="margin-top: 5px;">To ensure a smooth and comfortable experience for all attendees, the 6<sup>th</sup> GPCA Responsible Care Conference offers exclusive hotel rates and comprehensive travel information. Taking place from 13–15 October 2025 at the Sheraton Bahrain Hotel, Manama, Bahrain, this year’s event provides tailored accommodation packages and essential travel guidance to support your participation.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Signature Room</strong></p>
<p class="sub" style="margin-top: 5px;">Single Occupancy: BHD 70</p>
<p class="sub">Double Occupancy: BHD 80</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Club Room</strong></p>
<p class="sub" style="margin-top: 5px;">Single Occupancy: BHD 110</p>
<p class="sub">Double Occupancy: BHD 140</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Junior Suite</strong></p>
<p class="sub" style="margin-top: 5px;">Single Occupancy: BHD 145</p>
<p class="sub">Double Occupancy: BHD 155</p>

<p class="sub" style="margin-top: 20px;"><strong><em>Note: All prices are inclusive of breakfast and subject to applicable taxes.</em></strong></p>

<p class="sub" style="margin-top: 20px;">For reservation and inquiries, please contact:</p>
<p class="sub" style="margin-top: 5px;">Rakan Hussein</p>
<p class="sub"><a href="mailto:Rakan.hussein@sheraton.com">Rakan.hussein@sheraton.com</a></p>
<p class="sub">+973 3888 2716</p>

<p class="sub" style="margin-top: 20px;">If you wish to upgrade from a conference pass to a full event pass, the following additional fees will apply:</p>
<p class="sub" style="margin-top: 5px;">Member companies: $200</p>
<p class="sub">Non-member companies registered at the Early Bird rate: $200</p>
<p class="sub">Non-member companies registered at the Standard rate: $400</p>

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