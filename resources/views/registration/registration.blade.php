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

    {{-- intlTelInput --}}
    {{-- @include('helpers.intlTelInput') --}}

    {{-- VITE --}}
    @vite('resources/css/app.css')

    {{-- LIVEWIRE --}}
    @livewireStyles()

    <style>
        select:required:invalid {
            color: #afafaf;
        }

        option {
            color: #000;
        }
    </style>
</head>


<body class="min-h-screen flex flex-col">
    <div class="container mx-auto">
        <img src="{{ asset('assets/images/reg-banner.png') }}" alt=""
            class="h-full w-full object-fill object-center">
    </div>

    @livewire('registration-form')
    @livewireScripts()

    <footer class="bg-registrationPrimaryColor w-full py-5 text-center text-white mt-auto">
        <p>Copyright © 2023 GPCA Registration</p>
    </footer>

    <script>
        window.onbeforeunload = function(e) {
            return "Are you sure?";
        }
    </script>
</body>

</html>
