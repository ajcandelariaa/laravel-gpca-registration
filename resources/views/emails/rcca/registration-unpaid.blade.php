<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for your entry submission for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> scheduled to take place on {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p><em>This is a kind reminder to process your invoice in order to complete your entry submission.</em></p>

<p style="color: red;"><em>Note: Unpaid submission entries will not be considered successful submission. Please ensure to settle your invoice on or before 15th July. </em></p>

<p>
    We have received the following details you provided, which have been shared with the GPCA team:
</p>

<span>
    Full name: <strong>{{  $details['name'] }}</strong>
    <br>
    Job title: <strong>{{  $details['jobTitle'] }}</strong>
    <br>
    Company name: <strong>{{  $details['companyName'] }}</strong>
    <br>
    E-mail address: <strong>{{  $details['emailAddress'] }}</strong>
    <br>
    Mobile number: <strong>{{  $details['mobileNumber'] }}</strong>
    <br>
    City, Country: <strong>{{  $details['city'] }}, {{  $details['country'] }}</strong>
    <br>
    Submission category: <strong>{{  $details['category'] }}</strong>
    <br>
    Sub-category: <strong>{{  $details['subCategory'] }}</strong>
    <br><br>
    <strong><a href="{{ $details['downloadLink'] }}{{ $details['entryFormId'] }}">Download entry form</a></strong>
    <br>
    @if (count($details['supportingDocumentsDownloadId']) > 0)
        <br>
        Download supporting documents:
        <ul>
        @foreach ($details['supportingDocumentsDownloadId'] as $index => $documentId)
            <li><a href="{{ $details['downloadLink'] }}{{ $documentId }}">Supporting document {{ $index + 1 }}</a></li>
        @endforeach
        </ul>
    @endif
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

<p>Should you have any further inquiries or require assistance, please do not hesitate to reach out to Mohammed Seraj at <a href="mailto:mohamed@gpca.org.ae">mohamed@gpca.org.ae</a> or call +971 451 0666 ext. 121.</p>

<p>Thank you and we wish you the best of luck in your award submissions.</p>

Kind regards,
<br><br>
GPCA Team
</x-mail::message>