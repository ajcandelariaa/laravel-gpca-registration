<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>We are delighted that you have started your registration to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p>Please note that no badge will be issued without prior payment for registration. Kindly ensure that you either make the payment before the event or make the payment onsite. Onsite mode of payment is cash only.</p>

<p>Kindly contact Analee Candelaria at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a> +971 4 451 0666 ext. 116 for more information on how to settle your payment.</p>

Kind regards,<br>
GPCA Team
</x-mail::message>