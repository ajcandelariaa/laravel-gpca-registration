<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpouseTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'spouse_id',
        'spouse_type',
    ];
}
