<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScannedDelegate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'delegate_id',
        'delegate_type',
        'scanner_location',
        'scanned_date_time',
    ];
}
