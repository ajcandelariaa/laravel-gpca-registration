<div class="shadow-lg bg-white rounded-md w-72">
    <form>
        @csrf
        <div class="p-5">
            <div class="text-registrationPrimaryColor italic text-center font-bold text-2xl mt-4">
                Edit Registration Type
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Registration type: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="registrationType"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('registrationType')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Badge footer front name: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="badgeFooterFrontName"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('badgeFooterFrontName')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Badge footer front BG color: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="badgeFooterFrontBGColor"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('badgeFooterFrontBGColor')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Badge footer front text color: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="badgeFooterFrontTextColor"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('badgeFooterFrontTextColor')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Badge footer back name: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="badgeFooterBackName"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('badgeFooterBackName')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Badge footer back BG color: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="badgeFooterBackBGColor"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('badgeFooterBackBGColor')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="space-y-2 mt-10">
                <div class="text-registrationPrimaryColor">
                    Badge footer back text color: <span class="text-red-500">*</span>
                </div>
                <div>
                    <input type="text" wire:model="badgeFooterBackTextColor"
                        class="bg-registrationInputFieldsBGColor w-full py-1 px-3 outline-registrationPrimaryColor">
                    @error('badgeFooterBackTextColor')
                        <span class="mt-2 text-red-600 italic text-sm">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="text-center mt-10 flex gap-4">
                <button wire:click.prevent="cancelEditRegistrationType"
                    class="bg-red-500 rounded-md text-white py-1 w-full hover:cursor-pointer hover:bg-red-700">Cancel</button>
                <button wire:click.prevent="updateRegistrationTypeConfirmation"
                    class="bg-registrationPrimaryColor rounded-md text-white py-1 w-full hover:cursor-pointer hover:bg-registrationPrimaryColorHover">Update</button>
            </div>
        </div>

    </form>
</div>
