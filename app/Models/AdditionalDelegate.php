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
        'job_title',
        'email_address',
        'nationality',
        'telephone_number',
        'mobile_number',
    ];
}
