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

<h3>GENERAL INFORMATION</h3>

<p>Non – GPCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, must process their own visa. Visa applications can be submitted online at <a href="https://www.evisa.gov.bh/">https://www.evisa.gov.bh/</a></p>

<p>For the hotel accommodation, kindly <a href="http://gpcaresponsiblecare.com/wp-content/uploads/2023/07/The-Ritz-Carlton-Bahrain-Reservation-Form.pdf"><strong>click</strong></a> to download the form and submit your details to Prakash at <a href="mailto:Prakash.ramaiah@ritzcarlton.com">Prakash.ramaiah@ritzcarlton.com</a> or <a href="mailto:chahrazed.jassem@ritzcarlton.com">chahrazed.jassem@ritzcarlton.com</a>. You may also contact the following numbers for any additional inquiries: 973-66388039 or 973-39957141.</p>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p>We look forward to welcoming you to the {{ $details['eventName'] }} to share industry insights, explore networking opportunities, and share valuable industry experiences.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>