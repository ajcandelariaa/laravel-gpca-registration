<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>

    {{-- VITE --}}
    @vite('resources/css/app.css')
</head>


<body class="min-h-screen flex flex-col">
    <div class="container mx-auto">
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-fill object-center">

        <div class="my-10 text-lg">
            <h1 class="text-4xl">Transaction Failed </h1>

            <p class="mt-5">Thank you for registering to attend the <a href="{{ $event->link }}" target="_blank"
                    class="hover:underline font-semibold">{{ $event->name }}</a> taking place from
                {{ $eventFormattedDate }} at
                the {{ $event->location }}. </p>

            <h1 class="text-4xl mt-10">Although your registration is saved in our system. You can still pay by bank transfer through the below bank account. </h1>

            <div class="mt-5 bg-registrationCardBGColor p-5 rounded-lg">
                <div class="text-registrationPrimaryColor font-bold text-lg">
                    Bank Details
                </div>

                <div class="ml-5 text-black mt-2">
                    <p>In favor of: <strong>Gulf Petrochemicals & Chemicals Association</strong></p>
                    <p>Mashreq Bank</p>
                    <p>Riqa Branch, Deira, P.O. Box 5511, Dubai</p>
                    <p class="mt-5">USD Acct No. <strong>{{ $bankDetails['accountNumber'] }}</strong></p>
                    <p>IBAN No. <strong>{{ $bankDetails['ibanNumber'] }}</strong></p>
                    <p>Swift Code <strong>BOMLAEAD</strong></p>
                </div>
            </div>

            <p class="mt-5">For more information or queries please contact Analee Candelaria at <a href="mailto:analee@gpca.org.ae"
                    class="underline">analee@gpca.org.ae</a> | +971 4 451 0666 Ext 116.</p>

            <p class="mt-5">You can download your invoice from the below link </p>

            <a href="{{ $invoiceLink }}" target="_blank"
                class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white py-2 px-5 rounded-md text-lg text-center mt-5 inline-block">Download
                Invoice</a>
        </div>
    </div>


    <footer class="bg-registrationPrimaryColor w-full py-5 text-center text-white mt-auto">
        <p>Copyright © 2023 GPCA Registration</p>
    </footer>
</body>

</html>
