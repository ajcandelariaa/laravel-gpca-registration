<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GPCA Registration</title>
    @vite('resources/css/app.css')

    {{-- intlTelInput --}}
    @include('helpers.intlTelInput')
</head>

<body>
    <h1 class="text-4xl text-center bg-red-400 text-white">Welcome to GPCA Registration</h1>

    {{-- <input id="phoneTest" class="" type="tel" name="phone" maxlength="15" />
    <br>
    <span id="error-msg" class="hide"></span>
    <p id="result"></p> --}}

    {{-- <script src="{{ asset('js/phoneValidation.js') }}"></script> --}}

    <script>
        if (self === top) {

        } else {
            top.location = self.location;
        }
    </script>
</body>

</html>
