<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Greetings from GPCA!</p>

<p>We are delighted that you have started your registration process to attend the <a href="{{ $details['eventLink'] }}" target="_blank">{{ $details['eventName'] }}</a>.</p>

<p>Please note that <strong><em>NO BADGE</em></strong> will be issued unless payment has been settled and confirmed. Kindly ensure to process your payment before the event date. Onsite payment is available on a <strong><em>CASH BASIS</em></strong> only.</p>

<p>Kindly contact Analee Candelaria at <a href="mailto:analee@gpca.org.ae">analee@gpca.org.ae</a> or call +971 4 451 0666 ext. 116 for more information on how to settle your payment.</p>

Kind regards,
<br><br>
GPCA Team
</x-mail::message>