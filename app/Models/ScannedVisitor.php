<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScannedVisitor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'visitor_id',
        'visitor_type',
        'scanned_date_time',
    ];
}
