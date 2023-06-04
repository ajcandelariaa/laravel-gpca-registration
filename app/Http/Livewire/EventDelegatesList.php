<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Event as Events;
use Livewire\Component;

class EventDelegatesList extends Component
{
    public $finalListsOfDelegatesTemp = array();
    public $finalListsOfDelegates = array();
    public $eventId;
    public $eventCategory;
    public $searchTerm;
    public $eventBanner;

    public function mount($eventId, $eventCategory)
    {
        $this->eventId = $eventId;
        $this->eventCategory = $eventCategory;
        
        $this->eventBanner = Events::where('id', $eventId)->where('category', $eventCategory)->value('banner');

        $mainDelegates = MainDelegates::where('event_id', $this->eventId)->where('payment_status', '!=', 'unpaid')->get();
        if (!$mainDelegates->isEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {

                array_push($this->finalListsOfDelegatesTemp, [
                    'eventCategory' => $this->eventCategory,
                    'eventId' => $this->eventId,
                    'delegateId' => $mainDelegate->id,
                    'delegateType' => "main",
                    'delegateCompany' => $mainDelegate->company_name,
                    'delegateJobTitle' => $mainDelegate->job_title,
                    'delegateName' => $mainDelegate->salutation . " " . $mainDelegate->first_name . " " . $mainDelegate->middle_name . " " . $mainDelegate->last_name,
                    'delegateEmailAddress' => $mainDelegate->email_address,
                    'delegateBadgeType' => $mainDelegate->badge_type,
                ]);


                $subDelegates = AdditionalDelegates::where('main_delegate_id', $mainDelegate->id)->get();

                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {
                        array_push($this->finalListsOfDelegatesTemp, [
                            'delegateId' => $subDelegate->id,
                            'delegateType' => "sub",
                            'delegateCompany' => $mainDelegate->company_name,
                            'delegateJobTitle' => $subDelegate->job_title,
                            'delegateName' => $subDelegate->salutation . " " . $subDelegate->first_name . " " . $subDelegate->middle_name . " " . $subDelegate->last_name,
                            'delegateEmailAddress' => $subDelegate->email_address,
                            'delegateBadgeType' => $subDelegate->badge_type,
                        ]);
                    }
                }
            }
        }
    }

    public function render()
    {
        if (empty($this->searchTerm)) {
            $this->finalListsOfDelegates = $this->finalListsOfDelegatesTemp;
        } else {
            $this->finalListsOfDelegates = collect($this->finalListsOfDelegatesTemp)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['delegateCompany']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateJobTitle']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateName']), strtolower($this->searchTerm))||
                        str_contains(strtolower($item['delegateEmailAddress']), strtolower($this->searchTerm))||
                        str_contains(strtolower($item['delegateBadgeType']), strtolower($this->searchTerm));
                })
                ->all();
        }
        return view('livewire.admin.events.delegates.event-delegates-list');
    }
}
