<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RccAwardsMainParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'pass_type',
        'rate_type',
        'rate_type_string',

        'category',
        'sub_category',
        'company_name',

        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'mobile_number',
        'address',
        'country',
        'city',
        'job_title',

        'heard_where',

        'quantity',
        'unit_price',
        'net_amount',
        'vat_price',
        'discount_price',
        'total_amount',
        
        'mode_of_payment',
        'registration_status',
        'payment_status',
        'registered_date_time',
        'paid_date_time',
        'confirmation_date_time',
        'confirmation_status',

        'registration_method',
        'transaction_remarks',

        'participant_cancelled',
        'participant_replaced',
        'participant_refunded',
        
        'participant_replaced_by_id',
        'participant_cancelled_datetime',
        'participant_refunded_datetime',
        'participant_replaced_datetime',
    ];
}
