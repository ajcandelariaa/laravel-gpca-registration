<x-mail::message>
<p class="sub">Dear {{ $details['fullName'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings!</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering for the <a href="https://www.gpcaforum.com/" target="_blank">17<sup>th</sup> Annual GPCA Forum</a>, from 4-6 December 2023 which will be held in Qatar National Convention Centre. The forum's opening ceremony is scheduled for 3<sup>rd</sup> December, from 16:00-18:30, and will be inaugurated by the Energy Ministers of Qatar and Saudi Arabia, and SABIC’s CEO will deliver the welcome remarks. Additionally, the GPCA Legacy Awards ceremony and the inauguration of the exhibition are scheduled for the same day, followed by a networking reception at the Fountain Area, Qatar National Convention Centre.</p>

<p class="sub" style="margin-top: 15px;">For badge collection, delegates can collect their badges starting from 3<sup>rd</sup> December, 08:00 am onwards. To expedite the process, kindly present your valid ID along with your QR code at the designated registration desk located at Spider Foyer 1 and Exhibition Halls 5 & 6.</p>

<p class="sub" style="margin-top: 15px; color:red;">Please note that badges will only be issued to paid registrations. Kindly ensure that your registration dues are settled and paid before 3<sup>rd</sup> December to facilitate smooth badge collection onsite. Onsite payment is available through cash (preferred) or credit card. </p>

<p class="title" style="margin-top: 20px;">Delegate Information:</p>
<p class="sub">Full name: {{  $details['fullName'] }}</p>
<p class="sub">Job title: {{  $details['jobTitle'] }}</p>
<p class="sub">Company name: {{  $details['companyName'] }}</p>
<p class="sub">Transaction ID: {{  $details['transactionId'] }}</p>

<br>

<img src="data:image/png;base64, {!! base64_encode(
    QrCode::format('png')->size(200)->generate($details['qrCodeForPrint']),
) !!} ">

<p class="sub" style="margin-top: 15px;"><strong>Please note</strong>: Wearing badges during the conference is <span style="color: red;">mandatory</span> to ensure a seamless and successful networking experience. If you wish to appoint a representative to collect your badge, please present your email confirmation with the fast-track code at the designated counter onsite.</p>

<p class="title" style="margin-top: 20px;">EVENT INFORMATION</p>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Registration</p>
<ul class="event-list">
<li>Date: 3-6 December 2023</li>
<li>Locations:
<ul>
<li>VIP/Speakers: Spider Foyer Level 1</li>
<li>Delegates: Spider Foyer Level 1</li>
<li>Exhibitors/ Visitors: Ground Level Exhibition Halls 5 & 6</li>
<li>Youth Forum attendees: Ground Level Exhibition Halls 5 & 6</li>
<li>Media: Ground Level Exhibition Halls 5 & 6</li>
<li>Meeting room guests: Ground Level Exhibition Halls 5 & 6</li>
</ul>
</li>
<li>Timings:
<ul>
<li>3<sup>rd</sup> December: 08:00 – 17:00</li>
<li>4<sup>th</sup> December: 07:30 – 16:30</li>
<li>5<sup>th</sup> December: 08:00 – 17:00</li>
<li>6<sup>th</sup> December: 08:00 – 13:00</li>
</ul>
</li>
</ul>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Opening Ceremony</p>
<ul class="event-list">
<li>Date: 3 December 2023</li>
<li>Locations: Conference Hall Level 2</li>
<li>Timings:
<ul>
<li>Welcome remarks: 16:10-16:20</li>
<li>Ministerial addresses: 16:20-16:40</li>
<li>Incoming host address: 16:40-16:50</li>
<li>GPCA Legacy Awards Ceremony: 16:50-17:30</li>
<li>Exhibition Inauguration: 17:30-18:30</li>
<li>Networking reception: 18:00-19:30</li>
</ul>
</li>
<li><em>View the updated agenda <a href="https://www.gpcaforum.com/annual-forum/#program" target="_blank">here</a></em></li>
</ul>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Exhibition</p>
<ul class="event-list">
<li>Date: 3-6 December 2023</li>
<li>Locations: Ground Level Exhibition Halls 5 & 6</li>
<li>Timings: 08:00 – 17:00</li>
</ul>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">2<sup>nd</sup> GPCA Youth Forum</p>
<ul class="event-list">
<li>Date: 4-6 December 2023</li>
<li>Locations: Ground Level Exhibition Halls 5 & 6</li>
<li>Timings:
<ul>
<li>4<sup>th</sup> December: 11:00-17:00</li>
<li>5<sup>th</sup> December: 10:00-15:35</li>
<li>6<sup>th</sup> December: 09:00-12:30</li>
</ul>
</li>
<li><em>View the updated agenda <a href="https://www.gpcaforum.com/gpca-youth-forum/#program" target="_blank">here</a></em></li>
</ul>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">2<sup>nd</sup> GPCA Symposium</p>
<ul class="event-list">
<li>Date: 4-5 December 2023</li>
<li>Locations: Conference Halls Level 2</li>
<li>Timings:
<ul>
<li>4<sup>th</sup> December: 14:00-16:35</li>
<li>5<sup>th</sup> December: 14:30-17:05</li>
</ul>
</li>
<li><em>View the updated agenda <a href="https://www.gpcaforum.com/gpca-symposium/#program" target="_blank">here</a></em></li>
</ul>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">GPCA Solutions XChange</p>
<ul class="event-list">
<li>Date: 4-5 December 2023</li>
<li>Locations: Ground Level Exhibition Halls 5 & 6</li>
<li>Timings:
<ul>
<li>4<sup>th</sup> December: 14:00-16:20</li>
<li>5<sup>th</sup> December: 11:40-16:20</li>
</ul>
</li>
<li><em>View the updated agenda <a href="https://www.gpcaforum.com/solutions-xchange/#program" target="_blank">here</a></em></li>
</ul>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Networking dinners</p>
<table class="af-table">
<tr>
<td width="30%">
    Date: 3<sup>rd</sup> December 2023 <br>
    Location: QNCC <br>
    Timings: 18:30-20:00
</td>
<td width="40%">
    Date: 4<sup>th</sup> December 2023  <br>
    Location: Qatar National Museum <br>
    Timings: 20:00-22:00
</td>
<td width="30%">
    Date: 5<sup>th</sup> December 2023 <br>
    Location: Al Shaqab Longines Arena <br>
    Timings: 20:00-22:00 
</td>
</tr>
</table>

<p class="subtitle" style="margin-top: 15px; text-decoration: underline;">Complimentary shuttle buses available for the Youth Forum attendees and delegates</p>

<p class="sub" style="margin-top: 15px;">Please click on the <a href="https://www.gpcaforum.com/wp-content/uploads/2023/11/17th-Annual-GPCA-Forum-Bus-Schedule-Final.pdf" target="_blank">link</a> to check the availability of the buses for attending the forum.</p>


<p class="title" style="margin-top: 30px;">NEW KEY FEATURE THIS YEAR</p>

<p class="subtitle" style="margin-top: 15px;">GPCA Solutions XChange</p>
<p class="sub" style="margin-top: 5px;">The GPCA is proud to present the inaugural edition of the GPCA Solutions Xchange. This initiative is an innovative knowledge-sharing platform, where industry stakeholders will convene to address some of the most pressing challenges. This groundbreaking initiative is poised to transform the landscape of knowledge-sharing within the chemical industry, facilitating the exploration of practical solutions, exchange of insights, and unlocking new possibilities that will shape the industry's future.</p>
<p class="sub" style="margin-top: 5px;"><a href="https://www.gpcaforum.com/solutions-xchange/" target="_blank">Learn more</a></p>

<p class="subtitle" style="margin-top: 25px;">Sustainability District</p>
<p class="sub" style="margin-top: 5px;">The Sustainability District embodies this commitment by highlighting how the industry is incorporating sustainable practices into every aspect of its operations. An extraordinary experience that offers a unique insider’s view of the transformative efforts of the chemical industry. Prepare to be immersed in the narrative as an active participant, rather than a mere spectator.</p>
<p class="sub" style="margin-top: 5px;"><a href="https://www.gpcaforum.com/features/#sustainability-district" target="_blank">Learn more</a></p>

<p class="sub" style="margin-top: 25px;">For the latest updates during the program and to connect with industry leaders, we recommend downloading the event app using the following link.</p>

<p class="sub" style="margin-top: 15px;">Playstore: <a href="https://play.google.com/store/apps/details?id=com.eventify.bg" target="_blank">17<sup>th</sup> Annual GPCA Forum - Apps on Google Play</a></p>
<p class="sub">IOS: <a href="https://apps.apple.com/us/app/17th-annual-gpca-forum/id6471959694" target="_blank">17<sup>th</sup> Annual GPCA Forum on the App Store (apple.com)</a></p>

<p class="sub" style="margin-top: 15px;">For real-time updates and engagement, stay connected with us on our social media accounts. Don't forget to tag us and use the hashtags #GPCAForum to keep up with your peers' activities and have a chance to be featured on our social media platforms.</p>

<p class="sub" style="margin-top: 15px;">We hope you will enjoy this year's edition, designed to provide you with unparalleled opportunities for knowledge exchange, networking, and cultural experiences.</p>

<p class="sub" style="margin-top: 15px;">Kindest regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>
