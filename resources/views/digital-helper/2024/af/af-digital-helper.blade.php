<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Roboto&display=swap"
        rel="stylesheet">

    {{-- VITE --}}
    @vite('resources/css/app.css')

    {{-- LIVEWIRE --}}
    @livewireStyles()
</head>

<body class="font-montserrat">
    <div class="mb-10">
        <img src="https://www.gpcaforum.com/wp-content/uploads/2024/10/digital-helper-banner-design-v2.jpg"
            class="w-full object-fill object-center md:hidden block">

        <img src="https://www.gpcaforum.com/wp-content/uploads/2024/10/af-reigstration-banner-latest-scaled.jpg"
            class="w-full object-fill object-center md:block hidden">

        @livewire('digital-helper', ['event' => $event])
    </div>

    <script src="{{ asset('js/allswal.js') }}"></script>

    <script>
        document.addEventListener('livewire:load', function() {
            let dhLoadingScreen = document.getElementById('dh-loading-screen');
            let dhLoadingScreenText = document.getElementById('loading-text');
            dhLoadingScreenText.innerText = "Fetching data...";
            dhLoadingScreen.classList.remove('hidden');
            console.log("add dh loading screen");
            Livewire.emit('loadDelegates');
        });
    </script>

    <div>
        @include('helpers.dh_loading_screen')
    </div>

    @livewireScripts()
</body>

</html>
