<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Transaction as Transactions;
use App\Models\Event as Events;
use App\Models\Member as Members;
use App\Models\PromoCode as PromoCodes;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class RegistrantsList extends Component
{
    use WithFileUploads;

    public $event;
    public $members;
    public $countries;
    public $companySectors;

    public $finalListOfRegistrants = array();
    public $finalListOfRegistrantsConst = array();
    public $eventId;
    public $eventCategory;
    public $searchTerm;
    public $showImportModal = false;

    // COMPANY INFO
    public $delegatePassType = "member", $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $assistantEmailAddress, $companyMobileNumber, $heardWhere;
    public $csvFile;
    public $rateType;
    public $rateTypeString;
    public $finalUnitPrice;

    // FILTERS
    public $filterByPassType, $filterByRegStatus, $filterByPayStatus;

    // ERRORS
    public $incompleDetails = array(), $emailYouAlreadyUsed = array(), $emailAlreadyExisting = array(), $promoCodeErrors = array();
    public $csvFileError;

    public $getEventCode;

    protected $listeners = ['importDelegateConfirmed' => 'submitImportRegistrants'];

    public function mount($eventId, $eventCategory)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->countries = config('app.countries');
        $this->companySectors = config('app.companySectors');
        $this->delegatePassType = "member";
        $this->eventId = $eventId;
        $this->eventCategory = $eventCategory;

        $mainDelegates = MainDelegates::where('event_id', $this->eventId)->orderBy('registered_date_time', 'DESC')->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $this->getEventCode = $code;
            }
        }

        if ($mainDelegates->isNotEmpty()) {
            foreach ($mainDelegates as $mainDelegate) {

                // get invoice number & transaction id
                $transactionId = Transactions::where('delegate_id', $mainDelegate->id)->where('delegate_type', "main")->value('id');
                $tempYear = Carbon::parse($mainDelegate->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($transactionId);
                $tempInvoiceNumber = $this->event->category . $tempYear . "/" . $lastDigit;
                $tempTransactionId = $this->event->year . $this->getEventCode . $lastDigit;

                // get pass type
                if ($mainDelegate->pass_type == 'member') {
                    $passType = "Member";
                } else {
                    $passType = "Non-Member";
                }

                // get reg status
                if ($mainDelegate->registration_status == 'confirmed') {
                    $regStatus = "Confirmed";
                } else if ($mainDelegate->registration_status == 'pending') {
                    $regStatus = "Pending";
                } else {
                    $regStatus = "Dropped out";
                }

                // get payment status
                if ($mainDelegate->payment_status == 'paid') {
                    $payStatus = "Paid";
                } else if ($mainDelegate->payment_status == 'free') {
                    $payStatus = "Free";
                } else {
                    $payStatus = "Unpaid";
                }

                array_push($this->finalListOfRegistrants, [
                    'mainDelegateId' => $mainDelegate->id,
                    'invoiceNumber' => $tempInvoiceNumber,
                    'transactionId' => $tempTransactionId,
                    'companyName' => $mainDelegate->company_name,
                    'country' => $mainDelegate->company_country,
                    'city' => $mainDelegate->company_city,
                    'passType' => $passType,
                    'quantity' => $mainDelegate->quantity,
                    'totalAmount' => $mainDelegate->total_amount,
                    'regDateTime' => Carbon::parse($mainDelegate->registered_date_time)->format('M j, Y g:iA'),
                    'regStatus' => $regStatus,
                    'payStatus' => $payStatus,
                ]);
            }

            $this->finalListOfRegistrantsConst = $this->finalListOfRegistrants;
        }
    }

    public function render()
    {
        return view('livewire.registrants.registrants-list');
    }

    public function filter()
    {
        if ($this->filterByPassType == null && $this->filterByRegStatus == null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = $this->finalListOfRegistrantsConst;
        } else if ($this->filterByPassType != null && $this->filterByRegStatus == null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType));
                })->all();
        } else if ($this->filterByPassType == null && $this->filterByRegStatus != null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['regStatus'] === ($this->filterByRegStatus));
                })->all();
        } else if ($this->filterByPassType == null && $this->filterByRegStatus == null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else if ($this->filterByPassType != null && $this->filterByRegStatus != null && $this->filterByPayStatus == null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType)) &&
                        strtolower($item['regStatus'] === ($this->filterByRegStatus));
                })->all();
        } else if ($this->filterByPassType != null && $this->filterByRegStatus == null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else if ($this->filterByPassType == null && $this->filterByRegStatus != null && $this->filterByPayStatus != null) {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['regStatus'] === ($this->filterByRegStatus)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        } else {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return strtolower($item['passType'] === ($this->filterByPassType)) &&
                        strtolower($item['regStatus'] === ($this->filterByRegStatus)) &&
                        strtolower($item['payStatus'] === ($this->filterByPayStatus));
                })->all();
        }
    }

    public function search()
    {
        if (empty($this->searchTerm)) {
            $this->finalListOfRegistrants = $this->finalListOfRegistrantsConst;
        } else {
            $this->finalListOfRegistrants = collect($this->finalListOfRegistrantsConst)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['companyName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['country']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['city']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['invoiceNumber']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['transactionId']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['regDateTime']), strtolower($this->searchTerm));
                })
                ->all();
        }
    }

    public function submitImportRegistrantsConfirmation()
    {
        $this->csvFileError = null;
        $this->incompleDetails = array();
        $this->emailYouAlreadyUsed = array();
        $this->emailAlreadyExisting = array();
        $this->promoCodeErrors = array();

        $this->validate([
            'delegatePassType' => 'required',
            'companyName' => 'required',
            'companySector' => 'required',
            'companyAddress' => 'required',
            'companyCountry' => 'required',
            'companyCity' => 'required',
            'companyMobileNumber' => 'required',
            'csvFile' => 'required',
        ]);

        $file = fopen($this->csvFile->getRealPath(), "r");
        $rows = [];
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            $rows[] = $row;
        }
        fclose($file);

        $checkIfCorrectFormat = true;
        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                if (count($rows[$i]) == 10) {
                    if (
                        $rows[$i][0] != "Promo Code used" ||
                        $rows[$i][1] != "Badge Type" ||
                        $rows[$i][2] != "Salutation" ||
                        $rows[$i][3] != "First Name" ||
                        $rows[$i][4] != "Middle Name" ||
                        $rows[$i][5] != "Last Name" ||
                        $rows[$i][6] != "Email Address" ||
                        $rows[$i][7] != "Mobile Number" ||
                        $rows[$i][8] != "Nationality" ||
                        $rows[$i][9] != "Job Title"
                    ) {
                        $checkIfCorrectFormat = false;
                    }
                } else {
                    $checkIfCorrectFormat = false;
                }
                break;
            }
        }

        if ($checkIfCorrectFormat) {
            $this->csvFileError = null;

            for ($i = 0; $i < count($rows); $i++) {
                if ($i == 0) {
                    continue;
                } else {
                    if (
                        empty($rows[$i][1]) ||
                        empty($rows[$i][3]) ||
                        empty($rows[$i][5]) ||
                        empty($rows[$i][6]) ||
                        empty($rows[$i][7]) ||
                        empty($rows[$i][8])  ||
                        empty($rows[$i][9])
                    ) {
                        $lineNumber = $i + 1;
                        array_push($this->incompleDetails, "Line $lineNumber have missing details!");
                    }
                }
            }

            if (count($this->incompleDetails) == 0) {
                $this->csvFileError = null;
                $this->incompleDetails = array();
                for ($i = 0; $i < count($rows); $i++) {
                    if ($i == 0) {
                        continue;
                    } else {
                        $email = $rows[$i][6];
                        for ($j = $i + 1; $j < count($rows); $j++) {
                            $tempEmail = $rows[$j][6];
                            if ($email === $tempEmail) {
                                $lineNumber = $i + 1;
                                array_push($this->emailYouAlreadyUsed, "Line $lineNumber email address is duplicated!");
                                break;
                            }
                        }
                    }
                }

                if (count($this->emailYouAlreadyUsed) == 0) {
                    $this->csvFileError = null;
                    $this->incompleDetails = array();
                    $this->emailYouAlreadyUsed = array();
                    for ($i = 0; $i < count($rows); $i++) {
                        if ($i == 0) {
                            continue;
                        } else {
                            $allDelegates = Transactions::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();

                            $mainDelegate = null;
                            $subDelegate = null;
                            foreach ($allDelegates as $delegate) {
                                if ($delegate->delegate_type == "main") {
                                    $mainDelegate = MainDelegates::where('id', $delegate->delegate_id)->where('email_address', $rows[$i][6])->first();
                                } else {
                                    $subDelegate = AdditionalDelegates::where('id', $delegate->delegate_id)->where('email_address', $rows[$i][6])->first();
                                }
                            }

                            if ($mainDelegate != null || $subDelegate != null) {
                                $lineNumber = $i + 1;
                                array_push($this->emailAlreadyExisting, "Line $lineNumber email address is already registered!");
                            }
                        }
                    }

                    if (count($this->emailAlreadyExisting) == 0) {
                        $this->csvFileError = null;
                        $this->incompleDetails = array();
                        $this->emailYouAlreadyUsed = array();
                        $this->emailAlreadyExisting = array();

                        for ($i = 0; $i < count($rows); $i++) {
                            if ($i == 0) {
                                continue;
                            } else {
                                // check promo codes if valid
                                $promoCodeUsed = $rows[$i][0];
                                $delegateType = $rows[$i][1];
                                $lineNumber = $i + 1;

                                if ($promoCodeUsed != null) {
                                    $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $promoCodeUsed)->where('badge_type', $delegateType)->first();
                                    if ($promoCode == null) {
                                        array_push($this->promoCodeErrors, "Line $lineNumber promo code is invalid");
                                    } else {
                                        if ($promoCode->total_usage < $promoCode->number_of_codes) {
                                            $validityDateTime = Carbon::parse($promoCode->validity);
                                            if (Carbon::now()->lt($validityDateTime)) {
                                            } else {
                                                array_push($this->promoCodeErrors, "Line $lineNumber promo code is expired already");
                                            }
                                        } else {
                                            array_push($this->promoCodeErrors, "Line $lineNumber promo code has reached its capacity");
                                        }
                                    }
                                }
                            }
                        }

                        if (count($this->promoCodeErrors) == 0) {
                            $this->csvFileError = null;
                            $this->incompleDetails = array();
                            $this->emailYouAlreadyUsed = array();
                            $this->emailAlreadyExisting = array();
                            $this->promoCodeErrors = array();

                            $this->dispatchBrowserEvent('swal:import-delegate-confirmation', [
                                'type' => 'warning',
                                'message' => 'Are you sure?',
                                'text' => "",
                            ]);
                        }
                    }
                }
            }
        } else {
            $this->csvFileError = "File is not valid, please make sure you have the correct format.";
        }
    }

    public function submitImportRegistrants()
    {

        $file = fopen($this->csvFile->getRealPath(), "r");
        $rows = [];
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            $rows[] = $row;
        }
        fclose($file);

        $this->checkUnitPrice();

        $quantity = count($rows) - 1;
        $finalNetAmount = 0;
        $finalVatPrice = 0;
        $finalDiscountPrice = 0;
        $totalAmount = 0;
        $mainDelegateId = 0;

        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                continue;
            } else {
                // calculate 
                $promoCodeUsed = $rows[$i][0];
                $delegateType = $rows[$i][1];

                if ($promoCodeUsed != null) {
                    $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $promoCodeUsed)->where('badge_type', $delegateType)->first();

                    $tempDiscount = $this->finalUnitPrice * ($promoCode->discount / 100);
                    $tempNetAmount = $this->finalUnitPrice - $tempDiscount;

                    $finalNetAmount +=  $tempNetAmount;
                    $finalDiscountPrice +=  $tempDiscount;
                } else {
                    $finalNetAmount +=  $this->finalUnitPrice;
                    $finalDiscountPrice +=  0;
                }
            }
        }

        $finalVatPrice = $finalNetAmount * ($this->event->event_vat / 100);
        $totalAmount = $finalNetAmount + $finalVatPrice;

        if ($totalAmount == 0) {
            $paymentStatus = "free";
            $registrationStatus = "confirmed";
        } else {
            $registrationStatus = "pending";
            $paymentStatus = "unpaid";
        }

        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                continue;
            } else if ($i == 1) {
                // add main delegate
                $newRegistrant = MainDelegates::create([
                    'event_id' => $this->event->id,
                    'pass_type' => $this->delegatePassType,
                    'rate_type' => $this->rateType,
                    'rate_type_string' => $this->rateTypeString,

                    'company_name' => $this->companyName,
                    'company_sector' => $this->companySector,
                    'company_address' => $this->companyAddress,
                    'company_country' => $this->companyCountry,
                    'company_city' => $this->companyCity,
                    'company_telephone_number' => $this->companyLandlineNumber,
                    'company_mobile_number' => $this->companyMobileNumber,
                    'assistant_email_address' => $this->assistantEmailAddress,

                    'salutation' => $rows[$i][2],
                    'first_name' => $rows[$i][3],
                    'middle_name' => $rows[$i][4],
                    'last_name' => $rows[$i][5],
                    'email_address' => $rows[$i][6],
                    'mobile_number' => $rows[$i][7],
                    'nationality' => $rows[$i][8],
                    'job_title' => $rows[$i][9],
                    'badge_type' => $rows[$i][1],
                    'pcode_used' => $rows[$i][0],

                    'heard_where' => $this->heardWhere,
                    'quantity' => $quantity,
                    'unit_price' => $this->finalUnitPrice,
                    'net_amount' => $finalNetAmount,
                    'vat_price' => $finalVatPrice,
                    'discount_price' => $finalDiscountPrice,
                    'total_amount' => $totalAmount,
                    'mode_of_payment' => "bankTransfer",
                    'registration_status' => $registrationStatus,
                    'payment_status' => $paymentStatus,
                    'registered_date_time' => Carbon::now(),
                    'paid_date_time' => null,
                ]);

                Transactions::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'delegate_id' => $newRegistrant->id,
                    'delegate_type' => "main",
                ]);

                $mainDelegateId = $newRegistrant->id;

                // EMAIL NOTIFICATION HERE
                $details = [
                    'name' => $rows[$i][2] . " " . $rows[$i][3] . " " . $rows[$i][4] . " " . $rows[$i][5],
                    'eventName' => $this->event->name,
                    'startDate' => Carbon::createFromFormat('Y-m-d', $this->event->event_start_date)->format('l j F'),
                    'endDate' => Carbon::createFromFormat('Y-m-d', $this->event->event_end_date)->format('l j F'),
                    'year' => $this->event->year,
                    'location' => $this->event->location,
                    'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . "main" . "/" . $newRegistrant->id,
                    'invoiceLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-invoice" . "/" . $newRegistrant->id,
                ];

                // Mail::to($rows[$i][6])->send(new RegistrationConfirmation($details));

                // if ($this->assistantEmailAddress != null) {
                //     Mail::to($this->assistantEmailAddress)->send(new RegistrationConfirmation($details));
                // }
            } else {
                // add sub delegate
                $newAdditionDelegate = AdditionalDelegates::create([
                    'main_delegate_id' => $mainDelegateId,
                    'salutation' => $rows[$i][2],
                    'first_name' => $rows[$i][3],
                    'middle_name' => $rows[$i][4],
                    'last_name' => $rows[$i][5],
                    'email_address' => $rows[$i][6],
                    'mobile_number' => $rows[$i][7],
                    'nationality' => $rows[$i][8],
                    'job_title' => $rows[$i][9],
                    'badge_type' => $rows[$i][1],
                    'pcode_used' => $rows[$i][0],
                ]);

                Transactions::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'delegate_id' => $newAdditionDelegate->id,
                    'delegate_type' => "sub",
                ]);


                // EMAIL NOTIFICATION HERE
                $details = [
                    'name' => $rows[$i][2] . " " . $rows[$i][3] . " " . $rows[$i][4] . " " . $rows[$i][5],
                    'eventName' => $this->event->name,
                    'startDate' => Carbon::createFromFormat('Y-m-d', $this->event->event_start_date)->format('l j F'),
                    'endDate' => Carbon::createFromFormat('Y-m-d', $this->event->event_end_date)->format('l j F'),
                    'year' => $this->event->year,
                    'location' => $this->event->location,
                    'badgeLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-badge" . "/" . "main" . "/" . $newAdditionDelegate->id,
                    'invoiceLink' => env('APP_URL') . "/" . $this->event->category . "/" . $this->event->id . "/view-invoice" . "/" . $mainDelegateId,
                ];

                // Mail::to($rows[$i][6])->send(new RegistrationConfirmation($details));
            }
        }

        $this->resetImportFields();
        $this->showImportModal = false;

        $this->dispatchBrowserEvent('swal:import-delegate', [
            'type' => 'success',
            'message' => 'Delegate Imported Successfully!',
            'text' => ''
        ]);
    }

    public function openImportModal()
    {
        $this->members = Members::where('active', true)->get();
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->resetImportFields();
        $this->showImportModal = false;
    }

    public function resetImportFields()
    {
        $this->companyName = null;
        $this->companySector = null;
        $this->companyAddress = null;
        $this->companyCountry = null;
        $this->companyCity = null;
        $this->companyLandlineNumber = null;
        $this->assistantEmailAddress = null;
        $this->companyMobileNumber = null;
        $this->heardWhere = null;
        $this->csvFile = null;

        $this->csvFileError = null;
        $this->incompleDetails = array();
        $this->emailYouAlreadyUsed = array();
        $this->emailAlreadyExisting = array();
        $this->promoCodeErrors = array();
    }

    public function checkUnitPrice()
    {
        $today = Carbon::today();

        // CHECK UNIT PRICE
        if ($this->event->eb_end_date != null && $this->event->eb_member_rate != null && $this->event->eb_nmember_rate != null) {
            if ($today->lte(Carbon::parse($this->event->eb_end_date))) {
                if ($this->delegatePassType == "member") {
                    $this->rateTypeString = "Early Bird Member Rate";
                    $this->finalUnitPrice = $this->event->eb_member_rate;
                } else {
                    $this->rateTypeString = "Early Bird Non-Member Rate";
                    $this->finalUnitPrice = $this->event->eb_nmember_rate;
                }
                $this->rateType = "Early Bird";
            } else {
                if ($this->delegatePassType == "member") {
                    $this->rateTypeString = "Standard Member Rate";
                    $this->finalUnitPrice = $this->event->std_member_rate;
                } else {
                    $this->rateTypeString = "Standard Non-Member Rate";
                    $this->finalUnitPrice = $this->event->std_nmember_rate;
                }
                $this->rateType = "Standard";
            }
        } else {
            if ($this->delegatePassType == "member") {
                $this->rateTypeString = "Standard Member Rate";
                $this->finalUnitPrice = $this->event->std_member_rate;
            } else {
                $this->rateTypeString = "Standard Non-Member Rate";
                $this->finalUnitPrice = $this->event->std_nmember_rate;
            }
            $this->rateType = "Standard";
        }
    }
}
