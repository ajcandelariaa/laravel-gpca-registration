<form>
    @csrf
    <div class="mt-10 grid grid-cols-addDelegateFeeGrid items-end gap-5">
        <div>
            <div class="text-registrationPrimaryColor">
                @if ($eventCategory == 'AFV')
                    Visitor fee:
                @else
                    Delegate fee:
                @endif
                <span class="text-red-500">*</span>
            </div>
            <div>
                <input type="text" wire:model="delegateFee"
                    class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
            </div>
        </div>
        <div>
            <button wire:click.prevent="addDelegateFee"
                class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded-md inline-flex items-center text-sm">
                <span class="mr-2"><i class="fas fa-plus"></i></span>
                <span>Add</span>
            </button>
        </div>
    </div>
    @error('delegateFee')
        <span class="mt-2 text-red-600 italic text-sm">
            {{ $message }}
        </span>
    @enderror
</form>
