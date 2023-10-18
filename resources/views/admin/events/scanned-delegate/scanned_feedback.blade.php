<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Scanned badge {{ $status }}</title>
    @vite('resources/css/app.css')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        @if ($status == 'Success')
            <div class="flex justify-center">
                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
            </div>

            <p class="text-green-600 text-xl font-semibold text-center mt-2">Badge Scanned Successfully!</p>
            <div class="mt-4 text-center">
                <div>
                    <p class="text-gray-700">Name:</p>
                    <p class="font-semibold">{{ $name }}</p>
                </div>
                <div class="mt-4">
                    <p class="text-gray-700">Job Title:</p>
                    <p class="font-semibold">{{ $jobTitle }}</p>
                </div>
                <div class="mt-4">
                    <p class="text-gray-700">Company:</p>
                    <p class="font-semibold">{{ $companyName }}</p>
                </div>
            </div>

            <div class="flex justify-center">
                <a href="{{ route('admin.event.delegates.detail.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId, 'delegateId' => $delegateId, 'delegateType' => $delegateType]) }}"
                    class="inline-block bg-registrationPrimaryColorHover text-white px-4 py-1 rounded-lg hover:bg-registrationPrimaryColor mt-6">View
                    Delegate</a>
            </div>
        @else
            <div class="text-center">
                <div class="mb-3">
                    <img src="https://img.icons8.com/external-flaticons-flat-flat-icons/64/external-unauthorized-privacy-flaticons-flat-flat-icons-2.png"
                        class="w-16 mx-auto" />
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Delegate not found</h1>
                <p class="text-gray-600">Invalid QR Code, please user the correct QR code!</p>
            </div>
        @endif
    </div>
</body>

</html>
