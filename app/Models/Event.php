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
        'link',
        'event_start_date',
        'event_end_date',
        'event_vat',
        'banner',
        'logo',

        'eb_end_date',
        'eb_full_member_rate',
        'eb_member_rate',
        'eb_nmember_rate',

        'std_start_date',
        'std_full_member_rate',
        'std_member_rate',
        'std_nmember_rate',
        
        'badge_footer_link',
        'badge_footer_link_color',
        'badge_footer_bg_color',
        'badge_front_banner',
        'badge_back_banner',

        'year',
        'active',
    ];
}
