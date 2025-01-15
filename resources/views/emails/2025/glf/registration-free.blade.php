<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, an exclusive C-level event. Held under the theme <strong><em>“Building the Next Generation of Leaders in the GCC Chemical Industry”</em></strong>, the forum will take place on <strong>Wednesday, 19 February 2025</strong> at the <strong>Taj Ballroom, Sheraton Bahrain Hotel, Manama, Bahrain</strong></p>

<p class="sub" style="margin-top: 15px;">Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements for your optimal event experience.</p>

<p class="sub" style="margin-top: 20px;"><strong>YOUR BADGE DETAILS:</strong></p>
<p class="sub"><strong>Delegate full name:</strong> {{  $details['name'] }}</p>
<p class="sub"><strong>Job title:</strong> {{  $details['jobTitle'] }}</p>
<p class="sub"><strong>Company name:</strong> {{  $details['companyName'] }}</p>

<p class="sub" style="margin-top: 20px;"><strong>Key information you shouldn’t miss:</strong></p>
<ul class="event-list">
<li style="margin-top: 5px;"><strong>Badge collection</strong><br>Upon arrival, please proceed to the registration desk to collect your forum badge. This badge will serve as your access pass, granting entry to all sessions and networking events scheduled throughout the day.</li>
<li style="margin-top: 15px;"><strong>Networking opportunities</strong><br>Make the most of the networking sessions to connect with fellow C-level executives, exchange ideas, and explore potential collaborations.</li>
</ul>

<p class="sub" style="margin-top: 20px;"><strong>Hotel booking:</strong></p>
<p class="sub" style="margin-top: 5px;">We have secured a special discount rate for our attendees during the Forum. Please advise your office to use the link below to book a room at this exclusive rate:</p>
<p class="sub" style="margin-top: 5px;"><a href="https://www.marriott.com/event-reservations/reservation-link.mi?id=1736166064758&key=GRP&guestreslink2=true&app=resvlink" target="_blank">Reservation-Link</a></p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Hotel Contact Information:</strong></p>
<p class="sub" style="margin-top: 5px;">Rakan Hussein</p>
<p class="sub">Email: <a href="mailto:Rakan.Hussein@sheraton.com">Rakan.Hussein@sheraton.com</a></p>
<p class="sub">Mobile: +973 3888 27165</p>

<p class="sub" style="margin-top: 15px;">For any queries or further assistance, please contact <a href="mailto:forumregistration@gpca.org.ae">forumregistration@gpca.org.ae</a> or call +971 4 451 0666 ext. 153</p>

<p class="sub" style="margin-top: 15px;">Thank you for joining the {{ $details['eventName'] }}. Your participation is vital to the success of this forum, where industry leaders convene to engage in insightful discussions, gain exclusive insights, and foster valuable connections.</p>

<p class="sub" style="margin-top: 15px;">We look forward to hosting you for a day of inspiring, leadership, thought-provoking dialogue and exceptional networking opportunities.</p>

<p class="sub" style="margin-top: 15px;">Warm Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>