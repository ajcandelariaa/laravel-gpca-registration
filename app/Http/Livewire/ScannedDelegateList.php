<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use Livewire\Component;

class ScannedDelegateList extends Component
{
    public $event;

    public function mount($eventCategory, $eventId)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
    }

    public function render()
    {
        return view('livewire.admin.events.scanned-delegate.scanned-delegate-list');
    }
}
