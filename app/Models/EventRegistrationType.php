<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistrationType extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'registration_type',
        'badge_footer_front_name',
        'badge_footer_front_bg_color',
        'badge_footer_front_text_color',
        'badge_footer_back_name',
        'badge_footer_back_bg_color',
        'badge_footer_back_text_color',
        'active'
    ];
}
