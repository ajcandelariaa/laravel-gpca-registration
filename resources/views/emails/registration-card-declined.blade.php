<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>We regret to inform you that your payment registration for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a> has been declined.</p>

<p>We apologize for the inconvenience, but we have encountered a problem charging your account due to issues with your card. </p>

<p>Here are the possible reasons for the declined payment:</p>

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
@if ($sendInvoice)
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
<span>&nbsp;</span>
@else
<br><br>
@endif

<p>Please contact your bank for further information regarding the declined payment. Alternatively, you can reach out to Analee Candelaria at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a> or +971 4 451 0666 ext. 116 to explore alternative payment options that may be available to you.</p>

Kind regards,
<br><br>
GPCA Team
</x-mail::message>