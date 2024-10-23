<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>

    {{-- VITE --}}
    @vite('resources/css/app.css')

    {{-- LIVEWIRE --}}
    @livewireStyles()
</head>

<body>
    <div class="font-montserrat mb-10">
        <img src="https://www.gpcaforum.com/wp-content/uploads/2024/10/digital-helper-banner.png"
            class="w-full object-fill object-center md:hidden block">

        <img src="https://www.gpcaforum.com/wp-content/uploads/2024/10/af-reigstration-banner-latest-scaled.jpg"
            class="w-full object-fill object-center md:block hidden">

        @livewire('digital-helper', ['event' => $event])
    </div>

    <script src="{{ asset('js/allswal.js') }}"></script>
    
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.emit('loadDelegates');
        });
    </script>

    <div>
        @include('helpers.registration_loading_screen')
    </div>

    @livewireScripts()
</body>

</html>
