<div class="container mx-auto my-10">

    <div class="float-left mt-20">
        @include('livewire.registration.progress_bar')
    </div>

    <div style="margin-left: 360px;">
        {{-- <form>
            @csrf --}}
        @if ($currentStep == 1)
            @include('livewire.registration.step.first')
        @elseif ($currentStep == 2)
            @include('livewire.registration.step.second')
        @elseif ($currentStep == 3)
            @include('livewire.registration.step.third')
        @else
            @include('livewire.registration.step.fourth')
        @endif

        @if ($currentStep == 1)
            <div class="text-center mt-20">
                <button
                    class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2" wire:click="increaseStep()">NEXT</button>
            </div>
        @elseif ($currentStep == 2)
            <div class="w-full mt-20 flex justify-center gap-5">
                <button
                    class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2" wire:click="decreaseStep()">PREVIOUS</button>
                <button
                    class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2" wire:click="increaseStep()">NEXT</button>
            </div>
        @elseif ($currentStep == 3)
            <div class="w-full mt-20 flex justify-center gap-5">
                <button
                    class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2" wire:click="decreaseStep()">PREVIOUS</button>
                <button
                    class="hover:bg-registrationPrimaryColor hover:text-white font-bold border-registrationPrimaryColor border-2 bg-white text-registrationPrimaryColor w-52 rounded-md py-2" wire:click="increaseStep()">SUBMIT</button>
            </div>
        @else
            
        @endif
        {{-- </form> --}}
    </div>
</div>
