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
        'company_name',
        'company_sector',
        'company_address',
        'company_country',
        'company_city',
        'company_telephone_number',
        'company_mobile_number',
        'pcode_used',
        'heard_where',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'job_title',
        'email_address',
        'nationality',
        'mobile_number',
        'quantity',
        'unit_price',
        'net_amount',
        'vat_price',
        'discount_price',
        'total_amount',
        'mode_of_payment',
        'status',
        'registered_date',
        'paid_date',
    ];
}
