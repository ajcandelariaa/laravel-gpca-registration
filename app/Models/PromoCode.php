<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'active',
        'description',
        'badge_type',
        'promo_code',
        'discount',
        'remaining_codes',
        'number_of_codes',
        'validity',
    ];
}
