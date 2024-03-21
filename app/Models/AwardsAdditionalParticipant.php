<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardsAdditionalParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_participant_id',

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
        'nationality',

        'registration_method',
        'transaction_remarks',

        'participant_cancelled',
        'participant_replaced',
        'participant_refunded',
        
        'participant_replaced_type',
        'participant_original_from_id',
        'participant_replaced_from_id',
        'participant_replaced_by_id',

        'participant_cancelled_datetime',
        'participant_refunded_datetime',
        'participant_replaced_datetime',
    ];
}
