<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalDelegate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'main_delegate_id',
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

        'delegate_cancelled',
        'delegate_replaced',
        'delegate_refunded',

        'delegate_replaced_type',
        'delegate_original_from_id',
        'delegate_replaced_from_id',
        'delegate_replaced_by_id',

        'delegate_cancelled_datetime',
        'delegate_refunded_datetime',
        'delegate_replaced_datetime',
    ];
}
