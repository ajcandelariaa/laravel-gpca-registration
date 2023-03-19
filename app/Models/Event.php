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
        'event_start_date',
        'event_end_date',
        'event_vat',
        'banner',
        'logo',

        'eb_end_date',
        'eb_member_rate',
        'eb_nmember_rate',

        'std_start_date',
        'std_member_rate',
        'std_nmember_rate',
        
        'year',
        'active',
    ];
}
