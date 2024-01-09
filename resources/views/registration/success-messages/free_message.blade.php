<div class="text-lg">
    <h1 class="text-4xl">Transaction Success </h1>

    <p class="mt-5">Thank you for registering to attend the <a href="{{ $event->link }}" target="_blank"
            class="hover:underline font-semibold">{{ $event->name }}</a> taking place from {{ $eventFormattedDate }} at
        the {{ $event->location }}. </p>

    <p class="mt-5 text-red-600 italic">Please note that your registration is subject to confirmation from one of our
        team members. We will review the registration details you've provided to ensure we have the accurate information
        to make the necessary badge arrangements for your optimal event experience.</p>

    @if ($event->category != 'GLF')
        <a href="{{ $invoiceLink }}" target="_blank"
            class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white py-2 px-5 rounded-md text-lg text-center mt-5 inline-block">Download
            Invoice</a>
    @endif

    <p class="mt-5">If you require further assistance with the confirmation process, feel free to contact us at <a
            href="mailto:forumregistration@gpca.org.ae">forumregistration@gpca.org.ae</a>. </p>
</div>
