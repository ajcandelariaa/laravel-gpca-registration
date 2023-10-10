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

<span><strong>Hotel booking</strong></span><br><span>For the hotel accommodation, please click the <a href="https://www.ihg.com/crowneplaza/hotels/us/en/manama/mnaba/hoteldetail?fromRedirect=true&qSrt=sBR&qIta=99801505&icdv=99801505&qSlH=MNABA&qGrpCd=GU1&setPMCookies=true&qSHBrC=CP&qDest=Diplomatic%20Area%20317%2C%20Manama%2C%20BH&srb_u=1" target="_blank">hotel booking reservation</a> where we have special rates.</span>

<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p>We look forward to welcoming you to the {{ $details['eventName'] }} to share industry insights, explore networking opportunities, and share valuable industry experiences.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>