<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\EventRegistrationType as EventRegistrationTypes;
use App\Models\Event as Events;
use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\PrintedBadge as PrintedBadges;
use App\Models\ScannedDelegate as ScannedDelegates;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;


class DelegateDetails extends Component
{
    public $finalDelegate, $members, $event;

    public $badgeViewFFText, $badgeViewFBText, $badgeViewFFBGColor, $badgeViewFBBGColor, $badgeViewFFTextColor, $badgeViewFBTextColor;

    public $printDelegateType, $printDelegateId;

    public $printedBadges, $scannedBadges;

    public $scanDelegateUrl, $printBadgeDelegateUrl;

    public $seatNumber, $showEditSeatNumber;

    protected $listeners = ['printBadgeConfirmed' => 'printBadge'];

    public function mount($eventCategory, $eventId, $finalDelegate)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->finalDelegate = $finalDelegate;

        $this->printedBadges = PrintedBadges::where('event_id', $eventId)->where('delegate_id', $finalDelegate['delegateId'])->where('delegate_type', $finalDelegate['delegateType'])->get();


        $this->scannedBadges = ScannedDelegates::where('event_id', $eventId)->where('delegate_id', $finalDelegate['delegateId'])->where('delegate_type', $finalDelegate['delegateType'])->get();

        $registrationType = EventRegistrationTypes::where('event_id', $eventId)->where('registration_type', $finalDelegate['badge_type'])->first();

        $this->badgeViewFFText = $registrationType->badge_footer_front_name;
        $this->badgeViewFBText = $registrationType->badge_footer_back_name;

        $this->badgeViewFFBGColor = $registrationType->badge_footer_front_bg_color;
        $this->badgeViewFBBGColor = $registrationType->badge_footer_back_bg_color;

        $this->badgeViewFFTextColor = $registrationType->badge_footer_front_text_color;
        $this->badgeViewFBTextColor = $registrationType->badge_footer_back_text_color;

        $combinedStringScan =  "gpca@scan" . ',' . $eventId . ',' . $eventCategory . ',' . $finalDelegate['delegateId'] . ',' . $finalDelegate['delegateType'];
        $finalCryptStringScan = base64_encode($combinedStringScan);
        $this->scanDelegateUrl = 'gpca'.$finalCryptStringScan;


        $combinedStringPrint = "gpca@reg" . ',' . $eventId . ',' . $eventCategory . ',' . $finalDelegate['delegateId'] . ',' . $finalDelegate['delegateType'];
        $finalCryptStringPrint = base64_encode($combinedStringPrint);
        $this->printBadgeDelegateUrl = 'ca' . $finalCryptStringPrint . 'gp';

        
        $this->showEditSeatNumber = false;
    }


    public function render()
    {
        return view('livewire.admin.events.delegates.delegate-detailsv2');
    }

    public function printBadgeClicked($delegateType, $delegateId)
    {
        $this->printDelegateType = $delegateType;
        $this->printDelegateId = $delegateId;

        $this->dispatchBrowserEvent('swal:print-badge-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function printBadge()
    {
        PrintedBadges::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'delegate_id' => $this->printDelegateId,
            'delegate_type' => $this->printDelegateType,
            'printed_date_time' => Carbon::now(),
        ]);

        $this->dispatchBrowserEvent('swal:print-badge-confirmed', [
            'url' => route('admin.event.delegates.detail.printBadge', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'delegateId' => $this->printDelegateId, 'delegateType' => $this->printDelegateType]),

            'type' => 'success',
            'message' => 'Badge Printed Successfully!',
            'text' => ''
        ]);

        $this->printDelegateType = null;
        $this->printDelegateId = null;
    }


    public function openEditSeatNumber()
    {
        $this->seatNumber = $this->finalDelegate['seat_number'];
        $this->showEditSeatNumber = true;
    }

    public function closeEditSeatNumber()
    {
        $this->showEditSeatNumber = false;
        $this->seatNumber = null;
    }

    public function updateSeatNumber()
    {
        if($this->seatNumber == null || trim($this->seatNumber) == ""){
            $this->seatNumber = null;
        }

        if ($this->finalDelegate['delegateType'] == "main") {
            MainDelegates::find($this->finalDelegate['delegateId'])->fill([
                'seat_number' => $this->seatNumber,
            ])->save();
        } else {
            AdditionalDelegates::find($this->finalDelegate['delegateId'])->fill([
                'seat_number' => $this->seatNumber,
            ])->save();
        }

        $this->finalDelegate['seat_number'] = $this->seatNumber;

        $this->showEditSeatNumber = false;
        $this->seatNumber = null;
    }
}
