<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelegateDetailsUpdateLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'event_id',
        'event_category',
        'delegate_id',
        'delegate_type',
        'updated_by_name',
        'updated_by_pc_number',
        'description',
        'updated_date_time',
    ];
}
