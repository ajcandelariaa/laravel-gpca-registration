<div>
    <div class="flex flex-row items-start justify-between gap-5 w-full">
        <button type="button" class="bg-red-500 hover:bg-red-600 text-white rounded-md text-lg py-2 w-52"
            wire:click.prevent="returnToHome">Return to Home</button>

        <div class="flex flex-row gap-2 items-start">
            @if ($state == 'qrCodeScanned')
                <div class="flex flex-row gap-5 text-registrationPrimaryColor text-2xl">
                    <p>Transaction ID:</p>
                    <p class="font-semibold">{{ $searchTerm }}</p>
                </div>
            @else
                <div class="relative">
                    <input type="text" wire:model.debounce.300ms="searchTerm" @keydown.escape="suggestions = array()"
                        class="bg-registrationInputFieldsBGColor py-3 px-3 outline-registrationPrimaryColor w-96 text-center"
                        placeholder="Transaction ID or Full Name">

                    <ul class="bg-registrationPrimaryColorHover w-full absolute top-10 z-50">
                        @foreach ($suggestions as $suggestion)
                            <li wire:click="selectSuggestion('{{ $suggestion['transactionId'] }}')"
                                class="cursor-pointer text-center text-white hover:bg-registrationPrimaryColor">
                                {{ $suggestion['transactionId'] }} - {{ $suggestion['fullName'] }}</li>
                            <hr>
                        @endforeach
                    </ul>


                    @if ($delegateDetail == null && $searchTerm != null && count($suggestions) == 0)
                        <div class="w-full absolute top-10 z-50">
                            <p class="bg-red-500 cursor-pointer text-center text-white">
                                No user found
                            </p>
                        </div>
                    @endif
                </div>
                <button class="bg-registrationPrimaryColorHover hover:bg-registrationPrimaryColor text-lg text-white py-2 px-5 rounded-md"
                    wire:click.prevent="searchClicked">Search</button>
            @endif
        </div>
    </div>

    <div class="mt-10">
        <p class="text-registrationPrimaryColor text-2xl font-semibold text-center">Badge Preview</p>
        <div class="col-span-1 grid grid-cols-2 gap-5">
            <div>
                <div class="mt-10 flex flex-col justify-between">
                    <div class="relative">
                        <img src="https://www.gpcaforum.com/wp-content/uploads/2023/11/front.png"
                            class="border border-black" style="width: 400px;">
                        <div style="position: absolute; top:24%; height: 150px; width: 100%; padding: 0px 20px;">
                            <div class="flex flex-col justify-center" style="height: 150px;">
                                <div class="font-semibold text-registrationPrimaryColor">
                                    @if ($delegateDetail != null)
                                        <p>{{ $delegateDetail['fullName'] }}</p>
                                        <p>{{ $delegateDetail['jobTitle'] }}</p>
                                        <p>{{ $delegateDetail['companyName'] }}</p>
                                    @else
                                        <p>Name</p>
                                        <p>Job Title</p>
                                        <p>Company Name</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($delegateDetail != null)
                            @if ($delegateDetail['seatNumber'] != null || $delegateDetail['seatNumber'] != '')
                                <div style="position: absolute; left: 23%; top: 60%;">
                                    <div class="font-semibold text-registrationPrimaryColor">
                                        <p>{{ $delegateDetail['seatNumber'] }}</p>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div style="position: absolute; bottom: 10%; left: 5%;">
                            <div>
                                @if ($delegateDetail != null)
                                    <p class="text-sm text-center py-2 px-4 font-bold uppercase text-white bg-black">
                                        {{ $delegateDetail['frontText'] }}</p>
                                @else
                                    <p class="text-sm text-center py-2 px-4 font-bold uppercase text-white bg-black">
                                        Badge type</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-center mt-3">Front</p>
            </div>

            <div>
                <div class="mt-10 flex flex-col justify-between">
                    <img src="https://www.gpcaforum.com/wp-content/uploads/2023/11/back.png" class="border border-black"
                        style="width: 400px;">
                </div>

                <p class="text-center mt-3">Back</p>
            </div>
        </div>
    </div>


    @if ($delegateDetail != null)
        <div class="flex justify-center mt-10">
            <button type="button" class="bg-green-500 hover:bg-green-600 text-white rounded-md text-lg py-2 w-52"
                wire:click.prevent="printClicked">Print</button>
        </div>
    @endif
</div>
