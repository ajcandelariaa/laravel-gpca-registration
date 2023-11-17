<div>
    <div class="flex flex-row items-start justify-between gap-5 w-full">
        <button type="button" class="bg-red-600 text-white rounded-md text-lg py-2 w-52"
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

                    <ul class="bg-registrationPrimaryColorHover w-full absolute top-10">
                        @foreach ($suggestions as $suggestion)
                            <li wire:click="selectSuggestion('{{ $suggestion['transactionId'] }}')"
                                class="cursor-pointer text-center text-white hover:bg-registrationPrimaryColor">
                                {{ $suggestion['transactionId'] }} - {{ $suggestion['fullName'] }}</li>
                            <hr>
                        @endforeach
                    </ul>
                </div>
                <button class="bg-registrationPrimaryColor text-lg text-white py-2 px-5 rounded-md"
                    wire:click.prevent="searchClicked">Search</button>
            @endif
        </div>
    </div>

    <div class="mt-10 mb-30">
        <p class="text-registrationPrimaryColor text-2xl font-semibold text-center">Badge Preview</p>
        <div class="col-span-1 grid grid-cols-2 gap-5">
            <div>
                <div class="border border-black mt-10 flex flex-col justify-between">
                    <div>
                        <img src="{{ Storage::url($eventBanner) }}">
                    </div>
                    <div class="my-32">
                        @if ($delegateDetail != null)
                            <p class="text-center text-lg">
                                {{ $delegateDetail['fullName'] }}
                            </p>
                            <p class="text-center">{{ $delegateDetail['jobTitle'] }}</p>
                            <p class="text-center font-bold">{{ $delegateDetail['companyName'] }}</p>
                        @else
                            <p class="text-center text-lg">Name</p>
                            <p class="text-center">Job Title</p>
                            <p class="text-center font-bold">Company Name</p>
                        @endif
                    </div>
                    <div>
                        @if ($delegateDetail != null)
                            <p class="text-center py-4 font-bold uppercase"
                                style="color: {{ $delegateDetail['frontTextColor'] }}; background-color: {{ $delegateDetail['frontTextBGColor'] }}">
                                {{ $delegateDetail['frontText'] }}</p>
                        @else
                            <p class="text-center py-4 font-bold uppercase text-white bg-black">Badge type</p>
                        @endif
                    </div>
                </div>

                <div class="text-center mt-3">
                    <p>Front</p>
                </div>
            </div>

            <div>
                <div class="border border-black mt-10 flex flex-col justify-between">
                    <div>
                        <img src="{{ Storage::url($eventBanner) }}">
                    </div>
                    <div class="my-32">
                        @if ($delegateDetail != null)
                            <p class="text-center text-lg">
                                {{ $delegateDetail['fullName'] }}
                            </p>
                            <p class="text-center">{{ $delegateDetail['jobTitle'] }}</p>
                            <p class="text-center font-bold">{{ $delegateDetail['companyName'] }}</p>
                        @else
                            <p class="text-center text-lg">Name</p>
                            <p class="text-center">Job Title</p>
                            <p class="text-center font-bold">Company Name</p>
                        @endif
                    </div>
                    <div>
                        @if ($delegateDetail != null)
                            <p class="text-center py-4 font-bold uppercase"
                                style="color: {{ $delegateDetail['frontTextColor'] }}; background-color: {{ $delegateDetail['frontTextBGColor'] }}">
                                {{ $delegateDetail['frontText'] }}</p>
                        @else
                            <p class="text-center py-4 font-bold uppercase text-white bg-black">Badge type</p>
                        @endif
                    </div>
                </div>

                <div class="text-center mt-3">
                    <p>Back</p>
                </div>
            </div>
        </div>
    </div>


    @if ($delegateDetail != null)
        <div class="flex justify-center mt-10">
            <button type="button" class="bg-green-600 text-white rounded-md text-lg py-2 w-52"
                wire:click.prevent="printClicked">Print</button>
        </div>
    @endif
</div>
