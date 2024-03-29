<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainVisitor extends Model
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
        'assistant_email_address',
        'alternative_company_name',

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
        'confirmation_date_time',
        'confirmation_status',

        'registration_method',
        'transaction_remarks',

        'visitor_cancelled',
        'visitor_replaced',
        'visitor_refunded',

        'visitor_replaced_by_id',
        'visitor_cancelled_datetime',
        'visitor_refunded_datetime',
        'visitor_replaced_datetime',
        
        'registration_confirmation_sent_count',
        'registration_confirmation_sent_datetime',

        'email_broadcast_sent_count',
        'email_broadcast_sent_datetime',
    ];
}
