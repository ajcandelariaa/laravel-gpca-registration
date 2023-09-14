<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintedBadge extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'delegate_id',
        'delegate_type',
        'printed_date_time',
    ];
}
