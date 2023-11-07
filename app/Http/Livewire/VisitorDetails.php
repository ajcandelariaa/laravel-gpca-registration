<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\EventRegistrationType as EventRegistrationTypes;
use App\Models\Event as Events;
use App\Models\VisitorPrintedBadge as VisitorPrintedBadges;
use App\Models\ScannedVisitor as ScannedVisitors;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class VisitorDetails extends Component
{
    public $finalVisitor, $members, $event;

    public $badgeViewFFText, $badgeViewFBText, $badgeViewFFBGColor, $badgeViewFBBGColor, $badgeViewFFTextColor, $badgeViewFBTextColor;

    public $printVisitorType, $printVisitorId;

    public $printedBadges, $scannedBadges;

    public $scanVisitorUrl;

    protected $listeners = ['printBadgeConfirmed' => 'printBadge'];

    public function mount($eventCategory, $eventId, $finalVisitor)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->finalVisitor = $finalVisitor;

        $this->printedBadges = VisitorPrintedBadges::where('event_id', $eventId)->where('visitor_id', $finalVisitor['visitorId'])->where('visitor_type', $finalVisitor['visitorType'])->get();
        $this->scannedBadges = ScannedVisitors::where('event_id', $eventId)->where('visitor_id', $finalVisitor['visitorId'])->where('visitor_type', $finalVisitor['visitorType'])->get();

        $registrationType = EventRegistrationTypes::where('event_id', $eventId)->where('registration_type', $finalVisitor['badge_type'])->first();

        $this->badgeViewFFText = $registrationType->badge_footer_front_name;
        $this->badgeViewFBText = $registrationType->badge_footer_back_name;

        $this->badgeViewFFBGColor = $registrationType->badge_footer_front_bg_color;
        $this->badgeViewFBBGColor = $registrationType->badge_footer_back_bg_color;

        $this->badgeViewFFTextColor = $registrationType->badge_footer_front_text_color;
        $this->badgeViewFBTextColor = $registrationType->badge_footer_back_text_color;

        $combinedString = $eventId . ',' . $eventCategory . ',' . $finalVisitor['visitorId'] . ',' . $finalVisitor['visitorType'];
        $finalCryptString = Crypt::encryptString($combinedString);
        
        $this->scanVisitorUrl = route('scan.qr', ['id' => $finalCryptString]);
    }
    public function render()
    {
        return view('livewire.admin.events.visitors.visitor-details');
    }

    public function printBadgeClicked($visitorType, $visitorId)
    {
        $this->printVisitorType = $visitorType;
        $this->printVisitorId = $visitorId;

        $this->dispatchBrowserEvent('swal:print-badge-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function printBadge()
    {
        VisitorPrintedBadges::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'visitor_id' => $this->printVisitorId,
            'visitor_type' => $this->printVisitorType,
            'printed_date_time' => Carbon::now(),
        ]);

        $this->dispatchBrowserEvent('swal:print-badge-confirmed', [
            'url' => route('admin.event.delegates.detail.printBadge', ['eventCategory' => $this->event->category, 'eventId' => $this->event->id, 'delegateId' => $this->printVisitorId, 'delegateType' => $this->printVisitorType]),
            
            'type' => 'success',
            'message' => 'Badge Printed Successfully!',
            'text' => ''
        ]);

        $this->printVisitorType = null;
        $this->printVisitorId = null;
    }
}
