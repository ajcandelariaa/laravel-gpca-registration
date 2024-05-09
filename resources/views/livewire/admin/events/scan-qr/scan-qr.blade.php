<div class="min-h-screen flex flex-col">
    <header>
        <a href="{{ route('admin.scanned.delegate.list.view', ['eventCategory' => $eventCategory, 'eventId' => $eventId]) }}">
            <img src="{{ Storage::url($eventBanner) }}" class="w-full">
        </a>
    </header>

    <main class="container mx-auto my-10 flex-1 flex items-center justify-center">
        @if ($state == null)
            <div class="flex flex-col justify-center items-center">
                <button type="button" class="bg-registrationPrimaryColor text-white text-lg rounded-xl py-5 px-14"
                    wire:click.prevent="qrCodeScannerClicked">Scan QR Code</button>
            </div>
        @else
            @if ($state == 'qrCodeScanning')
                <div
                    class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 backdrop-filter backdrop-blur-md flex flex-col items-center justify-center z-50">
                    <div class="relative overflow-hidden rounded-lg border-4 border-gray-300">
                        {{-- <div class="hidden absolute inset-0 bg-gradient-to-r from-registrationPrimaryColor to-registrationSecondaryColor opacity-0 animate-scan m-2"
                            id="scan-animation"></div> --}}
                        <video id="preview" playsinline></video>
                    </div>

                    <p id="closeScannerBtn" class="text-white font-semibold underline mt-10 text-xl cursor-pointer">
                        Close scanner</p>
                </div>
            @else
                <div class="flex flex-col justify-center items-center">
                    <div class="flex justify-center">
                        <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                    </div>

                    <p class="text-green-600 text-xl font-semibold text-center mt-2">Badge Scanned Successfully!</p>
                    <div class="mt-4 text-center">
                        <div>
                            <p class="text-gray-700">Name:</p>
                            <p class="font-semibold">{{ $name }}</p>
                        </div>
                        <div class="mt-4">
                            <p class="text-gray-700">Job Title:</p>
                            <p class="font-semibold">{{ $jobTitle }}</p>
                        </div>
                        <div class="mt-4">
                            <p class="text-gray-700">Company:</p>
                            <p class="font-semibold">{{ $companyName }}</p>
                        </div>
                        <div class="mt-4">
                            <p class="text-gray-700">Badge type:</p>
                            <p class="font-semibold">{{ $badgeType }}</p>
                        </div>
                    </div>

                    <button type="button" class="bg-red-600 text-white rounded-md text-lg py-2 w-52 mt-10"
                        wire:click="returnToHome">Return to Home</button>
                </div>
            @endif
        @endif
    </main>

    <footer>
        <div class="bg-registrationPrimaryColor w-full py-5 text-center text-white">
            <p>Copyright © 2023 GPCA Registration</p>
        </div>
    </footer>

</div>
