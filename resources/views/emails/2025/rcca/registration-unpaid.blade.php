<x-mail::message>
<p class="sub">Dear {{ $details['name'] }},</p>

<p class="sub" style="margin-top: 15px;">Greetings from GPCA!</p>

<p class="sub" style="margin-top: 15px;">Thank you for your entry submission for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> scheduled to take place on {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p class="sub" style="margin-top: 15px; color: red;"><em>This is a kind reminder to process your invoice in order to complete your entry submission.</em></p>

<p class="sub" style="margin-top: 5px; color: red;">Note: Unpaid submission entries will not be considered successful submission. Please ensure to settle your invoice on or before 1<sup>st</sup> August. </p>

<p class="sub" style="margin-top: 15px;">We have received the following details you provided, which have been shared with the GPCA team:</p>

<p class="sub" style="margin-top: 20px;">Full name: {{  $details['name'] }}</p>
<p class="sub">Job title: {{  $details['jobTitle'] }}</p>
<p class="sub">Company name: {{  $details['companyName'] }}</p>
<p class="sub">E-mail address: {{  $details['emailAddress'] }}</p>
<p class="sub">Mobile number: {{  $details['mobileNumber'] }}</p>
<p class="sub">City, Country: {{  $details['city'] }}, {{  $details['country'] }}</p>
<p class="sub">Submission category: {{  $details['category'] }}</p>
<p class="sub">Sub-category: {{  $details['subCategory'] }}</p>
<p class="sub"><strong><a href="{{ $details['downloadLink'] }}{{ $details['entryFormId'] }}">Download entry form</a></strong></p>
@if (count($details['supportingDocumentsDownloadId']) > 0)
    <p class="sub">Download supporting documents:</p>
    <ul>
    @foreach ($details['supportingDocumentsDownloadId'] as $index => $documentId)
        <li><a href="{{ $details['downloadLink'] }}{{ $documentId }}">Supporting document {{ $index + 1 }}</a></li>
    @endforeach
    </ul>
@endif
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

<p class="sub" style="margin-top: 15px;">Should you have any further inquiries or require assistance, please do not hesitate to reach out to Mohammed Seraj at <a href="mailto:mohamed@gpca.org.ae">mohamed@gpca.org.ae</a> or call +971 451 0666 ext. 121.</p>

<p class="sub" style="margin-top: 15px;">Thank you and we wish you the best of luck in your award submissions.</p>

<p class="sub" style="margin-top: 15px;">Kind Regards,</p>
<p class="sub">GPCA Team</p>
</x-mail::message>