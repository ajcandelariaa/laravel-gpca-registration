<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering as a visitor for the <a href="https://www.gpcaforum.com/" target="_blank">17<sup>th</sup> Annual GPCA Forum</a>, which will be held from <strong>4-6 December 2023</strong>, at the <strong>{{ $details['eventLocation'] }}</strong>. Please note that the opening ceremony of the forum will commence on <strong>3<sup>rd</sup> December</strong> from <strong>16:00-18:30</strong>. Access to this exclusive event is restricted to VIP, delegate and exhibitor passes only.</p>

<p class="sub" style="margin-top: 15px;">Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements for your optimal event experience.</p>

<p class="title" style="margin-top: 20px;">Visitor Information:</p>
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

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Badge Collection</p>
<p class="sub" style="margin-top: 5px;">Visitors can start collecting their badges on 3<sup>rd</sup> December 2023, from 16:00-17:00 onwards located at the Ground Level Exhibition Halls 5 & 6.</p>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Visitor Pass Access</p>
<p class="sub" style="margin-top: 5px;">Your visitor passes grants access to the following forum features:</p>
<ul class="event-list">
<li>Solutions Xchange program</li>
<li>Exhibition Halls</li>
<li>Cultural Majlis</li>
<li>Sustainability District</li>
<li>Publication Showcase</li>
<li>Industry Journey</li>
</ul>

<p class="sub" style="margin-top: 15px; color: red;">To upgrade your visitor, pass and gain full access to the forum, please contact forumregistration@gpca.org.ae.</p>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Visa Information</p>
<p class="sub" style="margin-top: 5px;">For travelers with passports issued by India, Pakistan, Iran, Thailand, and Ukraine, please reach out to Cozmo Travel https://www.gpcaforum.com/travel-accomodation/ for visa assistance if you have booked your accommodation through them.</p>

<p class="sub" style="margin-top: 15px;">For other non-GCC nationals applying for a visa and who have booked their accommodation through our travel partner should contact Cozmo Travel to obtain a hotel confirmation letter, a requirement for your visa applications.</p>

<p class="sub" style="margin-top: 15px;">We look forward to your participation in the event and hope that your experience at the <strong>17<sup>th</sup> Annual GPCA Forum</strong> will be both enriching and insightful. Should you have any further inquiries or require additional information, please do not hesitate to reach out to us.</p>

<p class="sub" style="margin-top: 15px;">Thank you once again for your registration, and we look forward to welcoming you to this exceptional event!</p>

<p class="sub" style="margin-top: 15px;">Best regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>