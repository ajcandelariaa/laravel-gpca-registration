<?php

namespace App\Http\Controllers;

use App\Enums\AccessTypes;
use App\Mail\RegistrationFree;
use App\Mail\RegistrationPaid;
use App\Mail\RegistrationPaymentConfirmation;
use App\Mail\RegistrationUnpaid;
use App\Models\MainDelegate;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\AdditionalDelegate;
use App\Models\PromoCode;
use App\Models\Member;
use App\Models\PrintedBadge;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OnsiteRegistrationController extends Controller
{
    use HttpResponses;

    public function fetchMetadata($api_code, $eventCategory, $eventId)
    {
        $members = $this->getMembers();
        $countries = config('app.countries');
        $companySectors = config('app.companySectors');

        return $this->success([
            'members' => $members,
            'countries' => $countries,
            'companySectors' => $companySectors,
        ], "Metadata", 200);
    }

    public function getMembers()
    {
        try {
            $members = Member::where('active', true)->orderBy('name', 'ASC')->get();

            if ($members->isEmpty()) {
                Log::info("No active members found in the database.");
                return null;
            }

            $finalMembers = [];
            foreach ($members as $member) {
                array_push($finalMembers, $member->name);
            }

            return $finalMembers;
        } catch (\Exception $e) {
            Log::error("An error occurred while getting the members list: " . $e->getMessage());
            return null;
        }
    }












    public function validateOnsiteRegistration(Request $request, $api_code, $eventCategory, $eventId)
    {
        try {
            $event = Event::where('id', $eventId)->where('category', $eventCategory)->firstOrFail();

            $validator = Validator::make($request->all(), [
                'pass_type' => 'required|string',

                'company_name' => 'required|string',
                'company_sector' => 'required|string',
                'company_address' => 'required|string',
                'company_country' => 'required|string',

                'promo_code' => 'nullable|string',

                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'job_title' => 'required|string',
                'email_address' => 'required|email',
                'contact_number' => 'required|string',
                'nationality' => 'required|string',

                'pc_name' => 'required|string',
                'pc_number' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->errorValidation($validator->errors());
            }

            $data = $validator->validated();

            //CHECK Email
            if ($this->emailExistsForEvent($event, $data['email_address'])) {
                return $this->error(
                    null,
                    'Email already registered',
                    422
                );
            }

            // CHECK PROMO CODE
            $badgeType = "Delegate";
            $discountType = null;
            $promoCodeDiscount = null;
            if ($data['promo_code'] != null) {
                $promoCodeDetails = $this->applyPromoCode($data['promo_code'], $event->id, $event->category);

                if (!$promoCodeDetails['success']) {
                    return $this->error(null, $promoCodeDetails['message'], 422);
                }

                $badgeType = $promoCodeDetails['badgeType'];
                $discountType = $promoCodeDetails['discountType'];
                $promoCodeDiscount = $promoCodeDetails['promoCodeDiscount'];
            }

            $accessType = AccessTypes::FULL_EVENT->value;

            // GET UNIT PRICE
            $passLabel = $data['pass_type'];
            $rateType = 'Standard';
            $rateTypeString = '';
            $unitPrice = 0;

            $passType = $passLabel === 'Full member'
                ? 'fullMember'
                : ($passLabel === 'Member' ? 'member' : 'nonMember');

            $accessDesc = " - Full event";
            $rateType = 'Standard';

            if ($passType === 'member') {
                $rateTypeString = "$passLabel standard rate$accessDesc";
                $unitPrice = $event->std_member_rate;
            } else {
                $rateTypeString = "$passLabel standard rate$accessDesc";
                $unitPrice = $event->std_nmember_rate;
            }

            // CALCULATE FINAL AMOUNT
            $finalAmountDetail = $this->calculateAmount(
                $data['promo_code'],
                $promoCodeDiscount,
                $discountType,
                $unitPrice,
                $event->id,
                $event->event_vat,
            );

            $paymentStatus = $finalAmountDetail['total_amount'] == 0 ? 'free' : 'unpaid';

            $pcName = $data['pc_name'] ?? null;
            $pcNumber = $data['pc_number'] ?? null;

            $finalRemarks = "Added by PC Name: " . ($pcName ?? "N/A") . "; PC Number: " . ($pcNumber ?? "N/A");

            $newDelegate = MainDelegate::create([
                'event_id' => $event->id,
                'access_type' => $accessType,
                'pass_type' => $passType,
                'rate_type' => $rateType,
                'rate_type_string' => $rateTypeString,

                'company_name' => $data['company_name'],
                'company_sector' => $data['company_sector'],
                'company_address' => $data['company_address'],
                'company_country' => $data['company_country'],
                'company_city' => "N/A",
                'company_mobile_number' => "N/A",

                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email_address' => $data['email_address'],
                'mobile_number' => $data['contact_number'],
                'nationality' => $data['nationality'],
                'job_title' => $data['job_title'],
                'badge_type' => $badgeType,
                'pcode_used' => $data['promo_code'] ?? null,
                'country' => "N/A",

                'quantity' => $finalAmountDetail['quantity'],
                'unit_price' => $finalAmountDetail['unit_price'],
                'net_amount' => $finalAmountDetail['net_amount'],
                'vat_price' => $finalAmountDetail['vat_price'],
                'discount_price' => $finalAmountDetail['discount_price'],
                'total_amount' => $finalAmountDetail['total_amount'],

                'mode_of_payment' => 'bankTransfer',
                'registration_status' => 'droppedOut',
                'payment_status' => $paymentStatus,
                'registered_date_time' => Carbon::now(),
                'paid_date_time' => null,

                'registration_method' => 'onsite',

                'transaction_remarks' => $finalRemarks,
            ]);

            Transaction::create([
                'event_id' => $event->id,
                'event_category' => $event->category,
                'delegate_id' => $newDelegate->id,
                'delegate_type' => 'main',
            ]);

            $fullName = trim($newDelegate->first_name . ' ' . $newDelegate->last_name);

            return $this->success([
                'delegate' => [
                    'id' => $newDelegate->id,
                    'full_name' => $fullName,
                    'job_title' => $newDelegate->job_title,
                    'company_name' => $newDelegate->company_name,
                    'badge_type' => $newDelegate->badge_type,
                ],
                'pricing' => [
                    'description' => $newDelegate->rate_type_string,
                    'unit_price' => $newDelegate->unit_price,
                    'discount_price' => $newDelegate->discount_price,
                    'net_amount' => $newDelegate->net_amount,
                    'total_before_vat' => $newDelegate->net_amount,
                    'vat_price' => $newDelegate->vat_price,
                    'total_amount' => $newDelegate->total_amount,
                ],
            ], 'Onsite delegate created (draft)', 200);
        } catch (\Exception $e) {
            Log::error('Error in registerOnsiteDelegate: ' . $e->getMessage());
            return $this->error($e->getMessage(), 'Internal server error', 500);
        }
    }








    public function confirmOnsiteRegistration(Request $request, $api_code, $eventCategory, $eventId)
    {
        try {
            $event = Event::where('id', $eventId)
                ->where('category', $eventCategory)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'delegate_id' => 'required|integer',
                'is_paid' => 'required|boolean',
                'send_email' => 'required|boolean',
                'print_badge' => 'required|boolean',
                'add_to_app' => 'required|boolean',
                'pc_name' => 'required|string',
                'pc_number' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->errorValidation($validator->errors());
            }

            $data = $validator->validated();

            $delegate = MainDelegate::findOrFail($data['delegate_id']);

            if ($delegate->event_id !== $event->id) {
                return $this->error(
                    null,
                    'Invalid delegate/event combination',
                    422
                );
            }

            $isFree = ((float) $delegate->total_amount) == 0.0;
            $isPaid = (bool) $data['is_paid'];

            $registrationStatus = null;
            $paymentStatus = null;
            $isConfirmed = false;
            $emailType = null;


            if (!$isFree && $isPaid) {
                // paid yes, not free = confirm paid
                $registrationStatus = 'confirmed';
                $paymentStatus = 'paid';
                $isConfirmed = true;
                $emailType = 'paid';
            } else if (!$isFree && !$isPaid) {
                // paid no, not free = pending unpaid
                $registrationStatus = 'pending';
                $paymentStatus = 'unpaid';
                $isConfirmed = false;
                $emailType = 'pending_unpaid';
            } else if ($isFree && $isPaid) {
                // paid yes, free = confirm free
                $registrationStatus = 'confirmed';
                $paymentStatus = 'free';
                $isConfirmed = true;
                $emailType = 'free_confirmed';
            } else { // $isFree && !$isPaid
                // paid no, free = pending free
                $registrationStatus = 'pending';
                $paymentStatus = 'free';
                $isConfirmed = false;
                $emailType = 'free_pending';
            }

            $now = Carbon::now();

            $delegate->update([
                'registration_status' => $registrationStatus,
                'payment_status' => $paymentStatus,
                'paid_date_time' => $isConfirmed ? $now : null,
                'confirmation_date_time' => $now,
                'confirmation_status' => 'success',
            ]);

            $fullName = trim($delegate->first_name . ' ' . $delegate->last_name);

            $transactionId = Transaction::where('event_id', $event->id)
                ->where('event_category', $event->category)
                ->where('delegate_id', $delegate->id)
                ->where('delegate_type', 'main')
                ->value('id');

            foreach (config('app.eventCategories') as $eventCategoryC => $code) {
                if ($eventCategory == $eventCategoryC) {
                    $eventCode = $code;
                }
            }

            $lastDigit = 1000 + intval($transactionId);
            $finalTransactionId = $event->year . $eventCode . $lastDigit;

            if ($data['send_email']) {
                $eventFormattedData = Carbon::parse($event->event_start_date)->format('d') . '-' . Carbon::parse($event->event_end_date)->format('d M Y');
                $invoiceLink = env('APP_URL') . '/' . $event->category . '/' . $event->id . '/view-invoice/' . $delegate->id;

                $unitPrice = $delegate->unit_price;

                $delegateVatPrice = $unitPrice * ($event->event_vat / 100);
                $amountPaid = $unitPrice + $delegateVatPrice;

                $promoCode = PromoCode::where('event_id', $event->id)->where('promo_code', $delegate->pcode_used)->first();

                if ($promoCode != null) {
                    if ($promoCode->discount_type == "percentage") {
                        $delegateDiscountPrice = $unitPrice * ($promoCode->discount / 100);
                        $delegateDiscountedPrice = $unitPrice - $delegateDiscountPrice;
                        $delegateVatPrice = $delegateDiscountedPrice * ($event->event_vat / 100);
                        $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
                    } else if ($promoCode->discount_type == "price") {
                        $delegateDiscountedPrice = $unitPrice - $promoCode->discount;
                        $delegateVatPrice = $delegateDiscountedPrice * ($event->event_vat / 100);
                        $amountPaid = $delegateDiscountedPrice + $delegateVatPrice;
                    } else {
                        $delegateVatPrice = $promoCode->new_rate * ($event->event_vat / 100);
                        $amountPaid = $promoCode->new_rate + $delegateVatPrice;
                    }
                }

                $combinedStringPrint = "gpca@reg" . ',' . $event->id . ',' . $event->category . ',' . $delegate->id . ',' . 'main';
                $finalCryptStringPrint = base64_encode($combinedStringPrint);
                $qrCodeForPrint = 'ca' . $finalCryptStringPrint . 'gp';


                $details1 = [
                    'name' => $fullName,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,
                    'eventDates' => $eventFormattedData,
                    'eventLocation' => $event->location,
                    'eventCategory' => $event->category,
                    'eventYear' => $event->year,

                    'accessType' => $delegate->access_type,
                    'jobTitle' => $delegate->job_title,
                    'companyName' => $delegate->company_name,
                    'badgeType' => $delegate->badge_type,
                    'amountPaid' => $amountPaid,
                    'transactionId' => $finalTransactionId,
                    'invoiceLink' => $invoiceLink,
                    'badgeLink' => env('APP_URL') . "/" . $event->category . "/" . $event->id . "/view-badge" . "/" . 'main' . "/" . $delegate->id,
                    'qrCodeForPrint' => $qrCodeForPrint,
                ];


                $details2 = [
                    'name' => $fullName,
                    'eventLink' => $event->link,
                    'eventName' => $event->name,
                    'eventCategory' => $event->category,
                    'eventYear' => $event->year,

                    'invoiceAmount' => $delegate->total_amount,
                    'amountPaid' => $delegate->total_amount,
                    'balance' => 0,
                    'invoiceLink' => $invoiceLink,
                ];

                $ccEmailNotif = config('app.ccEmailNotif.default');
                $sendInvoice = true;

                if ($emailType === "paid" || $emailType === "free_confirmed") {
                    try {
                        Mail::to($delegate->email_address)->cc($ccEmailNotif)->send(new RegistrationPaid($details1, $sendInvoice));

                        MainDelegate::find($delegate->id)->fill([
                            'registration_confirmation_sent_count' => 1,
                            'registration_confirmation_sent_datetime' => Carbon::now(),
                        ])->save();
                    } catch (\Exception $e) {
                        Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaid($details1, $sendInvoice));
                    }

                    try {
                        Mail::to($delegate->email_address)->cc($ccEmailNotif)->send(new RegistrationPaymentConfirmation($details2, $sendInvoice));
                    } catch (\Exception $e) {
                        Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationPaymentConfirmation($details2, $sendInvoice));
                    }
                } else if ($emailType === "pending_unpaid") {
                    try {
                        Mail::to($delegate->email_address)->cc($ccEmailNotif)->send(new RegistrationUnpaid($details1, $sendInvoice));

                        MainDelegate::find($delegate->id)->fill([
                            'registration_confirmation_sent_count' => 1,
                            'registration_confirmation_sent_datetime' => Carbon::now(),
                        ])->save();
                    } catch (\Exception $e) {
                        Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationUnpaid($details1, $sendInvoice));
                    }
                } else {
                    // free_pending
                    try {
                        Mail::to($delegate->email_address)->cc($ccEmailNotif)->send(new RegistrationFree($details1, $sendInvoice));

                        MainDelegate::find($delegate->id)->fill([
                            'registration_confirmation_sent_count' => 1,
                            'registration_confirmation_sent_datetime' => Carbon::now(),
                        ])->save();
                    } catch (\Exception $e) {
                        Mail::to(config('app.ccEmailNotif.error'))->send(new RegistrationFree($details1, $sendInvoice));
                    }
                }
            }

            $canPrintBadge = $isConfirmed && $data['print_badge'];

            if ($canPrintBadge) {
                PrintedBadge::create([
                    'event_id' => $eventId,
                    'event_category' => $eventCategory,
                    'delegate_id' => $delegate->id,
                    'delegate_type' => "main",
                    'printed_by_name' => $request->pc_name,
                    'printed_by_pc_number' => $request->pc_number,
                    'printed_date_time' => Carbon::now(),
                ]);
            }

            if ($data['add_to_app']) {
                // api to add attendee to app
            }

            $finalFrontTextBGColor = "#ffffff";
            $finalFontTextColor = "#000000";

            $delegateDetails = [
                'accessType' => "FE",
                'transactionId' => $finalTransactionId,
                'id' => $delegate->id,
                'delegateType' => "main",
                'fullName' => $fullName,
                'salutation' => (empty($delegate->salutation)) ? null : $delegate->salutation,
                'fname' => $delegate->first_name,
                'mname' => (empty($delegate->middle_name)) ? null : $delegate->middle_name,
                'lname' => $delegate->last_name,
                'jobTitle' => trim($delegate->job_title),
                'companyName' => trim($delegate->company_name),
                'badgeType' => Str::upper($delegate->badge_type),

                'frontText' => Str::upper($delegate->badge_type),
                'frontTextColor' => $finalFontTextColor,
                'frontTextBGColor' => $finalFrontTextBGColor,
                'seatNumber' => "N/A",

                'isCollected' => "No",
                'isPrinted' => $canPrintBadge ? "Yes" : "No",
                'printedCount' => $canPrintBadge ? 1 : 0,
                'paidDateTime' => $delegate->paid_date_time ?? null,
                'isSelectedForPrint' => true,
            ];

            return $this->success($delegateDetails, 'Onsite delegate confirmed', 200);
        } catch (\Exception $e) {
            Log::error('Error in confirmOnsiteDelegate: ' . $e->getMessage());
            return $this->error($e->getMessage(), $e->getMessage(), 500);
        }
    }











    // HELPER FUNCTIONS
    private function emailExistsForEvent(Event $event, string $emailAddress): bool
    {
        $transactions = Transaction::where('event_id', $event->id)
            ->where('event_category', $event->category)
            ->get();

        $countMain = 0;
        $countSub  = 0;

        foreach ($transactions as $t) {
            if ($t->delegate_type === 'main') {
                $main = MainDelegate::where('id', $t->delegate_id)
                    ->where('email_address', $emailAddress)
                    ->where('registration_status', '!=', 'droppedOut')
                    ->first();

                if ($main) {
                    $countMain++;
                }
            } else {
                $sub = AdditionalDelegate::where('id', $t->delegate_id)
                    ->where('email_address', $emailAddress)
                    ->first();

                if ($sub) {
                    $mainStatus = MainDelegate::where('id', $sub->main_delegate_id)
                        ->value('registration_status');

                    if ($mainStatus !== 'droppedOut') {
                        $countSub++;
                    }
                }
            }
        }
        return $countMain > 0 || $countSub > 0;
    }

    public function applyPromoCode($promoCode, $eventId, $eventCategory)
    {
        $badgeType = "Delegate";
        $discountType = null;
        $promoCodeDiscount = null;

        $promoCode = PromoCode::where('event_id', $eventId)->where('event_category', $eventCategory)->where('active', true)->where('promo_code', $promoCode)->first();

        if (!$promoCode) {
            return [
                'success' => false,
                'message' => 'Invalid promo code',
            ];
        }

        if ($promoCode->total_usage >= $promoCode->number_of_codes) {
            return [
                'success' => false,
                'message' => 'Promo Code has reached its capacity',
            ];
        }

        $validityDateTime = Carbon::parse($promoCode->validity);
        if (!Carbon::now()->lt($validityDateTime)) {
            return [
                'success' => false,
                'message' => 'Promo Code is expired already',
            ];
        }

        $badgeType = $promoCode->badge_type;
        $discountType = $promoCode->discount_type;

        if ($discountType === 'percentage' || $discountType === 'price') {
            $promoCodeDiscount = $promoCode->discount;
        } else {
            $promoCodeDiscount = $promoCode->new_rate;
        }

        return [
            'success' => true,
            'badgeType' => $badgeType,
            'discountType' => $discountType,
            'promoCodeDiscount' => $promoCodeDiscount,
        ];
    }

    public function calculateAmount($promoCode, $promoCodeDiscount, $discountType, $unitPrice, $eventId, $eventVat)
    {

        $finalQuantity = 1;
        $finalNetAmount = 0;
        $finalUnitPrice = 0;
        $finalDiscount = 0;

        if ($promoCodeDiscount == null) {
            $finalUnitPrice = $unitPrice;
            $finalDiscount = 0;
            $finalNetAmount = $unitPrice;
        } else {
            if ($discountType == "percentage") {
                $finalUnitPrice = $unitPrice;
                $finalDiscount = $unitPrice * ($promoCodeDiscount / 100);
                $finalNetAmount = $unitPrice - ($unitPrice * ($promoCodeDiscount / 100));
            } else if ($discountType == "price") {
                $finalUnitPrice = $unitPrice;
                $finalDiscount = $promoCodeDiscount;
                $finalNetAmount = $unitPrice - $promoCodeDiscount;
            } else {
                // FIXED RATE
                $fetchPromoCodeData = PromoCode::where('event_id', $eventId)->where('promo_code', $promoCode)->first();
                $finalUnitPrice = $fetchPromoCodeData->new_rate;
                $finalDiscount = 0;
                $finalNetAmount = $fetchPromoCodeData->new_rate;
            }
        }

        $finalVat = $finalNetAmount * ($eventVat / 100);
        $finalTotal = $finalNetAmount + $finalVat;

        return [
            'quantity' => $finalQuantity,
            'unit_price' => $finalUnitPrice,
            'discount_price' => $finalDiscount,
            'net_amount' => $finalNetAmount,
            'vat_price' => $finalVat,
            'total_amount' => $finalTotal,
        ];
    }
}
