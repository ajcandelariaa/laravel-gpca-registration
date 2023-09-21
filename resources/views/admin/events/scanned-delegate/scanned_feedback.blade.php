<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Scanned badge {{ $status }}</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">
    @if (session('status'))
        @if (session('status') == 'success')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded fixed top-4 left-1/2 transform -translate-x-1/2 w-96"
                role="alert">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded fixed top-4 left-1/2 transform -translate-x-1/2 w-96"
                role="alert">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                </div>
            </div>
        @endif
    @endif



    <div class="flex flex-col items-center justify-center h-screen text-center">
        <img src="{{ asset('assets/images/invoice_logo.png') }}" alt="Company Logo">
        @if ($status == 'success')
            <h1 class="text-5xl font-bold text-gray-800 mt-6">Badge scanned succesfully!!</h1>
            <p class="text-xl text-gray-600 mt-4"></p>
            {{-- <a href="{{ route('admin.event.delegates.detail.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'delegateId' => $delegateId, 'delegateType' => $delegateType]) }}" class="inline-block bg-registrationPrimaryColorHover text-white px-4 py-2 rounded-full hover:bg-registrationPrimaryColor mt-6">View Delegate</a> --}}
        @else
            <h1 class="text-5xl font-bold text-gray-800 mt-6">Oops! Delegate not found</h1>
            <p class="text-xl text-gray-600 mt-4">Incorrect QR code, please try again!</p>
        @endif
    </div>
</body>

</html>
