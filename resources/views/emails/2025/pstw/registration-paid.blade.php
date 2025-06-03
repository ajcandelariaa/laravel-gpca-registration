<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms and conditions outlined in the invoice.</p>

<p class="sub" style="margin-top: 15px;"><strong>Your registration has been confirmed. Please find below the summary of your booking confirmation.</strong></p>

<p class="sub" style="margin-top: 15px;">Your registration details as follows:</p>

<p class="sub" style="margin-top: 10px;">Full name: {{  $details['name'] }}</p>
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

<p class="sub" style="margin-top: 15px;">To request updates on your registration details, contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> to ensure your badge information is accurate.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon your arrival, kindly make your way to the registration desk to collect your event badge. This badge will serve as your access pass, allowing entry to all sessions and networking events scheduled throughout the day.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Hotel booking</strong></p>
<p class="sub" style="margin-top: 5px;">For the hotel accommodation, guests can send an email to reservations.pullmandubaidowntown@accor.com and copy: arjun.mahindru@accor.com , nerissa.devilla@accor.com with their details to make the reservation.</p>

<ul class="event-list">
    <li style="margin-top: 5px;">Guests should mention the booking name <strong>“GPCA Product Stewardship Workshop”</strong> and booking code <a href="https://accorce2.oraclehospitality.eu-frankfurt-1.ocs.oraclecloud.com/OPERA9/opera/operacloud/faces/opera-cloud-index/OperaCloud"><strong>“2509GULFPE”</strong></a> to avail of the special rates.</li>
    <li>AED 520 – AED 620+++ per night includes breakfast and internet.</li>
    <li>Rates are applicable until 10<sup>th</sup> August 2025.</li>
</ul>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 15px;">Thank you, and we look forward to welcoming you to the premier gathering of industry professionals in the region.</p>

<p class="sub" style="margin-top: 15px;">Best Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>