<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use App\Models\MainVisitor as MainVisitors;
use App\Models\AdditionalVisitor as AdditionalVisitors;
use App\Models\VisitorTransaction as VisitorTransactions;
use App\Models\PromoCode as PromoCodes;
use App\Models\PromoCodeAddtionalBadgeType as PromoCodeAddtionalBadgeTypes;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;

class VisitorRegistrantsList extends Component
{
    use WithFileUploads;

    public $event;
    public $members;
    public $countries;
    public $companySectors;

    public $finalListOfRegistrants = array(), $finalListOfRegistrantsConst = array();
    public $eventId, $eventCategory;
    public $searchTerm;
    public $showImportModal = false;

    // COMPANY INFO
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
        $this->eventId = $eventId;
        $this->eventCategory = $eventCategory;

        $mainVisitors = MainVisitors::where('event_id', $this->eventId)->orderBy('id', 'DESC')->get();

        foreach (config('app.eventCategories') as $eventCategoryC => $code) {
            if ($this->event->category == $eventCategoryC) {
                $this->getEventCode = $code;
            }
        }

        if ($mainVisitors->isNotEmpty()) {
            foreach ($mainVisitors as $mainVisitor) {

                // get invoice number & transaction id
                $transactionId = VisitorTransactions::where('visitor_id', $mainVisitor->id)->where('visitor_type', "main")->value('id');
                $tempYear = Carbon::parse($mainVisitor->registered_date_time)->format('y');
                $lastDigit = 1000 + intval($transactionId);
                $tempInvoiceNumber = $this->event->category . $tempYear . "/" . $lastDigit;

                // get pass type
                if ($mainVisitor->pass_type == 'member') {
                    $passType = "Member";
                } else if ($mainVisitor->pass_type == 'nonMember') {
                    $passType = "Non-Member";
                } else {
                    $passType = "Full Member";
                }

                // get reg status
                if ($mainVisitor->registration_status == 'confirmed') {
                    $regStatus = "Confirmed";
                } else if ($mainVisitor->registration_status == 'pending') {
                    $regStatus = "Pending";
                } else if ($mainVisitor->registration_status == 'droppedOut') {
                    $regStatus = "Dropped out";
                } else {
                    $regStatus = "Cancelled";
                }

                // get payment status
                if ($mainVisitor->payment_status == 'paid') {
                    $payStatus = "Paid";
                } else if ($mainVisitor->payment_status == 'free') {
                    $payStatus = "Free";
                } else if ($mainVisitor->payment_status == 'unpaid') {
                    $payStatus = "Unpaid";
                } else {
                    $payStatus = "Refunded";
                }

                if ($mainVisitor->mode_of_payment == "bankTransfer") {
                    $paymentMethod = "Bank Transfer";
                } else {
                    $paymentMethod = "Credit Card";
                }


                $totalVisitors = 0;
                if ($mainVisitor->visitor_replaced_by_id == null && (!$mainVisitor->visitor_refunded)) {
                    $totalVisitors++;
                }

                $additionalVisitors = AdditionalVisitors::where('main_visitor_id', $mainVisitor->id)->get();
                foreach ($additionalVisitors as $additionalVisitor) {
                    if ($additionalVisitor->visitor_replaced_by_id == null && (!$additionalVisitor->visitor_refunded)) {
                        $totalVisitors++;
                    }
                }

                if ($mainVisitor->alternative_company_name != null) {
                    $companyName = $mainVisitor->alternative_company_name;
                } else {
                    $companyName = $mainVisitor->company_name;
                }

                array_push($this->finalListOfRegistrants, [
                    'mainVisitorId' => $mainVisitor->id,
                    'invoiceNumber' => $tempInvoiceNumber,
                    'companyName' => $companyName,
                    'alternativeCompanyName' => $mainVisitor->alternative_company_name,
                    'country' => $mainVisitor->company_country,
                    'city' => $mainVisitor->company_city,
                    'passType' => $passType,
                    'quantity' => $totalVisitors,
                    'totalAmount' => $mainVisitor->total_amount,
                    'regDateTime' => Carbon::parse($mainVisitor->registered_date_time)->format('M j, Y g:iA'),
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
        return view('livewire.admin.events.transactions.visitor.visitor-registrants-list');
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

        $file = fopen($this->csvFile->getRealPath(), "r", 'UTF-8');
        $rows = [];

        $rowCounter = 0;
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            if ($rowCounter > 0) {
                $tempRow = [];
                foreach ($row as $col) {
                    $tempRow[] = trim($col);
                }
                $row = $tempRow;
            }

            $rowCounter++;
            $rows[] = $row;
        }
        fclose($file);

        $checkIfCorrectFormat = true;
        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                if (count($rows[$i]) == 22) {
                    if (
                        $rows[$i][0] != "Rate type" ||
                        $rows[$i][1] != "Pass type" ||
                        $rows[$i][2] != "Company Name" ||
                        $rows[$i][3] != "Alternative Company Name" ||
                        $rows[$i][4] != "Company Sector" ||
                        $rows[$i][5] != "Company Address" ||
                        $rows[$i][6] != "Country" ||
                        $rows[$i][7] != "City" ||
                        $rows[$i][8] != "Landline Number" ||
                        $rows[$i][9] != "Mobile Number" ||
                        $rows[$i][10] != "Assistants email address" ||
                        $rows[$i][11] != "Payment status" ||
                        $rows[$i][12] != "Promo Code used" ||
                        $rows[$i][13] != "Badge Type" ||
                        $rows[$i][14] != "Salutation" ||
                        $rows[$i][15] != "First Name" ||
                        $rows[$i][16] != "Middle Name" ||
                        $rows[$i][17] != "Last Name" ||
                        $rows[$i][18] != "Email Address" ||
                        $rows[$i][19] != "Mobile Number" ||
                        $rows[$i][20] != "Nationality" ||
                        $rows[$i][21] != "Job Title"
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
                        empty($rows[$i][4]) ||
                        empty($rows[$i][5]) ||
                        empty($rows[$i][6]) ||
                        empty($rows[$i][7]) ||
                        empty($rows[$i][9]) ||
                        empty($rows[$i][11]) ||
                        empty($rows[$i][13]) ||
                        empty($rows[$i][15]) ||
                        empty($rows[$i][17]) ||
                        empty($rows[$i][18]) ||
                        empty($rows[$i][19]) ||
                        empty($rows[$i][20]) ||
                        empty($rows[$i][21])
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
                        $email = $rows[$i][18];
                        for ($j = $i + 1; $j < count($rows); $j++) {
                            $tempEmail = $rows[$j][18];
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
                            $allVisitors = VisitorTransactions::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();
                            $mainVisitor = null;
                            $subVisitor = null;

                            $tempEmail = $rows[$i][18];
                            $lineNumber = $i + 1;

                            foreach ($allVisitors as $visitor) {
                                if ($visitor->visitor_type == "main") {
                                    $mainVisitor = MainVisitors::where('id', $visitor->visitor_id)->where('email_address', $tempEmail)->first();

                                    if ($mainVisitor) {
                                        array_push($this->emailAlreadyExisting, "Line $lineNumber email address is already registered!");
                                    }
                                } else {
                                    $subVisitor = AdditionalVisitors::where('id', $visitor->visitor_id)->where('email_address', $tempEmail)->first();

                                    if ($subVisitor) {
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
                                $promoCodeUsed = $rows[$i][12];
                                $badgeType = $rows[$i][13];
                                $lineNumber = $i + 1;

                                if ($promoCodeUsed != null) {
                                    $promoCode = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->where('active', true)->where('promo_code', $promoCodeUsed)->first();

                                    $promoCodeChecker = true;

                                    if ($promoCode != null) {
                                        if ($promoCode->badge_type == $badgeType) {
                                            $promoCodeChecker = true;
                                        } else {
                                            $promoCodeAddtionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->eventId)->where('promo_code_id', $promoCode->id)->where('badge_type', $badgeType)->first();

                                            if ($promoCodeAddtionalBadgeType != null) {
                                                $promoCodeChecker = true;
                                            } else {
                                                $promoCodeChecker = false;
                                            }
                                        }
                                    } else {
                                        $promoCodeChecker = false;
                                    }

                                    if ($promoCodeChecker) {
                                        if ($promoCode->total_usage < $promoCode->number_of_codes) {
                                            $validityDateTime = Carbon::parse($promoCode->validity);
                                            if (Carbon::now()->lt($validityDateTime)) {
                                            } else {
                                                array_push($this->promoCodeErrors, "Line $lineNumber promo code is expired already");
                                            }
                                        } else {
                                            array_push($this->promoCodeErrors, "Line $lineNumber promo code has reached its capacity");
                                        }
                                    } else {
                                        array_push($this->promoCodeErrors, "Line $lineNumber promo code is invalid");
                                    }
                                } else {
                                    if ($rows[$i][11] == "Free") {
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
        $earlyBirdPaid = array();
        $earlyBirdFree = array();
        $standardUnpaid = array();
        $standardPaid = array();
        $standardFree = array();
        $finalData = array();

        for ($i = 0; $i < count($rows); $i++) {
            if ($i == 0) {
                continue;
            } else {
                $rateType = $rows[$i][0];
                $paymentStatus = $rows[$i][11];

                if ($rateType == "Early Bird") {
                    if ($paymentStatus == "Unpaid") {
                        $earlyBirdUnpaid = $this->groupVisitorsByCompany($earlyBirdUnpaid, $rows, $i, 'earlyBird');
                    } else if ($paymentStatus == "Paid") {
                        $earlyBirdPaid = $this->groupVisitorsByCompany($earlyBirdPaid, $rows, $i, 'earlyBird');
                    } else {
                        $earlyBirdFree = $this->groupVisitorsByCompany($earlyBirdFree, $rows, $i, 'earlyBird');
                    }
                } else {
                    if ($paymentStatus == "Unpaid") {
                        $standardUnpaid = $this->groupVisitorsByCompany($standardUnpaid, $rows, $i, 'standard');
                    } else if ($paymentStatus == "Paid") {
                        $standardPaid = $this->groupVisitorsByCompany($standardPaid, $rows, $i, 'standard');
                    } else {
                        $standardFree = $this->groupVisitorsByCompany($standardFree, $rows, $i, 'standard');
                    }
                }
            }
        }
        $finalData = [$earlyBirdUnpaid, $earlyBirdPaid, $earlyBirdFree, $standardUnpaid, $standardPaid, $standardFree];
        // dd($finalData);
        foreach ($finalData as $transactions) {
            foreach ($transactions as $transaction) {
                if($transaction['pass_type'] == "Full Member"){
                    $visitorPassType = 'fullMember';

                    if ($transaction['rate_type'] == "earlyBird") {
                        $rateTypeString = "Full member early bird rate";
                        $finalUnitPrice = $this->event->eb_full_member_rate;
                    } else {
                        $rateTypeString = "Full member standard rate";
                        $finalUnitPrice = $this->event->std_full_member_rate;
                    }
                } else if($transaction['pass_type'] == "Member"){
                    $visitorPassType = 'member';

                    if ($transaction['rate_type'] == "earlyBird") {
                        $rateTypeString = "Member early bird rate";
                        $finalUnitPrice = $this->event->eb_member_rate;
                    } else {
                        $rateTypeString = "Member standard rate";
                        $finalUnitPrice = $this->event->std_member_rate;
                    }
                } else {
                    $visitorPassType = 'nonMember';

                    if ($transaction['rate_type'] == "earlyBird") {
                        $rateTypeString = "Non-Member early bird rate";
                        $finalUnitPrice = $this->event->eb_nmember_rate;
                    } else {
                        $rateTypeString = "Non-Member standard rate";
                        $finalUnitPrice = $this->event->std_nmember_rate;
                    }
                }

                if ($transaction['visitors'][0]['payment_status'] == "Unpaid") {
                    $registrationStatus = "pending";
                } else {
                    $registrationStatus = "confirmed";
                }

                $finalNetAmount = 0;
                $finalDiscount = 0;
                $finalVat = 0;
                $finalTotal = 0;

                foreach ($transaction['visitors'] as $visitor) {
                    if($visitor['pcode_used'] != null){
                        $promoCode = PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('promo_code', $visitor['pcode_used'])->first();

                        if($promoCode != null){
                            $checker = false;

                            if($visitor['badge_type'] == $promoCode->badge_type ){
                                $checker = true;
                            } else {
                                $additionalBadgeType = PromoCodeAddtionalBadgeTypes::where('event_id', $this->event->id)->where('promo_code_id', $promoCode->id)->where('badge_type', $visitor['badge_type'])->first();

                                if($additionalBadgeType != null){
                                    $checker = true;
                                } else {
                                    $checker = false;
                                }
                            }

                            if($checker){
                                PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->where('badge_type', $visitor['badge_type'])->where('promo_code', $visitor['pcode_used'])->increment('total_usage');

                                if($promoCode->discount_type == "percentage"){
                                    $finalDiscount += $finalUnitPrice * ($promoCode->discount / 100);
                                    $finalNetAmount += $finalUnitPrice - ($finalUnitPrice * ($promoCode->discount / 100));
                                } else if ($promoCode->discount_type == "price") {
                                    $finalDiscount += $promoCode->discount;
                                    $finalNetAmount += $finalUnitPrice - $promoCode->discount;
                                } else {
                                    $finalDiscount += 0;
                                    $finalNetAmount += $promoCode->new_rate;
                                }
                            } else {
                                $finalDiscount += 0;
                                $finalNetAmount += $finalUnitPrice;
                            }
                        } else {
                            $finalDiscount += 0;
                            $finalNetAmount += $finalUnitPrice;
                        }
                    } else {
                        $finalDiscount += 0;
                        $finalNetAmount += $finalUnitPrice;
                    }
                }

                $finalVat = $finalNetAmount * ($this->event->event_vat / 100);
                $finalTotal = $finalNetAmount + $finalVat;

                if($finalTotal == 0){
                    $paymentStatus = "free";
                    $paidDateTime = Carbon::now();
                } else {
                    if($transaction['visitors'][0]['payment_status'] == "Unpaid"){
                        $paymentStatus = "unpaid";
                        $paidDateTime = null;
                    } else {
                        $paymentStatus = "paid";
                        $paidDateTime = Carbon::now();
                    }
                }

                $newTransaction = MainVisitors::create([
                    'event_id' => $this->event->id,
                    'pass_type' => $visitorPassType,
                    'rate_type' => $transaction['rate_type'],
                    'rate_type_string' => $rateTypeString,

                    'company_name' => $transaction['company_name'],
                    'alternative_company_name' => $transaction['alternative_company_name'],
                    'company_sector' => $transaction['company_sector'],
                    'company_address' => $transaction['company_address'],
                    'company_country' => $transaction['company_country'],
                    'company_city' => $transaction['company_city'],
                    'company_telephone_number' => $transaction['company_telephone_number'],
                    'company_mobile_number' => $transaction['company_mobile_number'],
                    'assistant_email_address' => $transaction['assistant_email_address'],

                    'salutation' => $transaction['visitors'][0]['salutation'],
                    'first_name' => $transaction['visitors'][0]['first_name'],
                    'middle_name' => $transaction['visitors'][0]['middle_name'],
                    'last_name' => $transaction['visitors'][0]['last_name'],
                    'email_address' => $transaction['visitors'][0]['email_address'],
                    'mobile_number' => $transaction['visitors'][0]['mobile_number'],
                    'nationality' => $transaction['visitors'][0]['nationality'],
                    'job_title' => $transaction['visitors'][0]['job_title'],
                    'badge_type' => $transaction['visitors'][0]['badge_type'],
                    'pcode_used' => $transaction['visitors'][0]['pcode_used'],

                    'quantity' => count($transaction['visitors']),
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

                VisitorTransactions::create([
                    'event_id' => $this->event->id,
                    'event_category' => $this->event->category,
                    'visitor_id' => $newTransaction->id,
                    'visitor_type' => "main",
                ]);

                if(count($transaction['visitors']) > 1){
                    for($x = 0; $x < count($transaction['visitors']); $x++){
                        if($x == 0){
                            continue;
                        } else {
                            $additionalVisitor = AdditionalVisitors::create([
                                'main_visitor_id' => $newTransaction->id,
                                'salutation' => $transaction['visitors'][$x]['salutation'],
                                'first_name' => $transaction['visitors'][$x]['first_name'],
                                'middle_name' => $transaction['visitors'][$x]['middle_name'],
                                'last_name' => $transaction['visitors'][$x]['last_name'],
                                'email_address' => $transaction['visitors'][$x]['email_address'],
                                'mobile_number' => $transaction['visitors'][$x]['mobile_number'],
                                'nationality' => $transaction['visitors'][$x]['nationality'],
                                'job_title' => $transaction['visitors'][$x]['job_title'],
                                'badge_type' => $transaction['visitors'][$x]['badge_type'],
                                'pcode_used' => $transaction['visitors'][$x]['pcode_used'],
                            ]);
            
                            VisitorTransactions::create([
                                'event_id' => $this->event->id,
                                'event_category' => $this->event->category,
                                'visitor_id' => $additionalVisitor->id,
                                'visitor_type' => "sub",
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
            'message' => 'Visitor Imported Successfully!',
            'text' => ''
        ]);
    }


    public function groupVisitorsByCompany($arrayData, $rows, $i, $rateType)
    {
        if (count($arrayData) > 0) {
            $checkIfMatch = 0;
            $arrayDataIndex = 0;
            foreach ($arrayData as $index => $data) {
                if ($data['company_name'] == $rows[$i][2] && $data['alternative_company_name'] == $rows[$i][3]) {
                    $checkIfMatch++;
                    $arrayDataIndex = $index;
                    break;
                }
            }

            if ($checkIfMatch > 0) {
                $visitor = [
                    'payment_status' => $rows[$i][11],
                    'pcode_used' => $rows[$i][12] == "" ? null : $rows[$i][12],
                    'badge_type' => $rows[$i][13],
                    'salutation' => $rows[$i][14] == "" ? null : $rows[$i][14],
                    'first_name' => $rows[$i][15],
                    'middle_name' => $rows[$i][16] == "" ? null : $rows[$i][16],
                    'last_name' => $rows[$i][17],
                    'email_address' => $rows[$i][18],
                    'mobile_number' => $rows[$i][19],
                    'nationality' => $rows[$i][20],
                    'job_title' => $rows[$i][21],
                ];

                array_push($arrayData[$arrayDataIndex]['visitors'], $visitor);
            } else {
                $visitor = [
                    'payment_status' => $rows[$i][11],
                    'pcode_used' => $rows[$i][12] == "" ? null : $rows[$i][12],
                    'badge_type' => $rows[$i][13],
                    'salutation' => $rows[$i][14] == "" ? null : $rows[$i][14],
                    'first_name' => $rows[$i][15],
                    'middle_name' => $rows[$i][16] == "" ? null : $rows[$i][16],
                    'last_name' => $rows[$i][17],
                    'email_address' => $rows[$i][18],
                    'mobile_number' => $rows[$i][19],
                    'nationality' => $rows[$i][20],
                    'job_title' => $rows[$i][21],
                ];

                array_push($arrayData, [
                    'rate_type' => $rateType,
                    'pass_type' => $rows[$i][1],
                    'company_name' => $rows[$i][2],
                    'alternative_company_name' => $rows[$i][3] == "" ? null : $rows[$i][3],
                    'company_sector' => $rows[$i][4],
                    'company_address' => $rows[$i][5],
                    'company_country' => $rows[$i][6],
                    'company_city' => $rows[$i][7],
                    'company_telephone_number' => $rows[$i][8] == "" ? null : $rows[$i][8],
                    'company_mobile_number' => $rows[$i][9],
                    'assistant_email_address' => $rows[$i][10] == "" ? null : $rows[$i][10],
                    'visitors' => [
                        $visitor
                    ],
                ]);
            }
        } else {
            $visitor = [
                'payment_status' => $rows[$i][11],
                'pcode_used' => $rows[$i][12] == "" ? null : $rows[$i][12],
                'badge_type' => $rows[$i][13],
                'salutation' => $rows[$i][14] == "" ? null : $rows[$i][14],
                'first_name' => $rows[$i][15],
                'middle_name' => $rows[$i][16] == "" ? null : $rows[$i][16],
                'last_name' => $rows[$i][17],
                'email_address' => $rows[$i][18],
                'mobile_number' => $rows[$i][19],
                'nationality' => $rows[$i][20],
                'job_title' => $rows[$i][21],
            ];

            array_push($arrayData, [
                'rate_type' => $rateType,
                'pass_type' => $rows[$i][1],
                'company_name' => $rows[$i][2],
                'alternative_company_name' => $rows[$i][3] == "" ? null : $rows[$i][3],
                'company_sector' => $rows[$i][4],
                'company_address' => $rows[$i][5],
                'company_country' => $rows[$i][6],
                'company_city' => $rows[$i][7],
                'company_telephone_number' => $rows[$i][8] == "" ? null : $rows[$i][8],
                'company_mobile_number' => $rows[$i][9],
                'assistant_email_address' => $rows[$i][10] == "" ? null : $rows[$i][10],
                'visitors' => [
                    $visitor
                ],
            ]);
        }
        return $arrayData;
    }
}
