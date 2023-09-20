<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use Livewire\Component;
use App\Models\PromoCode as PromoCodes;
use App\Models\EventRegistrationType as EventRegistrationTypes;

class PromoCode extends Component
{
    public $event, $promoCodes, $registrationTypes;

    // Add promo code
    public $promo_code, $description, $badge_type, $discount_type, $discount, $number_of_codes, $total_usage, $validity;

    // Edit promo code
    public $editPromoCodeId, $editPromoCode, $editDescription, $editBadgeType, $editDiscountType, $editDiscount, $editNumberOfCodes, $editTotalUsage, $editValidity;

    public $updatePromoCode = false;

    protected $listeners = ['updatePromoCodeConfirmed' => 'updatePromoCode', 'addPromoCodeConfirmed' => 'addPromoCode'];
    
    public function mount($eventCategory, $eventId)
    {
        $this->event = Events::where('id', $eventId)->where('category', $eventCategory)->first();
        $this->registrationTypes = EventRegistrationTypes::where('event_id', $eventId)->where('event_category', $eventCategory)->where('active', true)->get();
    }

    public function render()
    {
        $this->promoCodes = PromoCodes::where('event_id', $this->event->id)->where('event_category', $this->event->category)->get();
        return view('livewire.admin.events.promo-codes.promo-code');
    }

    public function addPromoCodeConfirmation()
    {
        $this->validate(
            [
                'promo_code' => 'required',
                'badge_type' => 'required',
                'discount_type' => 'required',
                'discount' => 'required|numeric|min:0',
                'number_of_codes' => 'required|numeric|min:1|max:10000',
                'validity' => 'required',
            ],
            [
                'promo_code.required' => 'Code is required',
                'badge_type.required' => 'Badge Type is required',
                'discount_type.required' => 'Discount Type is required',

                'discount.required' => 'Discount is required',
                'discount.numeric' => 'Discount must be a number.',
                'discount.min' => 'Discount must be at least :min.',

                'number_of_codes.required' => 'Number of codes is required',
                'number_of_codes.numeric' => 'Number of codes must be a number.',
                'number_of_codes.min' => 'Number of codes must be at least :min.',
                'number_of_codes.max' => 'Number of codes may not be greater than :max.',

                'validity.required' => 'Validity is required',
            ]
        );

        
        $this->dispatchBrowserEvent('swal:add-promo-code-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);
    }

    public function addPromoCode(){
        PromoCodes::create([
            'event_id' => $this->event->id,
            'event_category' => $this->event->category,
            'active' => true,
            'description' => $this->description,
            'badge_type' => $this->badge_type,
            'promo_code' => $this->promo_code,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'total_usage' => 0,
            'number_of_codes' => $this->number_of_codes,
            'validity' => $this->validity,
        ]);

        $this->description = null;
        $this->badge_type = null;
        $this->promo_code = null;
        $this->discount_type = null;
        $this->discount = null;
        $this->number_of_codes = null;
        $this->validity = null;

        $this->dispatchBrowserEvent('swal:add-promo-code', [
            'type' => 'success',
            'message' => 'Promo Code added Successfully!',
            'text' => ''
        ]);

    }

    public function updateStatus($promoCodeId, $promoCodeActive)
    {
        PromoCodes::find($promoCodeId)->fill(
            [
                'active' => !$promoCodeActive,
            ],
        )->save();
    }

    public function showEditPromoCode($promoCodeId)
    {
        $promoCode = PromoCodes::findOrFail($promoCodeId);
        $this->editPromoCodeId = $promoCode->id;
        $this->editPromoCode = $promoCode->promo_code;
        $this->editDescription = $promoCode->description;
        $this->editBadgeType = $promoCode->badge_type;
        $this->editDiscountType = $promoCode->discount_type;
        $this->editDiscount = $promoCode->discount;
        $this->editNumberOfCodes = $promoCode->number_of_codes;
        $this->editTotalUsage = $promoCode->total_usage;
        $this->editValidity = $promoCode->validity;
        $this->updatePromoCode = true;
    }

    public function hideEditPromoCode()
    {
        $this->updatePromoCode = false;

        $this->editPromoCodeId = null;
        $this->editPromoCode = null;
        $this->editDescription = null;
        $this->editBadgeType = null;
        $this->editDiscountType = null;
        $this->editDiscount = null;
        $this->editNumberOfCodes = null;
        $this->editTotalUsage = null;
        $this->editValidity = null;
    }

    public function updatePromoCodeConfirmation()
    {
        $this->validate(
            [
                'editPromoCode' => 'required',
                'editBadgeType' => 'required',
                'editDiscountType' => 'required',
                'editDiscount' => 'required|numeric|min:0',
                'editNumberOfCodes' => 'required|numeric|min:'.$this->editTotalUsage.'|max:10000',
                'editValidity' => 'required',
            ],
            [
                'editPromoCode.required' => 'Code is required',
                'editBadgeType.required' => 'Badge Type is required',
                'editDiscountType.required' => 'Discount Type is required',

                'editDiscount.required' => 'Discount is required',
                'editDiscount.numeric' => 'Discount must be a number.',
                'editDiscount.min' => 'Discount must be at least :min.',

                'editNumberOfCodes.required' => 'Number of codes is required',
                'editNumberOfCodes.numeric' => 'Number of codes must be a number.',
                'editNumberOfCodes.min' => 'Number of codes must be at least :min.',
                'editNumberOfCodes.max' => 'Number of codes may not be greater than :max.',

                'editValidity.required' => 'Validity is required',
            ]
        );

        $this->dispatchBrowserEvent('swal:update-promo-code-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);

    }

    public function updatePromoCode(){
        PromoCodes::find($this->editPromoCodeId)->fill([
            'description' => $this->editDescription,
            'badge_type' => $this->editBadgeType,
            'promo_code' => $this->editPromoCode,
            'discount_type' => $this->editDiscountType,
            'discount' => $this->editDiscount,
            'number_of_codes' => $this->editNumberOfCodes,
            'validity' => $this->editValidity,
        ])->save();

        $this->hideEditPromoCode();
        
        $this->dispatchBrowserEvent('swal:update-promo-code', [
            'type' => 'success',
            'message' => 'Promo Code Updated Successfully!',
            'text' => ''
        ]);
    }
}
