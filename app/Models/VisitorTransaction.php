<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'visitor_id',
        'visitor_type',
    ];
}
