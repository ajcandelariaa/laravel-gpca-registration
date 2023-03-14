<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category',
        'name',
        'location',
        'description',
        'start_date',
        'end_date',
        'banner',
        'logo',

        'eb_end_date',
        'member_eb_rate',
        'nmember_eb_rate',

        'std_start_date',
        'member_std_rate',
        'nmember_std_rate',
        
        'year',
        'active',
    ];
}
