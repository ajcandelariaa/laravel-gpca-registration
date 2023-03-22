<div class="shadow-lg bg-white rounded-md w-72">
    <form>
        @csrf
        <div class="p-5">
            <div class="text-registrationPrimaryColor italic text-center font-bold text-2xl mt-4">
                Add promo code
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Code: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="promo_code"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('promo_code')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Registration Type: <span class="text-red-500">*</span>
                </div>
                <div>
                    <select wire:model="badge_type"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                        <option value=""></option>
                        @foreach ($badgeTypes as $badgeType)
                            <option value="{{ $badgeType }}" @if (old('badge_type') == $badgeType) selected @endif>
                                {{ $badgeType }}</option>
                        @endforeach
                    </select>

                    @error('badge_type')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Discount: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="number" wire:model="discount" step="1" min="0" placeholder="0%" max="100"
                        value="{{ old('discount') }}"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                    @error('discount')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Number of Codes: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="number" wire:model="number_of_codes" step="1" min="1" placeholder="1" max="10000"
                        value="{{ old('number_of_codes') }}"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                    @error('number_of_codes')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Code Validity: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="datetime-local" wire:model="validity"
                        value="{{ old('validity') }}"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">

                    @error('validity')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-5">
                <div class="text-registrationPrimaryColor">
                    Description: 
                </div>
                <div>
                    <input type="text" wire:model="description"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('description')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="text-center mt-10">
                <button wire:click.prevent="addPromoCode()"
                    class="bg-registrationPrimaryColor hover:bg-registrationPrimaryColorHover text-white font-medium py-2 px-5 rounded inline-flex items-center text-sm">
                    <span class="mr-2"><i class="fas fa-plus"></i></span>
                    <span>Add Promo Code</span>
                </button>
            </div>
        </div>

    </form>
</div>
