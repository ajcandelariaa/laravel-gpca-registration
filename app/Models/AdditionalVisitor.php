<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_visitor_id',

        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'mobile_number',
        'nationality',
        'country',
        'city',
        'company_name',
        'job_title',

        'visitor_cancelled',
        'visitor_replaced',
        'visitor_refunded',
        
        'visitor_replaced_type',
        'visitor_original_from_id',
        'visitor_replaced_from_id',
        'visitor_replaced_by_id',

        'visitor_cancelled_datetime',
        'visitor_refunded_datetime',
        'visitor_replaced_datetime',
    ];
}
