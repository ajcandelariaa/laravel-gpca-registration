<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> which will be held on {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p style="color: red;">
    This is a kind reminder to process your registration invoice to complete your delegate registration.
</p>

<p>To take advantage of the early bird discount, please ensure to settle your invoice on or before {{ $details['earlyBirdValidityDate'] }}. Standard rate will be applied after {{ $details['earlyBirdValidityDate'] }}.</p>

<p>
    Note: <strong><em>NO BADGE</em></strong> will be issued unless payment has been settled and confirmed. Onsite payment is available on a <strong><em>CASH BASIS</em></strong> only.
</p>

<p>Your registration details as follows:</p>

<span>
    Delegate Full name: <strong>{{  $details['name'] }}</strong>
    <br>
    Job title: <strong>{{  $details['jobTitle'] }}</strong>
    <br>
    Company name: <strong>{{  $details['companyName'] }}</strong>
    <br>
    Amount paid: <strong>$ {{ number_format($details['amountPaid'], 2, '.', ',') }}</strong>
    <br>
    Transaction ID: <strong>{{  $details['transactionId'] }}</strong>
</span>
<br><br>
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
<span>&nbsp;</span>

<p>To request changes, kindly respond to this email at the earliest, to rectify your badge.</p>

<h3>GENERAL INFORMATION</h3>

<p>Non – GPCC participants attending the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>, must process their own visa. Visa applications can be submitted online at <a href="https://www.evisa.gov.bh/">https://www.evisa.gov.bh/</a></p>

<p>For the hotel accommodation, kindly <a href="http://gpcaresponsiblecare.com/wp-content/uploads/2023/07/The-Ritz-Carlton-Bahrain-Reservation-Form.pdf"><strong>click</strong></a> to download the form and submit your details to Prakash at <a href="mailto:Prakash.ramaiah@ritzcarlton.com">Prakash.ramaiah@ritzcarlton.com</a> or <a href="mailto:chahrazed.jassem@ritzcarlton.com">chahrazed.jassem@ritzcarlton.com</a>. You may also contact the following numbers for any additional inquiries: 973-66388039 or 973-39957141.</p>


<p>For the latest updates on the event, please visit the event website at <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

Best regards,
<br><br>
GPCA Team
</x-mail::message>