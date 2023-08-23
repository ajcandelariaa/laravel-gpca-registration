<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainSpouse extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'pass_type',
        'rate_type',
        'rate_type_string',

        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'mobile_number',
        'nationality',
        'country',
        'city',

        'reference_delegate_name',

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

        'spouse_cancelled',
        'spouse_replaced',
        'spouse_refunded',

        'spouse_replaced_by_id',
        'spouse_cancelled_datetime',
        'spouse_refunded_datetime',
        'spouse_replaced_datetime',
        
        'registration_confirmation_sent_count',
        'registration_confirmation_sent_datetime',
    ];
}
