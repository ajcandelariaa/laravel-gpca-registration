<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RegistrationForm extends Component
{
    public $currentStep = 1;

    public function render()
    {
        return view('livewire.registration.registration-form');
    }

    public function increaseStep(){
        $this->currentStep += 1;
    }

    public function decreaseStep(){
        $this->currentStep -= 1;
    }
}
