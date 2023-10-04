<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'active',
        'description',
        'badge_type',
        'promo_code',
        'discount_type',
        'discount',
        'new_rate',
        'new_rate_description',
        'total_usage',
        'number_of_codes',
        'validity',
    ];
}
