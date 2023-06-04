<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Event as Events;
use Livewire\Component;

class AllDelegatesList extends Component
{
    public $finalListsOfDelegatesTemp = array();
    public $finalListsOfDelegates = array();
    public $searchTerm;

    public function mount()
    {
        $mainDelegates = MainDelegates::where('payment_status', '!=', 'unpaid')->get();

        if (!$mainDelegates->isEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {
                $event = Events::where('id', $mainDelegate->event_id)->first();

                array_push($this->finalListsOfDelegatesTemp, [
                    'eventCategory' => $event->category,
                    'eventId' => $event->id,
                    'delegateId' => $mainDelegate->id,
                    'delegateType' => "main",
                    'delegateEventCategory' => $event->category,
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
                            'eventCategory' => $event->category,
                            'eventId' => $event->id,
                            'delegateId' => $subDelegate->id,
                            'delegateType' => "sub",
                            'delegateEventCategory' => $event->category,
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
                    return str_contains(strtolower($item['delegateEventCategory']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateCompany']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateJobTitle']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateEmailAddress']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['delegateBadgeType']), strtolower($this->searchTerm));
                })
                ->all();
        }
        return view('livewire.admin.delegates.all-delegates-list');
    }
}
