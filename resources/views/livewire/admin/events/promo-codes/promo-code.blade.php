<div>
    <div>
        <img src="{{ Storage::url($event->banner) }}" alt="" class="w-full object-cover">
    </div>
    <div class="px-5">
        <div class="float-left">
            @include('livewire.admin.events.promo-codes.add')
        </div>


        @if ($promoCodes->isNotEmpty())
            <div class="shadow-lg my-5 pt-5 bg-white rounded-md" style="margin-left: 320px; ">
                <h1 class="text-center text-2xl bg-registrationPrimaryColor text-white py-4">Promo codes</h1>
                <div class="grid grid-cols-11 gap-5 p-4 px-4 text-center items-center bg-blue-600 text-white ">
                    <div class="col-span-1 break-words">Code</div>
                    <div class="col-span-1 break-words">Registration Type</div>
                    <div class="col-span-1 break-words">Discount</div>
                    <div class="col-span-2 break-words">Description</div>
                    <div class="col-span-1 break-words">Remaining Usage</div>
                    <div class="col-span-1 break-words">Total Usage</div>
                    <div class="col-span-1 break-words">Number of Codes</div>
                    <div class="col-span-1 break-words">Validity</div>
                    <div class="col-span-1 break-words">Status</div>
                    <div class="col-span-1 break-words">Action</div>
                </div>

                @php
                    $count = 1;
                @endphp

                @foreach ($promoCodes as $promoCode)
                    <div
                        class="grid grid-cols-11 gap-5 py-2 px-4 mb-1 text-center items-center {{ $count % 2 == 0 ? 'bg-registrationInputFieldsBGColor' : 'bg-registrationCardBGColor' }}">
                        <div class="col-span-1 break-words">{{ $promoCode->promo_code }}</div>
                        <div class="col-span-1 break-words">{{ $promoCode->badge_type }}</div>
                        <div class="col-span-1 break-words">
                            @if ($promoCode->discount_type == 'percentage')
                                {{ $promoCode->discount }}%
                            @else
                                $ {{ number_format($promoCode->discount, 2, '.', ',') }}
                            @endif
                        </div>
                        <div class="col-span-2 break-words">{{ $promoCode->description }}</div>
                        <div class="col-span-1 break-words">{{ $promoCode->number_of_codes - $promoCode->total_usage }}
                        </div>
                        <div class="col-span-1 break-words">{{ $promoCode->total_usage }}</div>
                        <div class="col-span-1 break-words">{{ $promoCode->number_of_codes }}</div>
                        <div class="col-span-1 break-words">{{ $promoCode->validity }}</div>
                        <div class="col-span-1 break-words ">
                            @if ($promoCode->active)
                                <button wire:click="updateStatus({{ $promoCode->id }}, {{ $promoCode->active }})"
                                    class="text-gray-700 bg-green-300 hover:bg-green-500 hover:text-white py-1 px-2 text-sm rounded-md">Active</button>
                            @else
                                <button wire:click="updateStatus({{ $promoCode->id }}, {{ $promoCode->active }})"
                                    class="text-gray-700 bg-red-300 hover:bg-red-500 hover:text-white py-1 px-2 text-sm rounded-md">Inactive</button>
                            @endif
                        </div>
                        <div class="col-span-1 break-words flex gap-4 justify-center">
                            <div wire:click="showEditPromoCode({{ $promoCode->id }})"
                                class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit
                            </div>
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

        @if ($updatePromoCode)
            @include('livewire.admin.events.promo-codes.edit')
        @endif
    </div>
</div>
