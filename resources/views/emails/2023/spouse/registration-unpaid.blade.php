<x-mail::message>
<p>Dear {{ $details['name'] }},</p>

<p>Thank you for your interest in the Spouse Program at the 17<sup>th</sup> Annual GPCA Forum in Qatar. We are excited to welcome you to this exclusive experience!</p>

<p>Your booking form has been received, and we are delighted to confirm your reservation. Get ready for an unforgettable journey filled with exploration, networking, and cultural immersion</p>

<span><strong>Key Details:</strong></span>
<br>
<span>
    <strong>Event Dates:</strong> {{  $details['eventDatesDescription'] }}
    <br>
    <strong>Location:</strong> Qatar, a hub of architectural marvels and cultural richness.
    <br>
    <strong>Program Highlights:</strong> Enjoy guided tours of iconic landmarks, immersive cultural experiences, and networking opportunities with fellow spouses. As a valued participant, you'll witness Qatar's architectural wonders, experience the delightful weather in December, and engage in discussions that are vital to the petrochemical sector's growth in the region.
</span>
<br><br>

<span><strong>Booking Details:</strong></span>
<br>
<span>
    <strong>Name:</strong> {{  $details['name'] }}
    <br>
    <strong>Accompanying Delegate Name:</strong>  {{  $details['referenceDelegateName'] }}
    <br>
    <strong>Email Address:</strong> {{  $details['emailAddress'] }}
    <br>
    <strong>Mobile Phone Number (While in Qatar):</strong> {{  $details['mobileNumber'] }}
    <br>
    <strong>Country:</strong> {{  $details['country'] }}
    <br>
    <strong>City:</strong> {{  $details['city'] }}
    <br>
    <strong>Amount paid:</strong> $ {{ number_format($details['amountPaid'], 2, '.', ',') }}
    <br>
    <strong>Transaction ID:</strong> {{  $details['transactionId'] }}
</span>
<br><br>
<x-mail::button :url="$details['invoiceLink']" color="registration">
Download invoice
</x-mail::button>
<span>&nbsp;</span>

<p>Stay tuned for updates and a detailed itinerary closer to the event. If you have any specific preferences or questions, feel free to reach out to our dedicated support team.</p>

<p>We look forward to providing you with a truly exceptional Spouse Program experience. Your presence will add to the success of this event.</p>

Best regards,
<br>
GPCA Events Team
</x-mail::message>