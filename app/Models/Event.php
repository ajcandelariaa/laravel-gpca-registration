<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'year',
        'name',
        'start_date',
        'end_date',
        'location',
        'member_eb_rate',
        'nmember_eb_rate',
        'member_std_rate',
        'description',
        'banner',
        'logo',
        'active',
    ];
}
