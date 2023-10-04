<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalSpouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_spouse_id',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'mobile_number',
        'nationality',
        'country',
        'city',
        'day_one',
        'day_two',
        'day_three',
        'day_four',

        'spouse_cancelled',
        'spouse_replaced',
        'spouse_refunded',
        
        'spouse_replaced_type',
        'spouse_original_from_id',
        'spouse_replaced_from_id',
        'spouse_replaced_by_id',

        'spouse_cancelled_datetime',
        'spouse_refunded_datetime',
        'spouse_replaced_datetime',
    ];
}
