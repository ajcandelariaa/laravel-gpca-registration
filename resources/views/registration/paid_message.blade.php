<div class="text-lg">
    <h1 class="text-4xl">Transaction Success </h1>

    <p class="mt-5">Thank you for registering to attend the <a href="{{ $event->link }}" target="_blank"
            class="hover:underline font-semibold">{{ $event->name }}</a> taking place from {{ $eventFormattedDate }} at
        the {{ $event->location }}. </p>

    <h1 class="text-4xl mt-10">Your registration is confirmed. You can download your invoice from the below link.</h1>

    <a href="{{ $invoiceLink }}" target="_blank" class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white py-2 px-5 rounded-md text-lg text-center mt-5 inline-block">Download Invoice</a>

    <p class="mt-5">To collect your badge onsite, please present your business card as a reference to ease the verification process. To request changes, kindly contact Jovelyn Sadoguio at <a href="mailto:forumregistration@gpca.org.ae" class="underline">forumregistration@gpca.org.ae</a> | +971 4 451 0666 ext. 153.</p>
</div>