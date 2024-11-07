<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AddDelegatesToGrip extends Component
{
    public $event, $delegatesEmailFromGrip, $confirmedDelegates;
    public $eventCode;
    public $countAlreadyAdded = 0;
    public $activeSelectedIndex;

    protected $listeners = ['addDelegateConfirmed' => 'addDelegate', 'addRemaniningDelegatesConfirmed' => 'addRemainingDelegates'];

    public function mount($event)
    {
        $this->event = $event;
        $this->eventCode = config('app.eventCategories')[$event->category];
        $this->delegatesEmailFromGrip = array();

        $url = env('API_GRIP_URL') . '/thing/register/detail';
        $response = Http::withToken(env('API_GRIP_AUTH_TOKEN'))->get($url)->json();
        if ($response['success']) {
            foreach ($response['data'] as $apiDelegate) {
                array_push($this->delegatesEmailFromGrip, $apiDelegate['email']);
            }
        }

        $this->confirmedDelegates = $this->getConfirmedDelegates();
    }

    public function render()
    {
        return view('livewire.admin.events.delegates.add-delegates-to-grip');
    }

    public function addDelegateConfirmation($index)
    {
        $this->activeSelectedIndex = $index;
        $this->dispatchBrowserEvent('swal:add-to-grip-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure you want to add this delegate to Grip?',
            'text' => "",
            'livewireEmit' => "addDelegateConfirmed",
        ]);
    }

    public function addDelegate()
    {
        $selectedDelegate = $this->confirmedDelegates[$this->activeSelectedIndex];
        $url = env('API_GRIP_URL') . '/thing/register';
        $token = env('API_GRIP_AUTH_TOKEN');
        $applicationId = env('API_GRIP_APP_ID');
        $containerId = env('API_GRIP_CONTAINER_ID');
        $typeId = env('API_GRIP_TYPE_ID');

        $data = [
            'application_id' => $applicationId,
            'new_event_join_type' => "join_and_patch",
            'default_container_id' => $containerId,
            'type_id' => $typeId,
            'registration_id' => $selectedDelegate['registration_id'],
            'first_name' => $selectedDelegate['first_name'],
            'last_name' => $selectedDelegate['last_name'],
            'name' => $selectedDelegate['name'],
            'email' => $selectedDelegate['email'],
            'phone_number' => $selectedDelegate['phone_number'],
            'location' => $selectedDelegate['location'],
            'company_name' => $selectedDelegate['company_name'],
            'job_title' => $selectedDelegate['job_title'],
            'headline' => $selectedDelegate['headline'],
            'picture_url' => $selectedDelegate['picture_url'],
            'metadata_raw' => [
                'scan_id' => [
                    'en-gb' =>  $selectedDelegate['scan_id'],
                ],
            ],
            'picture_url' => "https://www.gpcaforum.com/wp-content/uploads/2022/08/af-male2.jpg",
        ];

        try {
            $response = Http::withToken($token)->post($url, $data)->json();
            if (isset($response['success']) && $response['success']) {
                $this->countAlreadyAdded++;
                $this->confirmedDelegates[$this->activeSelectedIndex]['isDelegateAlreadyAdded'] = true;
                $this->dispatchBrowserEvent('swal:add-to-grip', [
                    'type' => 'success',
                    'message' => 'Delegate added to Grip backend!',
                    'text' => "",
                ]);
            } else {
                $this->dispatchBrowserEvent('swal:add-to-grip', [
                    'type' => 'error',
                    'message' => 'An error occured while adding the delegate!',
                    'text' => "",
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal:add-to-grip', [
                'type' => 'error',
                'message' => 'An error occured while adding the delegate!',
                'text' => "$e",
            ]);
        }
    }

    public function addRemainingDelegatesConfirmation()
    {
        $this->dispatchBrowserEvent('swal:add-to-grip-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure you want add the remaining delegates to Grip?',
            'text' => "",
            'livewireEmit' => "addRemaniningDelegatesConfirmed",
        ]);
    }

    public function addRemainingDelegates()
    {
        $url = env('API_GRIP_URL') . '/thing/register';
        $token = env('API_GRIP_AUTH_TOKEN');
        $applicationId = env('API_GRIP_APP_ID');
        $containerId = env('API_GRIP_CONTAINER_ID');
        $typeId = env('API_GRIP_TYPE_ID');
        $errorCount = 0;

        try {
            foreach ($this->confirmedDelegates as $index => $confirmedDelegate) {
                if (!$confirmedDelegate['isDelegateAlreadyAdded']) {
                    $data = [
                        'application_id' => $applicationId,
                        'new_event_join_type' => "join_and_patch",
                        'default_container_id' => $containerId,
                        'type_id' => $typeId,
                        'registration_id' => $confirmedDelegate['registration_id'],
                        'first_name' => $confirmedDelegate['first_name'],
                        'last_name' => $confirmedDelegate['last_name'],
                        'name' => $confirmedDelegate['name'],
                        'email' => $confirmedDelegate['email'],
                        'phone_number' => $confirmedDelegate['phone_number'],
                        'location' => $confirmedDelegate['location'],
                        'company_name' => $confirmedDelegate['company_name'],
                        'job_title' => $confirmedDelegate['job_title'],
                        'headline' => $confirmedDelegate['headline'],
                        'picture_url' => $confirmedDelegate['picture_url'],
                        'metadata_raw' => [
                            'scan_id' => [
                                'en-gb' =>  $confirmedDelegate['scan_id'],
                            ],
                        ],
                        'picture_url' => "https://www.gpcaforum.com/wp-content/uploads/2022/08/af-male2.jpg",
                    ];
    
                    try {
                        $response = Http::withToken($token)->post($url, $data)->json();
                        if (isset($response['success']) && $response['success']) {
                            $this->countAlreadyAdded++;
                            $this->confirmedDelegates[$index]['isDelegateAlreadyAdded'] = true;
                        } else {
                            $errorCount++;
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                    }
                }
            }
            if($errorCount > 0){
                $message = "$errorCount delegates are not added due to an error";
                $this->dispatchBrowserEvent('swal:add-to-grip', [
                    'type' => 'success',
                    'message' => $message,
                    'text' => "",
                ]);
            } else {
                $this->dispatchBrowserEvent('swal:add-to-grip', [
                    'type' => 'success',
                    'message' => 'All remaning delegates are now added to the Grip backend!',
                    'text' => "",
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal:add-to-grip', [
                'type' => 'error',
                'message' => 'An error occured while adding the remaining delegates!',
                'text' => "$e",
            ]);
        }
    }












    public function getConfirmedDelegates()
    {
        $confirmedDelegates = array();
        $mainDelegates = MainDelegates::with(['additionalDelegates', 'transaction'])->where('event_id', $this->event->id)->limit(100)->get();

        foreach ($mainDelegates as $mainDelegate) {
            $companyName = $mainDelegate->alternative_company_name ?? $mainDelegate->company_name;

            if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                if ($mainDelegate->registration_status == "confirmed") {

                    if ($this->isDelegateAlreadyAdded($mainDelegate->email_address)) {
                        $this->countAlreadyAdded++;
                    }

                    array_push($confirmedDelegates, [
                        'registration_id' => $this->getRegistrationId($mainDelegate->transaction->id),
                        'first_name' => trim($mainDelegate->first_name),
                        'last_name' => trim($mainDelegate->last_name),
                        'name' => $this->formatFullName($mainDelegate->salutation, $mainDelegate->first_name, $mainDelegate->last_name),
                        'email' => $mainDelegate->email_address,
                        'phone_number' => $mainDelegate->mobile_number,
                        'location' => $mainDelegate->country,
                        'company_name' => trim($companyName),
                        'job_title' => trim($mainDelegate->job_title),
                        'headline' => $this->formatHeadline($mainDelegate->job_title, $companyName),
                        'picture_url' => "N/A",
                        'scan_id' => $this->getScanId($mainDelegate->id, 'main'),
                        'isDelegateAlreadyAdded' => $this->isDelegateAlreadyAdded($mainDelegate->email_address),
                    ]);
                }
            }

            if ($mainDelegate->registration_status == "confirmed") {
                $subDelegates = $mainDelegate->additionalDelegates;
                if (!$subDelegates->isEmpty()) {
                    foreach ($subDelegates as $subDelegate) {
                        if ($subDelegate->delegate_replaced_by_id == null && (!$subDelegate->delegate_refunded)) {

                            if ($this->isDelegateAlreadyAdded($subDelegate->email_address)) {
                                $this->countAlreadyAdded++;
                            }

                            array_push($confirmedDelegates, [
                                'registration_id' => $this->getRegistrationId($subDelegate->transaction->id),
                                'first_name' => trim($subDelegate->first_name),
                                'last_name' => trim($subDelegate->last_name),
                                'name' => $this->formatFullName($subDelegate->salutation, $subDelegate->first_name, $subDelegate->last_name),
                                'email' => $subDelegate->email_address,
                                'phone_number' => $subDelegate->mobile_number,
                                'location' => $subDelegate->country,
                                'company_name' => trim($companyName),
                                'job_title' => trim($subDelegate->job_title),
                                'headline' => $this->formatHeadline($subDelegate->job_title, $companyName),
                                'picture_url' => "N/A",
                                'scan_id' => $this->getScanId($subDelegate->id, 'sub'),
                                'isDelegateAlreadyAdded' => $this->isDelegateAlreadyAdded($subDelegate->email_address),
                            ]);
                        }
                    }
                }
            }
        }
        return $confirmedDelegates;
    }

    public function isDelegateAlreadyAdded($delegateEmailAddress)
    {
        $delegatesEmailFromGripUpper = array_map('strtoupper', $this->delegatesEmailFromGrip);
        $delegateEmailAddressUpper = strtoupper($delegateEmailAddress);

        return in_array($delegateEmailAddressUpper, $delegatesEmailFromGripUpper);
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
        $headline = trim($jobTitle) . " - " . trim($companyName);
        return trim($headline);
    }
}
