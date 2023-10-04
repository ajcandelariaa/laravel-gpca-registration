<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from {{ $details['eventDates'] }} at {{ $details['eventLocation'] }}.</p>

<p>Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements for your optimal event experience.</p>

<span>
    Your registration details are as follows:
    <br>
    Delegate Full name: <strong>{{  $details['name'] }}</strong>
    <br>
    Job title: <strong>{{  $details['jobTitle'] }}</strong>
    <br>
    Company name: <strong>{{  $details['companyName'] }}</strong>
    <br>
    Transaction ID: <strong>{{  $details['transactionId'] }}</strong>
    <br><br>
    If you require further assistance with the confirmation process, feel free to contact us at <a href="mailto:forumregistration@gpca.org.ae">forumregistration@gpca.org.ae</a>. 
</span>
@if ($sendInvoice)
<br><br>
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
<span>&nbsp;</span>
@else
<br><br>
@endif

<h2>GENERAL INFORMATION</h2>

<span><strong>Qatar Visa</strong></span><br><span>Please see the <a href="https://visitqatar.com/intl-en/practical-info/visas">link</a> for further information on how to obtain the visa. Kindly notify the GPCA office if you require any assistance.</span>

<span><strong>Hotel accomodation</strong></span><br><span>Experience a seamless and stress-free event journey with our expert travel partner. For more information on travel and accommodation please contact</span>
<br><br>
<span>Uchita Mhatre</span><br>
<span>+971553079469</span><br>
<span>umhatre@cozmotravel.com</span>
<br><br>
<span>Mohamed Reda Sabbah</span><br>
<span>+971558236738</span><br>
<span>r.sabbah@cozmotravel.com</span>
<br><br>
<span>Aziz Tinwala</span><br>
<span>+971547502008</span><br>
<span>t.aziz@cozmotravel.com</span>
<br>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p>We look forward to welcoming you to the {{ $details['eventName'] }} to share industry insights, explore networking opportunities, and share valuable industry experiences.</p>

<p><strong>For travellers with passports issued by India, Pakistan, Iran, Thailand, and Ukraine</strong> who will apply for the E-visa through Hayya App and booked their accommodation through our travel partner, please approach Cozmo Travel to obtain DQ voucher as a requirement for visa application. Other nationalities may be added to this requirement at the discretion of the State of Qatar.</p>

<p><strong>For other non GCC nationalities</strong> who will apply visa through Hayya App and booked their accommodation through our travel partner, please approach Cozmo Travel to obtain hotel confirmation letter as a requirement for visa application.</p>

<p><strong>Nationals of the Gulf Cooperation Council countries (Bahrain, Kuwait, Oman, Saudi Arabia, and United Arab Emirates)</strong> do not require a visa to enter Qatar.</p>

Best regards,
<br>
GPCA Team
</x-mail::message>