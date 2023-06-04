<?php

namespace App\Http\Livewire;

use App\Models\Event as Events;
use Livewire\Component;
use App\Models\PromoCode as PromoCodes;
use App\Models\EventRegistrationType as EventRegistrationTypes;

class PromoCode extends Component
{
    public $eventCategory, $eventId;
    public $promoCodes;
    public $updatePromoCode = false;
    public $registrationTypes;

    public $promo_code_id;
    public $promo_code;
    public $description;
    public $badge_type;
    public $discount;
    public $number_of_codes;
    public $total_usage;
    public $validity;

    public $eventBanner;
    
    
    protected $listeners = ['updatePromoCodeConfirmed' => 'updatePromoCode', 'addPromoCodeConfirmed' => 'addPromoCode'];
    
    public function mount($eventCategory, $eventId)
    {
        $this->eventBanner = Events::where('id', $eventId)->where('category', $eventCategory)->value('banner');
        $this->eventCategory = $eventCategory;
        $this->eventId = $eventId;
        $this->registrationTypes = EventRegistrationTypes::where('event_id', $eventId)->where('event_category', $eventCategory)->where('active', true)->get();
    }

    public function render()
    {
        $this->promoCodes = PromoCodes::where('event_id', $this->eventId)->where('event_category', $this->eventCategory)->get();
        return view('livewire.admin.events.promo-codes.promo-code');
    }

    public function addPromoCodeConfirmation()
    {
        $this->validate(
            [
                'promo_code' => 'required',
                'badge_type' => 'required',
                'discount' => 'required|numeric|min:0|max:100',
                'number_of_codes' => 'required|numeric|min:1|max:10000',
                'validity' => 'required',
            ],
            [
                'promo_code.required' => 'Code is required',
                'badge_type.required' => 'Badge Type is required',

                'discount.required' => 'Discount is required',
                'discount.numeric' => 'Discount must be a number.',
                'discount.min' => 'Discount must be at least :min.',
                'discount.max' => 'Discount may not be greater than :max.',

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
            'event_id' => $this->eventId,
            'event_category' => $this->eventCategory,
            'active' => true,
            'description' => $this->description,
            'badge_type' => $this->badge_type,
            'promo_code' => $this->promo_code,
            'discount' => $this->discount,
            'total_usage' => 0,
            'number_of_codes' => $this->number_of_codes,
            'validity' => $this->validity,
        ]);

        $this->description = null;
        $this->badge_type = null;
        $this->promo_code = null;
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
        $this->promo_code = $promoCode->promo_code;
        $this->description = $promoCode->description;
        $this->badge_type = $promoCode->badge_type;
        $this->discount = $promoCode->discount;
        $this->number_of_codes = $promoCode->number_of_codes;
        $this->total_usage = $promoCode->total_usage;
        $this->validity = $promoCode->validity;
        $this->promo_code_id = $promoCode->id;
        $this->updatePromoCode = true;
    }

    public function hideEditPromoCode()
    {
        $this->updatePromoCode = false;
        $this->description = null;
        $this->badge_type = null;
        $this->promo_code = null;
        $this->discount = null;
        $this->number_of_codes = null;
        $this->total_usage = null;
        $this->validity = null;
    }

    public function updatePromoCodeConfirmation()
    {
        $this->validate(
            [
                'promo_code' => 'required',
                'badge_type' => 'required',
                'discount' => 'required|numeric|min:0|max:100',
                'number_of_codes' => 'required|numeric|min:'.$this->total_usage.'|max:10000',
                'validity' => 'required',
            ],
            [
                'promo_code.required' => 'Code is required',
                'badge_type.required' => 'Badge Type is required',

                'discount.required' => 'Discount is required',
                'discount.numeric' => 'Discount must be a number.',
                'discount.min' => 'Discount must be at least :min.',
                'discount.max' => 'Discount may not be greater than :max.',

                'number_of_codes.required' => 'Number of codes is required',
                'number_of_codes.numeric' => 'Number of codes must be a number.',
                'number_of_codes.min' => 'Number of codes must be at least :min.',
                'number_of_codes.max' => 'Number of codes may not be greater than :max.',

                'validity.required' => 'Validity is required',
            ]
        );

        $this->dispatchBrowserEvent('swal:update-promo-code-confirmation', [
            'type' => 'warning',
            'message' => 'Are you sure?',
            'text' => "",
        ]);

    }

    public function updatePromoCode(){

        PromoCodes::find($this->promo_code_id)->fill([
            'description' => $this->description,
            'badge_type' => $this->badge_type,
            'promo_code' => $this->promo_code,
            'discount' => $this->discount,
            'number_of_codes' => $this->number_of_codes,
            'validity' => $this->validity,
        ])->save();


        $this->updatePromoCode = false;
        $this->description = null;
        $this->badge_type = null;
        $this->promo_code = null;
        $this->discount = null;
        $this->number_of_codes = null;
        $this->total_usage = null;
        $this->validity = null;

        
        $this->dispatchBrowserEvent('swal:update-promo-code', [
            'type' => 'success',
            'message' => 'Promo Code Updated Successfully!',
            'text' => ''
        ]);
    }
}
