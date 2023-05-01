<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 Not Found</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">
    <div class="flex flex-col items-center justify-center h-screen">
        <img src="{{ asset('assets/images/invoice_logo.png') }}" alt="Company Logo" >
        <h1 class="text-5xl font-bold text-gray-800 mt-6">Oops! Page not found</h1>
        <p class="text-xl text-gray-600 mt-2">The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ env('APP_URL') }}" class="inline-block bg-registrationPrimaryColorHover text-white px-4 py-2 rounded-full hover:bg-registrationPrimaryColor mt-6">Return to Home</a>
    </div>
</body>

</html>
