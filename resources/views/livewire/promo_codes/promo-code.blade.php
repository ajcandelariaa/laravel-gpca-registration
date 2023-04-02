<div class="px-5">
    <div class="float-left">
        @if ($updatePromoCode)
            @include('livewire.promo_codes.edit')
        @else
            @include('livewire.promo_codes.add')
        @endif
    </div>


    @if ($promoCodes->isNotEmpty())
        <div class="shadow-lg my-5 pt-5 bg-white rounded-md" style="margin-left: 320px; ">
            <h1 class="text-center text-2xl">Promo codes</h1>
            <div class="grid grid-cols-9 pt-4 pb-2 place-items-center">
                <div class="col-span-1">Code</div>
                <div class="col-span-1">Registration Type</div>
                <div class="col-span-1">Discount</div>
                <div class="col-span-1">Remaining Usage</div>
                <div class="col-span-1">Total Usage</div>
                <div class="col-span-1">Number of Codes</div>
                <div class="col-span-1">Validity</div>
                <div class="col-span-1">Status</div>
                <div class="col-span-1">Action</div>
            </div>

            @php
                $count = 1;
            @endphp

            @foreach ($promoCodes as $promoCode)
                <div
                    class="grid grid-cols-9 pt-2 pb-2 mb-1 place-items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                    <div class="col-span-1">{{ $promoCode->promo_code }}</div>
                    <div class="col-span-1">{{ $promoCode->badge_type }}</div>
                    <div class="col-span-1">{{ $promoCode->discount }}%</div>
                    <div class="col-span-1">{{ $promoCode->number_of_codes - $promoCode->total_usage }}</div>
                    <div class="col-span-1">{{ $promoCode->total_usage }}</div>
                    <div class="col-span-1">{{ $promoCode->number_of_codes }}</div>
                    <div class="col-span-1">{{ $promoCode->validity }}</div>
                    <div class="col-span-1">
                        @if ($promoCode->active)
                            <button wire:click="updateStatus({{ $promoCode->id }}, {{ $promoCode->active }})"
                                class="text-gray-700 bg-green-300 hover:bg-green-500 hover:text-white py-1 px-2 text-sm rounded-md">Active</button>
                        @else
                            <button wire:click="updateStatus({{ $promoCode->id }}, {{ $promoCode->active }})"
                                class="text-gray-700 bg-red-300 hover:bg-red-500 hover:text-white py-1 px-2 text-sm rounded-md">Inactive</button>
                        @endif
                    </div>
                    <div class="col-span-1 flex gap-4">
                        <div wire:click="showEditPromoCode({{ $promoCode->id }})"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Edit
                        </div>
                        {{-- <div onclick="deletepromoCodeScript({{ $promoCode->id }})"
                            class="cursor-pointer hover:text-red-600 text-red-500">
                            <i class="fa-solid fa-trash"></i>
                            Delete
                        </div> --}}
                    </div>
                    @php
                        $count++;
                    @endphp
                </div>
            @endforeach
        </div>
    @endif

    @if ($promoCodes->isEmpty())
        <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md" style="margin-left: 320px; ">
            There are no create codes yet.
        </div>
    @endif

    {{-- <script>
        function deletepromoCodeScript(promoCodeId) {
            if (confirm("Are you sure you want to delete this code?"))
                window.livewire.emit('deletePromoCodeScript', promoCodeId);
        }
    </script> --}}
</div>
