<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCodeAddtionalBadgeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'promo_code_id',
        'badge_type',
    ];
}
