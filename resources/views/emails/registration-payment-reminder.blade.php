<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>We are delighted to know that you have initiated the registration process for the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p>To take advantage of the early bird discount, please ensure that your payment fee is settled on or before {{ $details['earlyBirdValidityDate'] }}. Standard rate will be applied after {{ $details['earlyBirdValidityDate'] }}.</p>

<p>Note: <strong><em>NO BADGE</em></strong> will be issued unless payment has been settled and confirmed. Onsite payment is available on a <strong><em>CASH BASIS</em></strong> only.</p>

<p>For further information on how to settle your payment, please contact Analee Candelaria at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a> or call +971 4 451 0666 ext. 116.</p>

@if ($sendInvoice)
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>

<span>&nbsp;</span>
@endif

<p>Thank you and looking forward to welcoming you at the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>. </p>

<p>Kind regards,</p>

<p>GPCA Team</p>
</x-mail::message>