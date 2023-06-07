<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Thank you for your registering for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>. We have successfully received your payment.</p>

<p>Please find below a summary of your payment for your reference.</p>

<table style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr align="center" valign="middle">
            <th style="border: 1px solid black; padding: 5px;">Description</th>
            <th style="border: 1px solid black; padding: 5px;">Invoice amount</th>
            <th style="border: 1px solid black; padding: 5px;">Amount paid</th>
            <th style="border: 1px solid black; padding: 5px;">Balance</th>
        </tr>
    </thead>
    <tbody>
        <tr align="center" valign="middle">
            <td style="border: 1px solid black; padding: 2px;">{{ $details['eventName'] }} registration fees</td>
            <td style="border: 1px solid black; padding: 2px;">$ {{ number_format($details['invoiceAmount'], 2, '.', ',') }}</td>
            <td style="border: 1px solid black; padding: 2px;">$ {{ number_format($details['amountPaid'], 2, '.', ',') }}</td>
            <td style="border: 1px solid black; padding: 2px;">$ {{ number_format($details['balance'], 2, '.', ',') }}</td>
        </tr>
    </tbody>
</table>

<br>

<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
<span>&nbsp;</span>

<p>For further information, you may contact the following:</p>

<span>
    <strong>Analee Candelaria</strong>
    <br>
    <em>Senior Events Accounts Specialist</em>
    <br>
    Tel : <strong>+971 4 451 0666 Ext 116</strong> | Fax : <strong>+971 4 451 0777</strong>
    <br>
    Email: <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a>
    <br>
    <a href="https://www.gpca.org.ae/" target="_blank">www.gpca.org.ae</a>
    <br>
</span>

<p>Thank you.</p>

<p>Kind regards,</p>

<p>GPCA Team</p>
</x-mail::message>
