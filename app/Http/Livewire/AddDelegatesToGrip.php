<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AddDelegatesToGrip extends Component
{
    public $event, $delegatesFromGrip, $confirmedDelegates;
    public $eventCode;

    public function mount($event)
    {
        $this->event = $event;
        $this->eventCode = config('app.eventCategories')[$event->category];
        $this->delegatesFromGrip = array();

        $url = env('API_GRIP_URL') . '/thing/register/detail';
        $response = Http::withToken(env('API_GRIP_AUTH_TOKEN'))->get($url)->json();
        if ($response['success']) {
            foreach ($response['data'] as $apiDelegate) {
                array_push($this->delegatesFromGrip, $apiDelegate);
            }
        }

        $this->confirmedDelegates = $this->getConfirmedDelegates($event->id, $event->category, $event->year);

        dd($this->confirmedDelegates);
    }

    public function render()
    {
        return view('livewire.admin.delegates.add-delegates-to-grip');
    }


    public function getConfirmedDelegates($eventId, $eventCategory, $eventYear)
    {
        $confirmedDelegates = array();
        $mainDelegates = MainDelegates::with(['additionalDelegates', 'transaction'])->where('event_id', $eventId)->get();

        foreach ($mainDelegates as $mainDelegate) {
            $companyName = $mainDelegate->alternative_company_name ?? $mainDelegate->company_name;

            if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                if ($mainDelegate->registration_status == "confirmed") {
                    array_push($confirmedDelegates, [
                        'registration_id' => $this->getRegistrationId($mainDelegate->transaction->id),
                        'first_name' => trim($mainDelegate->first_name),
                        'last_name' => trim($mainDelegate->last_name),
                        'name' => $this->formatFullName($mainDelegate->salutation, $mainDelegate->first_name, $mainDelegate->last_name),
                        'email' => $mainDelegate->email_address,
                        'phone_number' => $mainDelegate->mobile_number,
                        'location' => $mainDelegate->country,
                        'companyName' => trim($companyName),
                        'jobTitle' => trim($mainDelegate->job_title),
                        'headline' => $this->formatHeadline($mainDelegate->job_title, $companyName),
                        'picture_url' => "N/A",
                        'scan_id' => $this->getScanId($mainDelegate->id, 'main'),
                    ]);
                }
            }

            if ($mainDelegate->registration_status == "confirmed") {
                $subDelegates = $mainDelegate->additionalDelegates;
                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {
                        if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {
                            array_push($confirmedDelegates, [
                                'registration_id' => $this->getRegistrationId($subDelegate->transaction->id),
                                'first_name' => trim($subDelegate->first_name),
                                'last_name' => trim($subDelegate->last_name),
                                'name' => $this->formatFullName($subDelegate->salutation, $subDelegate->first_name, $subDelegate->last_name),
                                'email' => $subDelegate->email_address,
                                'phone_number' => $subDelegate->mobile_number,
                                'location' => $subDelegate->country,
                                'companyName' => trim($companyName),
                                'jobTitle' => trim($subDelegate->job_title),
                                'headline' => $this->formatHeadline($subDelegate->job_title, $companyName),
                                'picture_url' => "N/A",
                                'scan_id' => $this->getScanId($subDelegate->id, 'sub'),
                            ]);
                        }
                    }
                }
            }
        }
        return $confirmedDelegates;
    }

    public function getScanId($delegeateId, $delegateType)
    {
        $combinedStringScan =  "gpca@scan" . ',' . $this->event->id . ',' . $this->event->category . ',' . $delegeateId . ',' . $delegateType;
        $finalCryptStringScan = base64_encode($combinedStringScan);
        return 'gpca' . $finalCryptStringScan;
    }

    public function getRegistrationId($transactionId)
    {
        $lastDigit = 1000 + intval($transactionId);
        $finalRegistrationId = $this->event->year . $this->eventCode . $lastDigit;
        return $finalRegistrationId;
    }

    public function formatFullName($salutation, $fname, $lname)
    {
        $delegateSalutation = null;
        if ($salutation == "Dr." || $salutation == "Prof.") {
            $delegateSalutation = $salutation;
        }

        $fullName = $delegateSalutation;

        if (!empty($fname)) {
            $fullName .= ' ' . $fname;
        }

        if (!empty($lname)) {
            $fullName .= ' ' . $lname;
        }

        return preg_replace('/\s+/', ' ', trim($fullName));
    }

    public function formatHeadline($jobTitle, $companyName)
    {
        $headline = $jobTitle . " - " . trim($companyName);
        return $headline;
    }
}
