<div class="container mx-auto my-10">
    @include('livewire.registration.progress_bar')

    @if ($currentStep == 1)
        @include('livewire.registration.step.first')
    @elseif ($currentStep == 2)
        @include('livewire.registration.step.second')
    @elseif ($currentStep == 3)
        @include('livewire.registration.step.third')
    @else
        @include('livewire.registration.step.fourth')
    @endif
</div>
