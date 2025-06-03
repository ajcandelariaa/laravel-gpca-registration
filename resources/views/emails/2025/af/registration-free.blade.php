<x-mail::message>
<img src="https://www.gpcaforum.com/wp-content/uploads/2025/06/email-notif-banner.jpg">
    
<p class="sub" style="margin-top: 15px;">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}. By registering as a delegate, you are subject to the terms and conditions outlined in the invoice.</p>

<p class="sub" style="margin-top: 15px;"><strong>Please note that your registration is subject to confirmation from one of our team members. We will review the registration details you've provided to ensure we have the accurate information to make the necessary badge arrangements.</strong></p>

<p class="sub" style="margin-top: 15px;">Your registration details as follows:</p>

<br>

<img src="data:image/png;base64, {!! base64_encode(
    QrCode::format('png')->size(200)->generate($details['qrCodeForPrint']),
) !!} ">

<p class="sub" style="margin-top: 20px;"><strong>Delegate Information</strong></p>
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

<p class="sub" style="margin-top: 15px;">To request any updates on your registration details, kindly contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a> before 13<sup>th</sup> November to ensure your badge information is accurate. </p>

<p class="sub" style="margin-top: 20px; text-decoration: underline;"><strong>Collection of badges</strong></p>

<p class="sub" style="margin-top: 5px;">Upon your arrival, please proceed to the registration desk located in the foyer to collect your event badge. Kindly present your ID or email confirmation for verification.</p>

<p class="sub" style="margin-top: 20px;"><strong>GENERAL INFORMATION</strong></p>

<p class="sub" style="margin-top: 15px; text-decoration: underline;"><strong>Bahrain Visa</strong></p>

<p class="sub" style="margin-top: 5px;">Non-GCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> must process their own visa. For your visa eligibility and visa application guidelines, please visit <a href="https://www.evisa.gov.bh/" target="_blank">https://www.evisa.gov.bh/</a>. Invitation letters are available upon request, only once payment has been made. For further assistance, please contact <a href="mailto:jovelyn@gpca.org.ae">jovelyn@gpca.org.ae</a>.</p>

<p class="sub" style="margin-top: 15px; text-decoration: underline;"><strong>Hotel Accommodation</strong></p>

<p class="sub" style="margin-top: 5px;">To avail the special hotel rates kindly click <a href="https://www.gpcaforum.com/travel-and-accommodation/" target="_blank">here</a> for the list of partner hotels in Bahrain. Kindly indicate in your accommodation inquiry that you are attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>. </p>

<p class="sub" style="margin-top: 15px;">For any event-related queries, please reach out to the following team members:</p>

<p class="sub" style="margin-top: 15px;"><strong>Sponsorship, Exhibition, and Delegate Inquiries:</strong></p>
<p class="sub" style="margin-top: 5px;"><strong>Salman Khan and Jerry Rodrigues</strong></p>
<p class="sub">Email: <a href="mailto:salman@gpca.org.ae">salman@gpca.org.ae</a>, <a href="mailto:jerry@gpca.org.ae">jerry@gpca.org.ae</a></p>
<p class="sub">Telephone: +971 4 451 0666 ext 103 & 106</p>

<p class="sub" style="margin-top: 15px;">Stay updated on upcoming GPCA events and industry news by following our <a href="https://www.linkedin.com/company/gulf-petrochemicals-and-chemicals-association-gpca-/">LinkedIn Page</a>. You can also connect with us on our official social media accounts: <a href="https://twitter.com/GulfPetChem">Twitter</a>, <a href="https://www.instagram.com/gulfpetchem/">Instagram</a>, <a href="https://www.facebook.com/GulfPetChem?fref=ts">Facebook</a>, and <a href="https://www.youtube.com/user/GPCAorg">YouTube</a>. </p>

<p class="sub" style="margin-top: 15px;">Thank you, and we look forward to welcoming you in Bahrain for the {{ $details['eventName'] }}.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>