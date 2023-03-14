<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>

    {{-- FONT AWESOME LINK --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- VITE --}}
    @vite('resources/css/app.css')

    {{-- LIVEWIRE --}}
    @livewireStyles()

    <style>
        .add-event-form select:required:invalid {
            color: #afafaf;
        }

        .add-event-form option {
            color: #000;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="bg-registrationPrimaryColor">
        <div class="container mx-auto py-3 px-5">
            <div class="flex justify-between">
                <div>
                    <img src="{{ asset('assets/images/logo2.jpg') }}" class="max-h-16" alt="logo">
                </div>
                <div class="text-white font-semibold flex items-center gap-10">
                    <a href="{{ route('admin.dashboard.view') }}"
                        class="{{ request()->is('admin/dashboard*') ? 'text-dashboardNavItemHoverColor' : 'hover:underline' }}">Dashboard</a>
                    <a href="{{ route('admin.event.view') }}"
                        class="{{ request()->is('admin/event*') ? 'text-dashboardNavItemHoverColor' : 'hover:underline' }}">Manage
                        Events</a>
                    <a href="{{ route('admin.member.view') }}"
                        class="{{ request()->is('admin/member*') ? 'text-dashboardNavItemHoverColor' : 'hover:underline' }}">Manage
                        Members</a>
                    <a href="{{ route('admin.delegate.view') }}"
                        class="{{ request()->is('admin/delegate*') ? 'text-dashboardNavItemHoverColor' : 'hover:underline' }}">Manage
                        Delegates</a>
                    <a href="{{ route('admin.logout') }}" class="hover:underline">Logout</a>
                    <br><br>
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    @livewireScripts()
</body>

</html>
