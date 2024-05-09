<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&family=Roboto&display=swap"
        rel="stylesheet">


    {{-- FONT AWESOME LINK --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- VITE --}}
    @vite('resources/css/app.css')

    {{-- LIVEWIRE --}}
    @livewireStyles()

    <style>
        #preview {
            width: 300px;
            height: 300px;
            object-fit: cover;
            margin: 0px auto;
            border-radius: 10px;
            padding: 10px;
            transition: 1s
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes scan {
            0% {
                transform: scaleX(0);
                opacity: 0.3;
            }

            50% {
                transform: scaleX(1);
                opacity: 0.6;
            }

            100% {
                transform: scaleX(0);
                opacity: 0.3;
            }
        }

        .animate-scan {
            animation: scan 2s infinite;
        }
    </style>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
</head>

<body class="font-montserrat">
    @livewire('scan-qr', ['eventCategory' => $eventCategory, 'eventId' => $eventId])

    @include('helpers.registration_loading_screen')

    @livewireScripts()

    <script>
        window.addEventListener("invalid-qr", (event) => {
            swal({
                title: event.detail.message,
                text: event.detail.text,
                icon: event.detail.type,
            });
        });

        window.addEventListener("scanStarted", () => {
            let videoTag = document.getElementById('preview');
            let closeScanner = document.getElementById('closeScannerBtn');
            let scannAnimation = document.getElementById('scan-animation');

            var scanner = new Instascan.Scanner({
                continues: true,
                video: videoTag,
                mirror: false,
                captureImage: false,
                backgroundScan: false,
                refractoryPeriod: 3000,
                scanPeriod: 1,
            });

            scanner.addListener('scan', function(content) {
                let registrationLloadingScreen = document.getElementById('registration-loading-screen');
                registrationLloadingScreen.classList.remove('hidden');
                Livewire.emit("scannedSuccess", content);
                scanner.stop();
            });

            closeScanner.addEventListener('click', function() {
                Livewire.emit("scannerStoppedSuccess");
                scanner.stop();
            });

            Instascan.Camera.getCameras().then(function(cameras) {
                if (cameras.length > 0) {
                    for (var i = 0; i < cameras.length; i++) {
                        var message = "Index " + i + ": " + cameras[i].id + ": " + cameras[i].name;
                        alert(message);
                    }
                    scanner.start(cameras[cameras.length - 1]).then(function() {
                        scannAnimation.classList.remove('hidden');
                    });
                } else {
                    console.error('No cameras found.');
                    alert('No cameras found.');
                }
            }).catch(function(e) {
                console.error(e);
                alert(e);
            });
        });

        window.addEventListener("scan-qr-success", () => {
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.add('hidden');

            swal({
                title: event.detail.message,
                text: event.detail.text,
                icon: event.detail.type,
            });
        });

        window.addEventListener("remove-loading-screen", () => {
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.add('hidden');
        });

        window.addEventListener("add-loading-screen", (event) => {
            let registrationLloadingScreen = document.getElementById('registration-loading-screen');
            registrationLloadingScreen.classList.remove('hidden');
            Livewire.emit(event.detail.redirectFunction)
        });
    </script>
</body>

</html>
