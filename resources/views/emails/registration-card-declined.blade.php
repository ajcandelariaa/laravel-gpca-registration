<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Thank you for registering to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> taking place from {{ $details['eventDates'] }} at the {{ $details['eventLocation'] }}.</p>

<p>Due to the problem with your card, we have been unable to charge your account. Here are the possible reasons for the declined payment are the following: </p>

<ul>
    <li style="mso-special-format:bullet;">Your Card or personal details entered do not match the information held by your card Issuer.</li>
    <li style="mso-special-format:bullet;">You do not have enough balance on your card to cover the amount of the purchase.</li>
    <li style="mso-special-format:bullet;">Your bank referred to the transaction for an authorization code or further identity checks.</li>
    <li style="mso-special-format:bullet;">Your card has been reported as lost or stolen and been canceled by your bank.</li>
    <li style="mso-special-format:bullet;">Your card has or is due to expire and has been replaced by your bank.</li>
    <li style="mso-special-format:bullet;">Your card has recently been replaced by your bank but has not yet been activated.</li>
    <li style="mso-special-format:bullet;">Your card cannot be used for card-not-present transactions</li>
    <li style="mso-special-format:bullet;">There may be a problem with your bank’s authorization system.</li>
</ul>

<br>

<p>Although your registration is saved in our system. You can still pay by bank transfer through the below bank account.</p>

<br>

<p>In favour of: Gulf Petrochemicals & Chemicals Association Mashreq Bank Riqqa Branch, Deira, P.O. Box5511, Dubai, UAE</p>
<p>USD Acct No. <strong>0190-00-05007-7</strong></p>
<p>IBAN No. <strong>AE360330000019000050077</strong></p>
<p>Swift Code <strong>BOMLAEAD</strong></p>

<br><br>
<p>For more information or queries please contact Analee Candelaria at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a> | +971 4 451 0666 Ext 116.</p>
<br>

Kind regards,
<br><br>
GPCA Team
</x-mail::message>