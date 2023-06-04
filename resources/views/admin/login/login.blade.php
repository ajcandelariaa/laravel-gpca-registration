<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>

    {{-- FONT AWESOME LINK --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- TAILWINDCSS --}}
    @vite('resources/css/app.css')

    <style>
        .relative:focus-within i {
            color: #034889;
        }
    </style>
</head>

<body class="bg-loginBg bg-cover bg-center h-screen">

    @if (Session::has('fail') || Session::has('success'))
        <div class="feedback">
            @if (Session::has('fail'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded fixed top-4 left-1/2 transform -translate-x-1/2 w-96"
                    role="alert">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block sm:inline">{{ Session::get('fail') }}</span>
                        </div>
                        <div>
                            <button type="button" class="text-sm leading-none focus:outline-none"
                                onclick="closeFlashMessage()">&times;</button>
                        </div>
                    </div>
                </div>
            @endif

            @if (Session::has('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded fixed top-4 left-1/2 transform -translate-x-1/2 w-96"
                    role="alert">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block sm:inline">{{ Session::get('success') }}</span>
                        </div>
                        <div>
                            <button type="button" class="text-sm leading-none focus:outline-none"
                                onclick="closeFlashMessage()">&times;</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="flex flex-col items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <div class="flex justify-center">
                <img src="{{ asset('assets/images/logo2.jpg') }}" alt="logo" class="w-40">
            </div>
            <form action="/admin/login" method="POST" id="login-form">
                @csrf
                <div class="mt-10">
                    <div class="relative">
                        <input required type="text" name="username" placeholder="Username"
                            class="input-field w-full px-4 py-2 border border-gray-400 rounded-lg pl-10 focus:outline-none focus:border-registrationPrimaryColor focus:border-2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>

                    <div class="relative mt-5">
                        <input required type="password" name="password" placeholder="Password"
                            class="input-field w-full px-4 py-2 border border-gray-400 rounded-lg pl-10 focus:outline-none focus:border-registrationPrimaryColor focus:border-2">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember_me" name="remember_me" class="h-4 w-4 text-registrationPrimaryColor focus:ring-offset-registrationPrimaryColorHover border-gray-300 rounded">
                            <label for="remember_me" class="ml-1 block text-sm text-gray-900">
                              Keep me logged in
                            </label>
                          </div>
                    </div>

                    <div class="text-center mt-3">
                        <input type="submit" value="Login"
                            class=" bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white rounded-md py-1 px-16 mt-5 cursor-pointer">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const inputFields = document.querySelectorAll('.input-field');

        if (document.querySelector('.feedback')) {
            const element = document.querySelector('.feedback');
            inputFields.forEach(function(inputField) {
                inputField.addEventListener('keydown', function(event) {
                    if (element.querySelector('.fixed') && document.querySelector('.feedback')) {
                        closeFlashMessage();
                    }
                });
            });
        }

        function closeFlashMessage() {
            document.querySelector('.fixed').remove();
        }
    </script>
</body>

</html>
