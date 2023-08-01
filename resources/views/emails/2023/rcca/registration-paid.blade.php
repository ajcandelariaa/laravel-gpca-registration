<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>Thank you for your entry submission for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> scheduled to take place on {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

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

<p>Your abstract will be reviewed by the jury and the winners and runner ups will be announced on {{ $details['eventDates'] }} during the 5th GPCA Responsible Care Conference gala dinner. </p>

<p><strong>Confidentiality:</strong> The information contained in the submittal and all related materials are privileged and solely for the use of the GPCA Responsible Care Excellence Awards.</p>

<p>The participating organization and GPCA require that information shall be strictly protected by the Reviewer/Judge and will not be disclosed nor distributed to any other parties.</p>

<p>Should you have any further inquiries or require assistance, please do not hesitate to reach out to Mohammed Seraj at <a href="mailto:mohamed@gpca.org.ae">mohamed@gpca.org.ae</a> or call +971 451 0666 ext. 121.</p>

<p>Thank you and we wish you the best of luck in your award submissions.</p>

Kind regards,
<br><br>
GPCA Team
</x-mail::message>