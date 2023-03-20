<div class="container mx-auto my-10">

    <div class="float-left mt-20">
        @include('livewire.registration.progress_bar')
    </div>

    <div style="margin-left: 360px;">
        <form wire:submit.prevent="submit">
            @if ($currentStep == 1)
                @include('livewire.registration.step.first')
            @elseif ($currentStep == 2)
                @include('livewire.registration.step.second')
            @elseif ($currentStep == 3)
                @include('livewire.registration.step.third')
            @else
                @include('livewire.registration.step.fourth')
            @endif

            <div class="w-full mt-20 flex justify-between gap-5">
                @if ($currentStep == 1)
                    <div></div>
                @endif
                @if ($currentStep == 2 || $currentStep == 3)
                    <button type="button"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        wire:click.prevent="decreaseStep">PREVIOUS</button>
                @endif
                @if ($currentStep == 1 || $currentStep == 2)
                    <button type="button"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        wire:click.prevent="increaseStep">NEXT</button>
                @endif
                @if ($currentStep == 3)
                    <button type="submit"
                        class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2"
                        >SUBMIT</button>
                @endif
            </div>

        </form>
    </div>
</div>
