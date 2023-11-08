<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Warm greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">We appreciate your registration for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, scheduled to take place from {{ $details['eventDates'] }}, at the {{ $details['eventLocation'] }}.</p>

<p class="sub" style="margin-top: 15px;">Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements for your optimal event experience.</p>

<p class="title" style="margin-top: 20px;">Badge Collection:</p>
<p class="sub">Delegates can begin collecting their badges on December 3<sup>rd</sup> at the designated registration desk on-site. To ensure a seamless and convenient experience for our esteemed delegates, we have designated the following locations:</p>

<p class="sub" style="margin-top: 15px;"><span class="subtitle">Spider Foyer on Level 1:</span> Delegates can collect their badges here.</p>
<p class="sub"><span class="subtitle">Exhibition Halls 4 to 5 on the Ground Floor:</span> This area is designated for Youth Forum participants, exhibitors, and visitors to pick up their badges.</p>

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
<br>
@else
<br>
@endif

<x-mail::button :url="'https://www.gpcaforum.com/conference-at-a-glance/'" color="registration">
Conference at a glance
</x-mail::button>

<p class="sub" style="margin-top: 15px;">We look forward to your participation in the event and hope that your experience at the {{ $details['eventName'] }} will be both enriching and insightful.</p>

<p class="sub" style="margin-top: 15px;">Should you have any further inquiries or require additional information, please do not hesitate to reach out to us.</p>

<p class="sub" style="margin-top: 15px;">Thank you once again for your registration, and we eagerly anticipate your presence at the event.</p>

<p class="title" style="margin-top: 30px;">GENERAL INFORMATION</p>

<p class="subtitle" style="margin-top: 15px;">Networking Dinners:</p>
<p class="sub">Join us and other industry leaders for our formal sit-down dinner receptions celebrating the success of the {{ $details['eventName'] }}. We would like to extend an invitation to all attendees and their spouses to participate in these networking dinners. Kindly RSVP your attendance to jovelyn@gpca.org.ae on or before December 02, 2023.</p>

<p class="subtitle" style="margin-top: 15px;">Welcome Dinner (December 4, 2023):</p>
<p class="sub">Sponsored by Qatar Energy, our welcome dinner is a standout event crafted to foster meaningful connections.</p>

<p class="subtitle" style="margin-top: 15px;">Gala Dinner (December 5, 2023):</p>
<p class="sub">The {{ $details['eventName'] }}'s gala dinner, sponsored by SABIC, is an exceptional networking evening exclusively reserved for top executives and decision-makers in the petrochemical and chemical industry.</p>

<p class="subtitle" style="margin-top: 15px;">Qatar Visa</p>
<p class="sub">For travellers with passports issued by India, Pakistan, Iran, Thailand, and Ukraine, please reach out to Cozmo Travel <a href="https://www.gpcaforum.com/travel-accomodation/">https://www.gpcaforum.com/travel-accomodation/</a> for visa assistance if you have booked your accommodation through them.</p>

<p class="sub" style="margin-top: 15px;">Other non-GCC nationalities applying for a visa and who have booked their accommodation through our travel partner should approach Cozmo Travel to obtain a hotel confirmation letter, which is a visa application requirement.</p>

<p class="sub" style="margin-top: 15px;">We look forward to welcoming you to this exceptional event!</p>

<p class="sub" style="margin-top: 15px;">Best regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>