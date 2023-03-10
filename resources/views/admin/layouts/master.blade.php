<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>

    {{-- FONT AWESOME LINK --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- VITE --}}
    @vite('resources/css/app.css')

    {{-- LIVEWIRE --}}
    @livewireStyles()
</head>

<body class="bg-gray-100">
    <div class="bg-blue-600">
        <div class="container mx-auto py-3 px-5">
            <div class="flex justify-between">
                <div>
                    <img src="{{ asset('assets/images/logo2.jpg') }}" class="max-h-16" alt="logo">
                </div>
                <div class="text-white flex items-center gap-10">
                    <a href="/admin/dashboard"
                        class="{{ request()->is('admin/dashboard*') ? 'underline' : '' }} hover:underline">Dashboard</a>
                    <a href="/admin/event"
                        class="{{ request()->is('admin/event*') ? 'underline' : '' }} hover:underline">Manage
                        Events</a>
                    <a href="/admin/member"
                        class="{{ request()->is('admin/member*') ? 'underline' : '' }} hover:underline">Manage
                        Members</a>
                    <a href="/admin/logout" class="hover:underline">Logout</a>
                    <br><br>
                </div>
            </div>
        </div>
    </div>
    
    @yield('content')
    @livewireScripts()
</body>

</html>
