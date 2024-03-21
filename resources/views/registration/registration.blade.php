<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $pageTitle }}</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    {{-- FONT AWESOME LINK --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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

        .form-container {
            margin-left: 0px;
        }

        .swal-button--confirm {
            background-color: #034889;
            color: #fff;
        }

        .swal-button--cancel {
            background-color: #dd3333;
            color: #fff;
        }


        .swal-button--confirm:hover {
            background-color: #033e75 !important;
        }

        .swal-button--cancel:hover {
            background-color: #cb2e2e !important;
        }

        .cc-show-red-border {
            border: 1px solid red !important;
        }

        .cc-show-green-border {
            border: 1px solid green !important;
        }



        .vertical-progress {
            display: none;
        }

        .horizontal-progress {
            display: block;
        }

        @media only screen and (min-width: 1280px) {
            .form-container {
                margin-left: 360px;
            }

            .vertical-progress {
                display: block;
            }

            .horizontal-progress {
                display: none;
            }
        }



        .package-summary-col {
            display: none;
        }

        .package-summary-row {
            display: block;
        }

        @media only screen and (min-width: 768px) {
            .package-summary-col {
                display: block;
            }

            .package-summary-row {
                display: none;
            }
        }
    </style>
</head>


<body class="min-h-screen flex flex-col">
    <div class="container mx-auto">
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-fill object-center">
    </div>

    @if ($event->category == 'AFS')
        @livewire('spouse-registration-form', ['data' => $event])
    @elseif ($event->category == 'AFV')
        @livewire('visitor-registration-form', ['data' => $event])
    @elseif ($event->category == 'RCCA')
        @livewire('rcc-awards-registration-form', ['data' => $event])
    @elseif ($event->category == 'SCEA')
        @livewire('awards-registration-form', ['data' => $event])
    @else
        @livewire('registration-form', ['data' => $event])
    @endif

    <footer class="bg-registrationPrimaryColor w-full py-5 text-center text-white mt-auto">
        <p>Copyright © 2023 GPCA Registration</p>
    </footer>

    <script src="{{ asset('js/allswal.js') }}"></script>

    <div>
        @include('helpers.registration_loading_screen')
        @include('helpers.registration_loading_screen2')
    </div>

    @livewireScripts()
</body>

</html>
