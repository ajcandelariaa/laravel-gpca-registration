<?php

namespace App\Http\Livewire;

use App\Models\MainDelegate as MainDelegates;
use App\Models\AdditionalDelegate as AdditionalDelegates;
use App\Models\Transaction as Transactions;
use App\Models\Event as Events;
use App\Models\Member as Members;
use App\Models\PromoCode as PromoCodes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
    public $delegatePassType = "member", $companyName, $companySector, $companyAddress, $companyCountry, $companyCity, $companyLandlineNumber, $assistantEmailAddress, $companyMobileNumber, $heardWhere, $paid;
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

        $mainDelegates = MainDelegates::where('event_id', $this->eventId)->orderBy('id', 'DESC')->get();

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

                // get pass type
                if ($mainDelegate->pass_type == 'member') {
                    $passType = "Member";
                } else if ($mainDelegate->pass_type == 'nonMember') {
                    $passType = "Non-Member";
                } else {
                    $passType = "Full Member";
                }

                // get reg status
                if ($mainDelegate->registration_status == 'confirmed') {
                    $regStatus = "Confirmed";
                } else if ($mainDelegate->registration_status == 'pending') {
                    $regStatus = "Pending";
                } else if ($mainDelegate->registration_status == 'droppedOut') {
                    $regStatus = "Dropped out";
                } else {
                    $regStatus = "Cancelled";
                }

                // get payment status
                if ($mainDelegate->payment_status == 'paid') {
                    $payStatus = "Paid";
                } else if ($mainDelegate->payment_status == 'free') {
                    $payStatus = "Free";
                } else if ($mainDelegate->payment_status == 'unpaid') {
                    $payStatus = "Unpaid";
                } else {
                    $payStatus = "Refunded";
                }

                if ($mainDelegate->mode_of_payment == "bankTransfer") {
                    $paymentMethod = "Bank Transfer";
                } else {
                    $paymentMethod = "Credit Card";
                }

                $totalDelegates = 0;
                if ($mainDelegate->delegate_replaced_by_id == null && (!$mainDelegate->delegate_refunded)) {
                    $totalDelegates++;
                }

                $additionalDelegates = AdditionalDelegates::where('main_delegate_id', $mainDelegate->id)->get();
                foreach ($additionalDelegates as $additionalDelegate) {
                    if ($additionalDelegate->delegate_replaced_by_id == null && (!$additionalDelegate->delegate_refunded)) {
                        $totalDelegates++;
                    }
                }

                array_push($this->finalListOfRegistrants, [
                    'mainDelegateId' => $mainDelegate->id,
                    'invoiceNumber' => $tempInvoiceNumber,
                    'companyName' => $mainDelegate->company_name,
                    'country' => $mainDelegate->company_country,
                    'city' => $mainDelegate->company_city,
                    'passType' => $passType,
                    'quantity' => $totalDelegates,
                    'totalAmount' => $mainDelegate->total_amount,
                    'regDateTime' => Carbon::parse($mainDelegate->registered_date_time)->format('M j, Y g:iA'),
                    'regStatus' => $regStatus,
                    'payStatus' => $payStatus,
                    'paymentMethod' => $paymentMethod,
                ]);
            }
            $this->finalListOfRegistrantsConst = $this->finalListOfRegistrants;
        }
    }

    public function render()
    {
        return view('livewire.admin.events.transactions.registrants-list');
    }

    public function clearFilter()
    {
        $this->filterByPassType = null;
        $this->filterByRegStatus = null;
        $this->filterByPayStatus = null;
        $this->filter();
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
                    return str_contains(strtolower($item['invoiceNumber']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['companyName']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['country']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['city']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['totalAmount']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['regDateTime']), strtolower($this->searchTerm)) ||
                        str_contains(strtolower($item['paymentMethod']), strtolower($this->searchTerm));
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
            'csvFile' => 'required|mimes:csv,txt',
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
                if (count($rows[$i]) == 20) {
                    if (
                        $rows[$i][0] != "Rate type" ||
                        $rows[$i][1] != "Company Name" ||
                        $rows[$i][2] != "Company Sector" ||
                        $rows[$i][3] != "Company Address" ||
                        $rows[$i][4] != "Country" ||
                        $rows[$i][5] != "City" ||
                        $rows[$i][6] != "Landline Number" ||
                        $rows[$i][7] != "Mobile Number" ||
                        $rows[$i][8] != "Assistants email address" ||
                        $rows[$i][9] != "Payment status" ||
                        $rows[$i][10] != "Promo Code used" ||
                        $rows[$i][11] != "Badge Type" ||
                        $rows[$i][12] != "Salutation" ||
                        $rows[$i][13] != "First Name" ||
                        $rows[$i][14] != "Middle Name" ||
                        $rows[$i][15] != "Last Name" ||
                        $rows[$i][16] != "Email Address" ||
                        $rows[$i][17] != "Mobile Number" ||
                        $rows[$i][18] != "Nationality" ||
                        $rows[$i][19] != "Job Title"
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
                        empty($rows[$i][0]) ||
                        empty($rows[$i][1]) ||
                        empty($rows[$i][2]) ||
                        empty($rows[$i][3]) ||
                        empty($rows[$i][4]) ||
                        empty($rows[$i][5]) ||
                        empty($rows[$i][7]) ||
                        empty($rows[$i][9]) ||
                        empty($rows[$i][11]) ||
                        empty($rows[$i][13]) ||
                        empty($rows[$i][15]) ||
                        empty($rows[$i][16]) ||
                        empty($rows[$i][17]) ||
                        empty($rows[$i][18])
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
                        $email = $rows[$i][16];
                        for ($j = $i + 1; $j < count($rows); $j++) {
                            $tempEmail = $rows[$j][16];
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

                            $tempEmail = $rows[$i][16];
                            $lineNumber = $i + 1;

                            foreach ($allDelegates as $delegate) {
                                if ($delegate->delegate_type == "main") {
                                    $mainDelegate = MainDelegates::where('id', $delegate->delegate_id)->where('email_address', $tempEmail)->first();

                                    if ($mainDelegate) {
                                        array_push($this->emailAlreadyExisting, "Line $lineNumber email address is already registered!");
                                    }
                                } else {
                                    $subDelegate = AdditionalDelegates::where('id', $delegate->delegate_id)->where('email_address', $tempEmail)->first();

                                    if ($subDelegate) {
                                        array_push($this->emailAlreadyExisting, "Line $lineNumber email address is already registered!");
                                    }
                                }
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
                                $promoCodeUsed = $rows[$i][10];
                                $badgeType = $rows[$i][11];
                                $lineNumber = $i + 1;

                                if ($promoCodeUsed != null) {
                                    $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $promoCodeUsed)->where('badge_type', $badgeType)->first();
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
                                } else {
                                    if ($rows[$i][9] == "Free") {
                                        array_push($this->promoCodeErrors, "Line $lineNumber promo code is required since the payment status is Free");
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

    public function openImportModal()
    {
        $this->showImportModal = true;
    }

    public function closeImportModal()
    {
        $this->resetImportFields();
        $this->showImportModal = false;
    }

    public function resetImportFields()
    {
        $this->csvFile = null;
        $this->csvFileError = null;
        $this->incompleDetails = array();
        $this->emailYouAlreadyUsed = array();
        $this->emailAlreadyExisting = array();
        $this->promoCodeErrors = array();
    }

    public function submitImportRegistrants()
    {
        $file = fopen($this->csvFile->getRealPath(), "r");
        $rows = [];
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            $rows[] = $row;
        }
        fclose($file);

        $earlyBirdUnpaid = array();
        $earlyBirdPaidFree = array();
        $standardUnpaid = array();
        $standardPaidFree = array();
        $finalData = array();

        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                continue;
            } else {
                $rateType = $rows[$i][0];
                $paymentStatus = $rows[$i][9];

                if ($rateType == "Early Bird") {
                    if ($paymentStatus == "Unpaid") {
                        $earlyBirdUnpaid = $this->groupDelegatesByCompany($earlyBirdUnpaid, $rows, $i, 'earlyBird');
                    } else {
                        $earlyBirdPaidFree = $this->groupDelegatesByCompany($earlyBirdPaidFree, $rows, $i, 'earlyBird');
                    }
                } else {
                    if ($paymentStatus == "Unpaid") {
                        $standardUnpaid = $this->groupDelegatesByCompany($standardUnpaid, $rows, $i, 'standard');
                    } else {
                        $standardPaidFree = $this->groupDelegatesByCompany($standardPaidFree, $rows, $i, 'standard');
                    }
                }
            }
        }
        $finalData = [$earlyBirdUnpaid, $earlyBirdPaidFree, $standardUnpaid, $standardPaidFree];

        foreach ($finalData as $transactions) {
            foreach ($transactions as $transaction) {

                // dd($transaction);

                $checkCompanyIfMember = Members::where('name', $transaction['company_name'])->first();

                if ($checkCompanyIfMember) {
                    if ($this->event->eb_full_member_rate != null && $this->event->std_full_member_rate != null) {
                        $delegatePassType = 'fullMember';

                        if ($transaction['rate_type'] == "earlyBird") {
                            $rateTypeString = "Full member early bird rate";
                            $finalUnitPrice = $this->event->eb_full_member_rate;
                        } else {
                            $rateTypeString = "Full member standard rate";
                            $finalUnitPrice = $this->event->std_full_member_rate;
                        }
                    } else {
                        $delegatePassType = 'member';

                        if ($transaction['rate_type'] == "earlyBird") {
                            $rateTypeString = "Member early bird rate";
                            $finalUnitPrice = $this->event->eb_member_rate;
                        } else {
                            $rateTypeString = "Member standard rate";
                            $finalUnitPrice = $this->event->std_member_rate;
                        }
                    }
                } else {
                    $delegatePassType = 'nonMember';

                    if ($transaction['rate_type'] == "earlyBird") {
                        $rateTypeString = "Non-Member early bird rate";
                        $finalUnitPrice = $this->event->eb_nmember_rate;
                    } else {
                        $rateTypeString = "Non-Member standard rate";
                        $finalUnitPrice = $this->event->std_nmember_rate;
                    }
                }

                if ($transaction['delegates'][0]['payment_status'] == "Unpaid") {
                    $registrationStatus = "pending";
                } else {
                    $registrationStatus = "confirmed";
                }

                $finalNetAmount = 0;
                $finalDiscount = 0;

                foreach ($transaction['delegates'] as $delegate) {
                    if($delegate['pcode_used'] != null){
                        $promoCodeDiscount = PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('badge_type', $delegate['badge_type'])->where('promo_code', $delegate['pcode_used'])->value('discount');

                        if($promoCodeDiscount != null){
                            PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('badge_type', $delegate['badge_type'])->where('promo_code', $delegate['pcode_used'])->increment('total_usage');

                            $finalDiscount += $finalUnitPrice * ($promoCodeDiscount / 100);
                            $finalNetAmount += $finalUnitPrice - ($finalUnitPrice * ($promoCodeDiscount / 100));
                        }
                    } else {
                        $finalNetAmount += $finalUnitPrice;
                    }
                }

                $finalVat = $finalNetAmount * ($this->event->event_vat / 100);
                $finalTotal = $finalNetAmount + $finalVat;

                if($finalTotal == 0){
                    $paymentStatus = "free";
                    $paidDateTime = Carbon::now();
                } else {
                    if($transaction['delegates'][0]['payment_status'] == "Unpaid"){
                        $paymentStatus = "unpaid";
                        $paidDateTime = null;
                    } else {
                        $paymentStatus = "paid";
                        $paidDateTime = Carbon::now();
                    }
                }

                $newTransaction = MainDelegates::create([
                    'event_id' => $this->event->id,
                    'pass_type' => $delegatePassType,
                    'rate_type' => $transaction['rate_type'],
                    'rate_type_string' => $rateTypeString,

                    'company_name' => $transaction['company_name'],
                    'company_sector' => $transaction['company_sector'],
                    'company_address' => $transaction['company_address'],
                    'company_country' => $transaction['company_country'],
                    'company_city' => $transaction['company_city'],
                    'company_telephone_number' => $transaction['company_telephone_number'],
                    'company_mobile_number' => $transaction['company_mobile_number'],
                    'assistant_email_address' => $transaction['assistant_email_address'],

                    'salutation' => $transaction['delegates'][0]['salutation'],
                    'first_name' => $transaction['delegates'][0]['first_name'],
                    'middle_name' => $transaction['delegates'][0]['middle_name'],
                    'last_name' => $transaction['delegates'][0]['last_name'],
                    'email_address' => $transaction['delegates'][0]['email_address'],
                    'mobile_number' => $transaction['delegates'][0]['mobile_number'],
                    'nationality' => $transaction['delegates'][0]['nationality'],
                    'job_title' => $transaction['delegates'][0]['job_title'],
                    'badge_type' => $transaction['delegates'][0]['badge_type'],
                    'pcode_used' => $transaction['delegates'][0]['pcode_used'],

                    'heard_where' => null,
                    'quantity' => count($transaction['delegates']),
                    'unit_price' => $finalUnitPrice,
                    'net_amount' => $finalNetAmount,
                    'vat_price' => $finalVat,
                    'discount_price' => $finalDiscount,
                    'total_amount' => $finalTotal,
                    'mode_of_payment' => "bankTransfer",
                    'registration_status' => $registrationStatus,
                    'payment_status' => $paymentStatus,
                    'registered_date_time' => Carbon::now(),
                    'paid_date_time' => $paidDateTime,

                    'registration_method' => 'imported',
                ]);

                Transactions::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'delegate_id' => $newTransaction->id,
                    'delegate_type' => "main",
                ]);

                if(count($transaction['delegates']) > 1){
                    for($x = 0; $x < count($transaction['delegates']); $x++){
                        if($x == 0){
                            continue;
                        } else {
                            $addtionalDelegate = AdditionalDelegates::create([
                                'main_delegate_id' => $newTransaction->id,
                                'salutation' => $transaction['delegates'][$x]['salutation'],
                                'first_name' => $transaction['delegates'][$x]['first_name'],
                                'middle_name' => $transaction['delegates'][$x]['middle_name'],
                                'last_name' => $transaction['delegates'][$x]['last_name'],
                                'email_address' => $transaction['delegates'][$x]['email_address'],
                                'mobile_number' => $transaction['delegates'][$x]['mobile_number'],
                                'nationality' => $transaction['delegates'][$x]['nationality'],
                                'job_title' => $transaction['delegates'][$x]['job_title'],
                                'badge_type' => $transaction['delegates'][$x]['badge_type'],
                                'pcode_used' => $transaction['delegates'][$x]['pcode_used'],
                            ]);
            
                            Transactions::create([
                                'event_id' => $this->event->id,
                                'event_category' => $this->event->category,
                                'delegate_id' => $addtionalDelegate->id,
                                'delegate_type' => "sub",
                            ]);
                        }
                    }
                }
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

    public function groupDelegatesByCompany($arrayData, $rows, $i, $rateType)
    {
        if (count($arrayData) > 0) {
            $checkIfMatch = 0;
            $arrayDataIndex = 0;
            foreach ($arrayData as $index => $data) {
                if ($data['company_name'] == $rows[$i][1]) {
                    $checkIfMatch++;
                    $arrayDataIndex = $index;
                    break;
                }
            }

            if ($checkIfMatch > 0) {
                $delegate = [
                    'payment_status' => $rows[$i][9],
                    'pcode_used' => $rows[$i][10],
                    'badge_type' => $rows[$i][11],
                    'salutation' => $rows[$i][12],
                    'first_name' => $rows[$i][13],
                    'middle_name' => $rows[$i][14],
                    'last_name' => $rows[$i][15],
                    'email_address' => $rows[$i][16],
                    'mobile_number' => $rows[$i][17],
                    'nationality' => $rows[$i][18],
                    'job_title' => $rows[$i][19],
                ];

                array_push($arrayData[$arrayDataIndex]['delegates'], $delegate);
            } else {
                $delegate = [
                    'payment_status' => $rows[$i][9],
                    'pcode_used' => $rows[$i][10],
                    'badge_type' => $rows[$i][11],
                    'salutation' => $rows[$i][12],
                    'first_name' => $rows[$i][13],
                    'middle_name' => $rows[$i][14],
                    'last_name' => $rows[$i][15],
                    'email_address' => $rows[$i][16],
                    'mobile_number' => $rows[$i][17],
                    'nationality' => $rows[$i][18],
                    'job_title' => $rows[$i][19],
                ];

                array_push($arrayData, [
                    'rate_type' => $rateType,
                    'company_name' => $rows[$i][1],
                    'company_sector' => $rows[$i][2],
                    'company_address' => $rows[$i][3],
                    'company_country' => $rows[$i][4],
                    'company_city' => $rows[$i][5],
                    'company_telephone_number' => $rows[$i][6],
                    'company_mobile_number' => $rows[$i][7],
                    'assistant_email_address' => $rows[$i][8],
                    'delegates' => [
                        $delegate
                    ],
                ]);
            }
        } else {
            $delegate = [
                'payment_status' => $rows[$i][9],
                'pcode_used' => $rows[$i][10],
                'badge_type' => $rows[$i][11],
                'salutation' => $rows[$i][12],
                'first_name' => $rows[$i][13],
                'middle_name' => $rows[$i][14],
                'last_name' => $rows[$i][15],
                'email_address' => $rows[$i][16],
                'mobile_number' => $rows[$i][17],
                'nationality' => $rows[$i][18],
                'job_title' => $rows[$i][19],
            ];

            array_push($arrayData, [
                'rate_type' => $rateType,
                'company_name' => $rows[$i][1],
                'company_sector' => $rows[$i][2],
                'company_address' => $rows[$i][3],
                'company_country' => $rows[$i][4],
                'company_city' => $rows[$i][5],
                'company_telephone_number' => $rows[$i][6],
                'company_mobile_number' => $rows[$i][7],
                'assistant_email_address' => $rows[$i][8],
                'delegates' => [
                    $delegate
                ],
            ]);
        }
        return $arrayData;
    }
}
