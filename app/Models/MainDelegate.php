<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainDelegate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'pass_type',
        'rate_type',
        'rate_type_string',

        'company_name',
        'company_sector',
        'company_address',
        'company_country',
        'company_city',
        'company_telephone_number',
        'company_mobile_number',
        
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'mobile_number',
        'nationality',
        'job_title',
        'badge_type',
        'pcode_used',

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
    ];
}
