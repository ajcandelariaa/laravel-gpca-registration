<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>GPCA Registration</title>

    {{-- FONT AWESOME LINK --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite('resources/css/app.css')
</head>

<body>
    <div class="container mx-auto py-8">
        <div class="px-4">
            <h1 class="text-4xl font-bold text-center text-gray-800 my-4">Welcome to GPCA Registration</h1>

            <h3 class="text-xl mt-10 font-bold uppercase">Upcoming Events</h3>
            @if ($upcomingEvents == null)
                <div class="text-red-400 py-1 w-full rounded-md mt-2 font-medium">
                    There are no upcoming events yet.
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 mt-5">
                    @foreach ($upcomingEvents as $event)
                        <div class="bg-gray-100 px-4 py-4 rounded-lg">
                            <div class="flex items-center gap-4">
                                <img src="{{ Storage::url($event['eventLogo']) }}" alt="" class="h-16">
                                <p class="font-bold text-3xl">{{ $event['eventName'] }}</p>
                                <p
                                    class="text-registrationPrimaryColor rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                                    {{ $event['eventCategory'] }}</p>
                            </div>

                            <div class="flex gap-3 items-center mt-5 text-registrationPrimaryColor">
                                <i class="fa-solid fa-location-dot"></i>
                                <p>{{ $event['eventLocation'] }}</p>
                            </div>

                            <div class="flex gap-3 items-center mt-2 text-registrationPrimaryColor">
                                <i class="fa-solid fa-calendar-days"></i>
                                <p>{{ $event['eventDate'] }}</p>
                            </div>

                            <div class="mt-5">
                                {{ $event['eventDescription'] }}
                            </div>

                            <a href="{{ $event['eventLink'] }}"
                                class="inline-block bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-white font-medium py-1 px-10 rounded-lg transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110 mt-4 animate-bounce"
                                target="_blank">
                                Register now
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <h3 class="text-xl mt-10 font-bold uppercase">Past Events</h3>
            @if ($pastEvents == null)
                <div class="text-red-400 py-1 w-full rounded-md mt-2 font-medium">
                    There are no past events yet.
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 mt-5">
                    @foreach ($pastEvents as $event)
                        <div class="bg-gray-100 px-4 py-4 rounded-lg">
                            <div class="flex items-center gap-4">
                                <img src="{{ Storage::url($event['eventLogo']) }}" alt="" class="h-16">
                                <p class="font-bold text-3xl">{{ $event['eventName'] }}</p>
                                <p
                                    class="text-registrationPrimaryColor rounded-full border border-registrationPrimaryColor px-4 font-bold text-sm">
                                    {{ $event['eventCategory'] }}</p>
                            </div>

                            <div class="flex gap-3 items-center mt-5 text-registrationPrimaryColor">
                                <i class="fa-solid fa-location-dot"></i>
                                <p>{{ $event['eventLocation'] }}</p>
                            </div>

                            <div class="flex gap-3 items-center mt-2 text-registrationPrimaryColor">
                                <i class="fa-solid fa-calendar-days"></i>
                                <p>{{ $event['eventDate'] }}</p>
                            </div>

                            <div class="mt-5">
                                {{ $event['eventDescription'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</body>

</html>
