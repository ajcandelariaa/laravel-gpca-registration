<?php

namespace App\Http\Livewire;

use App\Models\AdditionalDelegate;
use App\Models\DelegateDetailsUpdateLog;
use App\Models\Event as Events;
use App\Models\MainDelegate;
use Carbon\Carbon;
use Livewire\Component;

class DelegatesUpdateLogs extends Component
{
    public $event;

    public $delegateLogs = array();

    public function mount($eventId, $eventCategory){
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();

        $delegateDetailsUpdateLogs = DelegateDetailsUpdateLog::where('event_id', $eventId)->where('event_category', $eventCategory)->orderBy('updated_date_time', 'ASC')->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($eventCategory == $eventCategoryC) {
                $eventCode = $code;
            }
        }

        if($delegateDetailsUpdateLogs->isNotEmpty()){
            foreach($delegateDetailsUpdateLogs as $delegateDetailsUpdateLog){
                
                if($delegateDetailsUpdateLog->delegate_type == "main"){
                    $delegate = MainDelegate::where('id', $delegateDetailsUpdateLog->delegate_id)->first();
                } else {
                    $delegate = AdditionalDelegate::where('id', $delegateDetailsUpdateLog->delegate_id)->first();
                }

                $transactionId = $delegate->transaction->id;
                $lastDigit = 1000 + intval($transactionId);
                $finalTransactionId = $this->event->year . $eventCode . $lastDigit;
                
                array_push($delegateLogs, [
                    'transactionId' => $finalTransactionId,
                    'delegateId' => $delegateDetailsUpdateLog->delegate_id,
                    'delegateType' => $delegateDetailsUpdateLog->delegate_type,
                    'pcName' => $delegateDetailsUpdateLog->updated_by_name,
                    'pcNumber' => $delegateDetailsUpdateLog->updated_by_pc_number,
                    'description' => $delegateDetailsUpdateLog->description,
                    'updateDateTime' => $delegateDetailsUpdateLog->updated_date_time,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.events.delegates.delegates-update-logs');
    }
}
