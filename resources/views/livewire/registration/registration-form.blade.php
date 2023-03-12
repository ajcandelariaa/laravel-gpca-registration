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
        {{-- </form> --}}
    </div>
</div>
