<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use Livewire\Component;
use App\Models\EventDelegateFee as EventDelegateFees;

class DelegateFees extends Component
{
    public $delegateFee;
    public $delegateFees;
    public $eventCategory, $eventId;

    public $delegateFeeEdit;
    public $delegateFeeId;
    public $isDelegateFeeEdit = false;
    public $eventBanner;

    public function mount($eventCategory, $eventId)
    {
        $this->eventBanner = Events::where('id', $eventId)->where('category', $eventCategory)->value('banner');
        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
    }

    public function render()
    {
        $this->delegateFees = EventDelegateFees::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();
        return view('livewire.delegate_fees.delegate-fees');
    }

    public function addDelegateFee()
    {
        $this->validate(
            [
                'delegateFee' => 'required',
            ],
            [
                'delegateFee.required' => "Delegate fee is required",
            ]
        );

        EventDelegateFees::create([
            'event_id' => $this->eventId,
            'event_category' => $this->eventCategory,
            'description' => $this->delegateFee,
        ]);

        $this->delegateFee = null;
    }

    public function showEditDelegateFee($delegateFeeId){
        $delegateFee = EventDelegateFees::findOrFail($delegateFeeId);
        $this->delegateFeeEdit = $delegateFee->description;
        $this->delegateFeeId = $delegateFeeId;
        $this->isDelegateFeeEdit = true;
    }
    

    public function updateDelegateFee()
    {
        $this->validate(
            [
                'delegateFeeEdit' => 'required',
            ],
            [
                'delegateFeeEdit.required' => "Delegate fee is required",
            ]
        );

        EventDelegateFees::find($this->delegateFeeId)->fill(
            [
                'description' => $this->delegateFeeEdit,
            ],
        )->save();

        $this->delegateFeeEdit = null;
        $this->delegateFeeId = null;
        $this->isDelegateFeeEdit = false;
    }

    
    public function deleteDelegateFee($delegateFeeId)
    {
        EventDelegateFees::find($delegateFeeId)->delete();
    }
}
