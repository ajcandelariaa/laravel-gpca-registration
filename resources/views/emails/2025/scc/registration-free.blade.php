<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="https://gpca.org.ae/conferences/scc/gulf-sqas-workshop/" target="_blank">Gulf SQAS Workshop</a>, taking place on 26 May 2025 at the Sofitel Dubai Downtown and <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, scheduled for {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms and conditions outlined in the invoice.</p>

<p class="sub" style="margin-top: 15px;"><strong>Please note that your registration is subject to confirmation by one of our team members. We will review the details provided to ensure accuracy for the necessary badge arrangements.</strong></p>

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

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Hotel Booking</strong></p>

<p class="sub" style="margin-top: 15px;"><strong>1. Address Sky View Hotel</strong></p>

<p class="sub">For the hotel accommodation, please click the booking link <strong><a href="https://www.addresshotels.com/en/hotels/address-sky-view/">GPCA EVENT 2025</a></strong> and use the discount code: <strong>GPCA</strong>.</p>

<p class="sub" style="margin-top: 5px;">Note: Upon clicking the code, choose promo code and enter <strong>GPCA</strong>.</p>

<p class="sub" style="margin-top: 15px; text-decoration: underline;"><strong>Terms & Conditions:</strong></p>

<ul class="event-list">
    <li style="margin-top: 5px;">Booking dates from May 26 – 29, 2025</li>
    <li>Discounted rate is valid for 3 nights pre and 3 nights post stay</li>
    <li>Minimum length of stay for 2 nights, required for all bookings</li>
    <li>Nonrefundable rate plan (full cancellation charges will apply)</li>
    <li>Room will be subject to availability upon confirmation</li>
</ul>

<p class="sub" style="margin-top: 15px;"><strong>2. Sofitel Dubai Downtown</strong></p>

<p class="sub" style="margin-top: 15px; text-decoration: underline;"><strong>Terms & Conditions:</strong></p>

<ul class="event-list">
    <li style="margin-top: 5px;">Luxury room rate – AED 861++ per night</li>
    <li>Special hotel rate booking is valid until April 15, 2025</li>
    <li>Special hotel rate includes breakfast and internet</li>
    <li>Above rate is subject to availability</li>
</ul>

<p class="sub" style="margin-top: 5px;">For booking and other inquiry, kindly coordinate with Tamir Khilenbaatar at <a href="mailto:Tamir.KHILENBAATAR@sofitel.com">Tamir.KHILENBAATAR@sofitel.com</a> or call +971 54 581 2958.</p>

<p class="sub" style="margin-top: 20px;">For any event-related queries, please reach out to the following team members:</p>

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