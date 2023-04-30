<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Thank you for your registration for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>. Your payment has been received successfully.</p>

<p>Below is the summary of your payment for your reference.</p>

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
            <td style="border: 1px solid black; padding: 2px;">{{ $details['balance'] }}</td>
        </tr>
    </tbody>
</table>

<br>

<p>For further queries, you may contact below.</p>

<span>
    Best regards,
    <br><br>
    <strong>Analee Candelaria</strong>
    <br>
    <em>Senior Events Accounts Specialist</em>
    <br>
    Tel : <strong>+971 4 451 0666 Ext 116</strong> | Fax : <strong>+971 4 451 0777</strong>
    <br>
    Email: <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a>
    <br>
    <a href="https://www.gpca.org.ae/" target="_blank">www.gpca.org.ae</a>
</span>
</x-mail::message>
