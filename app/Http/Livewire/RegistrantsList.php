<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use Livewire\Component;

class RegistrantsList extends Component
{
    public $finalListOfRegistrants;
    public $eventId;
    public $eventCategory;
    public $searchTerm;

    public function mount($eventId, $eventCategory)
    {
        $this->eventId = $eventId;
        $this->eventCategory = $eventCategory;
    }

    public function render()
    {
        if (empty($this->searchTerm)) {
            $this->finalListOfRegistrants = MainDelegates::where('event_id', $this->eventId)->get();
        } else {
            if($this->searchTerm == "member"){
                $this->finalListOfRegistrants = MainDelegates::where('event_id', $this->eventId)
                ->where('pass_type', 'member')
                ->get();
            } else {
                $this->finalListOfRegistrants = MainDelegates::where('event_id', $this->eventId)
                ->where('company_name', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('company_country', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('company_city', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('pass_type', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('payment_status', 'like', '%' . $this->searchTerm . '%')
                ->get();
            }
        }
        return view('livewire.registrants.registrants-list');
    }
}
