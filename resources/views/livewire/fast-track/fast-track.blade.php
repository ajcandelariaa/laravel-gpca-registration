<div class="min-h-screen flex flex-col">
    <header class="">
        <img src="{{ Storage::url($eventBanner) }}" class="w-full">
    </header>

    <main class="container mx-auto my-10 flex-1 flex items-center justify-center">
        @if ($state == null)
            <div>
                <p class="text-registrationPrimaryColor text-7xl font-semibold text-center">Welcome to FAST TRACK
                </p>
                <div class="flex flex-col justify-center items-center gap-10 mt-20">
                    <button type="button" class="bg-registrationPrimaryColor text-white rounded-xl text-4xl w-96 h-20"
                        wire:click.prevent="qrCodeScannerClicked">Scan QR Code</button>
                    <button type="button"
                        class="bg-registrationSecondaryColor text-white rounded-xl text-4xl w-96 h-20"
                        wire:click.prevent="transactionIdClicked">Enter your details</button>
                </div>
            </div>
        @else
            @if ($state == 'qrcode')
                @include('livewire.fast-track.qr_code_scanner')
            @else
                @include('livewire.fast-track.enter_transaction_id')
            @endif
        @endif
    </main>

    <footer>
        <div class="flex flex-col">
            <p>Date: {{ $date }} ({{ $day }})</p>
            <p>Time: <span id="current-time">{{ $time }}</span></p>
        </div>
        <div class="bg-registrationPrimaryColor w-full py-5 text-center text-white">
            <p>Copyright © 2023 GPCA Registration</p>
        </div>
    </footer>

</div>
