<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms and conditions outlined in the invoice.</p>

<p class="sub" style="margin-top: 15px; color: red;">Please be advised that your registration is not yet confirmed. To avoid any inconvenience during onsite badge collection, please settle your payment or contact our finance team at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a>. If you have registered within the early bird time frame, payment must be completed before the deadline. Otherwise, the registration fee will automatically reflect the standard rate.</p>

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

<p class="sub" style="margin-top: 15px;">To request updates on your registration details, contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> before 7<sup>th</sup> April to ensure your badge information is accurate.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Collection of Badges</strong></p>

<p class="sub" style="margin-top: 5px;">Upon arrival, please proceed to the registration desk located in the Foyer to collect your event badge. Kindly present your ID or email confirmation for verification.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Invitation Letter</strong></p>

<p class="sub" style="margin-top: 5px;">Non-GCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> must process their own visa. For visa eligibility and application guidelines, please visit <a href="https://ksavisa.sa/">https://ksavisa.sa/</a>. Invitation letters are available upon request, only once payment has been made. For further assistance, please contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a>.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Hotel Booking</strong></p>

<p class="sub" style="margin-top: 5px;">For hotel accommodation, please click the booking <a href="https://www.marriott.com/event-reservations/reservation-link.mi?id=1737985974160&key=GRP&guestreslink2=true&app=resvlink">link</a> to avail the special hotel rate at the JW Marriott Hotel, Riyadh, Saudi Arabia:</p>

<ul class="event-list">
    <li style="margin-top: 5px;">One Deluxe Guest Room – SAR 1,300++ per night</li>
    <li>Special hotel rate booking is valid until 19 March 2025</li>
    <li>Special hotel rate includes breakfast and internet</li>
    <li>Above rate is subject to availability</li>
</ul>

<p class="sub" style="margin-top: 15px;">If you have any questions or need assistance with the booking, kindly coordinate with Abdelrahman Hamza at <a href="mailto:abdelrahman.ahmed@marriott.com">abdelrahman.ahmed@marriott.com</a> or call +966 570037631.</p>

<p class="sub" style="margin-top: 20px;">For any event-related queries, please reach out to the following team members:</p>

<p class="sub" style="margin-top: 15px;"><strong>Sponsorship, Exhibition, and Delegate Inquiries:</strong></p>

<ul class="event-list">
    <li style="margin-top: 5px;">Salman Khan and Jerry Rodrigues</li>
    <li>Email: <a href="mailto:salman@gpca.org.ae">salman@gpca.org.ae</a>, <a href="mailto:jerry@gpca.org.ae">jerry@gpca.org.ae</a></li>
    <li>Telephone: +971 4 451 0666 ext 103 & 106</li>
</ul>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 15px;">Thank you, and we look forward to welcoming you to the premier gathering of plastic industry professionals in the region.</p>

<p class="sub" style="margin-top: 15px;">Best Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>