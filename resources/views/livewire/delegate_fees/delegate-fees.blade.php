<div>
    <img src="{{ Storage::url($eventBanner) }}" alt="" class="w-full object-cover">
</div>
<div class="container mx-auto my-10">

    @if ($isDelegateFeeEdit)
        @include('livewire.delegate_fees.edit_delegate_fee')
    @else
        @include('livewire.delegate_fees.add_delegate_fee')
    @endif

    <div class="mt-5">
        @if ($delegateFees->isNotEmpty())
            <p class="font-medium text-xl upper text-white bg-registrationPrimaryColor py-2 px-3">Event Delegate fees
            </p>
            @foreach ($delegateFees as $index => $delegateFee)
                <div class="grid grid-cols-delegateFeesGrid items-end gap-2 mt-1 bg-gray-100 py-2">
                    <div class="px-2">
                        {{ $index + 1 }}. {{ $delegateFee->description }}
                    </div>
                    <div class="col-span-1 flex gap-4">
                        <div wire:click="showEditDelegateFee({{ $delegateFee->id }})"
                            class="cursor-pointer hover:text-yellow-600 text-yellow-500">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Edit
                        </div>
                        <div wire:click="deleteDelegateFee({{ $delegateFee->id }})" class="cursor-pointer hover:text-red-600 text-red-500">
                            <i class="fa-solid fa-trash"></i>
                            Delete
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        @if ($delegateFees->isEmpty())
            <div class="bg-red-400 text-white text-center py-3 mt-5 rounded-md">
                There are no delegate fees yet.
            </div>
        @endif
    </div>

</div>
