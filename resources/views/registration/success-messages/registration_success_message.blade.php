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

        <div class="my-10">
            @if ($paymentStatus == 'free')
                @include('registration.success-messages.free_message')
            @elseif ($paymentStatus == 'unpaid')
                @include('registration.success-messages.unpaid_message')
            @else
                @include('registration.success-messages.paid_message')
            @endif
        </div>
    </div>


    <footer class="bg-registrationPrimaryColor w-full py-5 text-center text-white mt-auto">
        <p>Copyright © 2023 GPCA Registration</p>
    </footer>
</body>

</html>
