<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}</p>

<p class="sub" style="margin-top: 15px;">Your registration has been confirmed. Please find below the summary of your booking confirmation.</p>

<p class="sub" style="margin-top: 20px;"><strong>Collection of badges</strong></p>
<p class="sub" style="margin-top: 5px;">Upon your arrival, kindly make your way to the registration desk to collect your event badge located at the Foyer and present your ID or email confirmation. </p>

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

<p class="sub" style="margin-top: 15px;">To request any updates on your registration details, kindly contact <a href="mailto:forumregistration@gpca.org.ae">forumregistration@gpca.org.ae</a> before 30 April to rectify your badge. </p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>HOTEL BOOKING</strong></p>

<p class="sub" style="margin-top: 5px;">For the hotel accommodation, please click the booking link <a href="https://reservations.travelclick.com/108317?RatePlanId=6362987" target="_blank"><strong>GPCA EVENT 2024</strong></a> and use the discount code: <strong>GPCA</strong>.</p>

<p class="sub" style="margin-top: 15px;">Note: Upon clicking Add Code, choose DISCOUNT CODE and enter the GPCA code.</p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Terms & Conditions:</strong></p>

<p class="sub">Booking Dates: May 12 – 17 2024</p>

<p class="sub">15% Discount off (Best available rates) – <strong>Early Bird Offer</strong>, valid from February 12 to March 31, 2024.</p>

<p class="sub">10% Discount off (Best available rates) – <strong>Special Offer</strong>, valid from April 1 to May 17, 2024.</p>

<ul class="event-list">
    <li style="margin-top: 5px;">Minimum length of stay for 2 nights, required for all bookings.</li>
    <li>Cancellation policy: 30 days prior to arrival (after this date full cancellation charges will apply)</li>
    <li>Full Payment required, 30 days prior to arrival date.</li>
    <li>Rooms will be subject to availability upon confirmation.</li>
</ul>

<p class="sub" style="margin-top: 20px;">For any event related queries, please feel free to reach out to the following team members:</p>

<p class="sub" style="margin-top: 15px;"><strong>For sponsorship, exhibition and membership inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Sales Team</strong></p>
<p class="sub">Email: <a href="mailto:sales@gpca.org.ae">sales@gpca.org.ae</a></p>
<p class="sub">Telephone: +971 4 451 0666</p>

<p class="sub" style="margin-top: 15px;"><strong>For conference program and speaking inquiries please contact:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Nakul Jain</strong></p>
<p class="sub"><em>Conference Producer</em></p>
<p class="sub">Email: <a href="mailto:nakul@gpca.org.ae">nakul@gpca.org.ae</a></p>
<p class="sub">Telephone: +971 4 451 0666 ext. 127</p>

<p class="sub" style="margin-top: 15px;"><strong>For delegate registration inquiries:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Delegate Sales Team</strong></p>
<p class="sub">Email: <a href="mailto:forumregistration@gpca.org.ae">forumregistration@gpca.org.ae</a></p>
<p class="sub">Telephone: +971 4 451 0666 ext. 153</p>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">Youtube</a>. </p>

<p class="sub" style="margin-top: 15px;">For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}">GPCA Supply Chain Conference</a>.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>