<div class="mx-5">
    <div>
        <div class="text-registrationPrimaryColor font-bold text-2xl">
            Package summary
        </div>

        <div class="italic mt-5">
            By registering your details, you understand that your personal data will be handled according to <a
                href="" class="text-registrationPrimaryColor underline ">GPCA Privacy Policy</a>
        </div>
    </div>

    <div class="mt-5">
        <div class="grid grid-cols-addDelegateGrid gap-y-2">
            <div class="text-registrationPrimaryColor col-span-2">
                Invoice to be sent to: <span
                    class="text-black">{{ $salutation . ' ' . $firstName . ' ' . $middleName . ' ' . $lastName }}</span>
            </div>

            <div class="text-registrationPrimaryColor col-span-2">
                Email address: <span class="text-black">{{ $emailAddress }}</span>
            </div>

            @if ($event->category != 'GLF')
                <div class="text-registrationPrimaryColor col-span-2">
                    Payment method:
                </div>
            @endif
        </div>

        <div class="mt-5 flex gap-5 items-start">
            @if ($finalTotal == 0)
                @if ($event->category != 'GLF')
                    <button wire:click.prevent="btClicked" type="button"
                        class="{{ $paymentMethod == 'bankTransfer' ? 'bg-registrationSecondaryColor text-white' : 'hover:bg-registrationSecondaryColor hover:text-white border-registrationSecondaryColor border-2 bg-white text-registrationSecondaryColor' }} font-bold w-52 rounded-md py-5 ">
                        <i class="fa-solid fa-building-columns mr-2"></i> Bank transfer</button>
                @endif
            @else
                <button wire:click.prevent="btClicked" type="button"
                    class="{{ $paymentMethod == 'bankTransfer' ? 'bg-registrationSecondaryColor text-white' : 'hover:bg-registrationSecondaryColor hover:text-white border-registrationSecondaryColor border-2 bg-white text-registrationSecondaryColor' }} font-bold w-52 rounded-md py-5 ">
                    <i class="fa-solid fa-building-columns mr-2"></i> Bank transfer</button>

                <div class="flex flex-col">
                    <button wire:click.prevent="ccClicked" type="button"
                        class="{{ $paymentMethod == 'creditCard' ? 'bg-registrationSecondaryColor text-white' : 'hover:bg-registrationSecondaryColor hover:text-white border-registrationSecondaryColor border-2 bg-white text-registrationSecondaryColor' }} font-bold w-52 rounded-md py-5 ">
                        <i class="fa-solid fa-credit-card mr-2"></i> Credit card</button>

                    <span class="text-registrationPrimaryColor italic text-sm text-center mt-1">for Visa and MasterCard
                        only</span>
                </div>
            @endif
        </div>

        @if ($paymentMethodError != null)
            <div class="text-red-500 text-xs italic mt-2">
                {{ $paymentMethodError }}
            </div>
        @endif
    </div>

    {{-- @if ($event->category == 'AF' && $event->year == '2023')
        <p class="mt-5">If you are interested with the GPCA spouse program, please click here to <a
                href="https://www.gpcaforum.com/spouse-program/" target="_blank"
                class="text-blue-600 hover:underline font-semibold">learn more</a> and <a
                href="https://www.gpcaregistration.com/register/2023/AFS/11" target="_blank"
                class="text-blue-600 hover:underline font-semibold">register.</a></p>
    @endif --}}

    <div class="mt-10 flex justify-between gap-5">
        <button type="button" wire:key="btnDecreaseStep"
            class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
            wire:click.prevent="decreaseStep">PREVIOUS</button>

        @if ($event->category == 'GLF')
            <button type="button" wire:key="btnIncreaseStep"
                class="hover:bg-registrationPrimaryColorHover hover:text-white font-bold border-registrationPrimaryColor border-2 bg-registrationPrimaryColor text-white w-52 rounded-md py-2"
                wire:click.prevent="increaseStep" wire:loading.attr="disabled"
                wire:loading.class="cursor-not-allowed">SUBMIT</button>
        @else
            <button type="button" wire:key="btnIncreaseStep"
                class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                wire:click.prevent="increaseStep" wire:loading.attr="disabled"
                wire:loading.class="cursor-not-allowed">NEXT</button>
        @endif
    </div>

    <div class="mt-10">
        @include('livewire.registration.package_summary.package_rows')
        @include('livewire.registration.package_summary.package_cols')
    </div>
</div>
