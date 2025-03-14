<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalDelegate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'main_delegate_id',
        'salutation',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'mobile_number',
        'nationality',
        'job_title',
        'badge_type',
        'pcode_used',
        'country',

        'interests',

        'seat_number',

        'delegate_cancelled',
        'delegate_replaced',
        'delegate_refunded',

        'delegate_replaced_type',
        'delegate_original_from_id',
        'delegate_replaced_from_id',
        'delegate_replaced_by_id',

        'delegate_cancelled_datetime',
        'delegate_refunded_datetime',
        'delegate_replaced_datetime',

        'registration_confirmation_sent_count',
        'registration_confirmation_sent_datetime',

        'email_broadcast_sent_count',
        'email_broadcast_sent_datetime',
    ];

    protected $casts = [
        'interests' => 'array',
    ];

    public function mainDelegate()
    {
        return $this->belongsTo(MainDelegate::class, 'main_delegate_id');
    }
    
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'delegate_id')->where('delegate_type', 'sub');
    }

    public function printedBadge()
    {
        return $this->hasOne(PrintedBadge::class, 'delegate_id')->where('delegate_type', 'sub');
    }

    public function printedBadges()
    {
        return $this->hasMany(PrintedBadge::class, 'delegate_id')->where('delegate_type', 'sub');
    }

    public function scannedBadge()
    {
        return $this->hasOne(ScannedDelegate::class, 'delegate_id')->where('delegate_type', 'sub');
    }

    public function scannedBadges()
    {
        return $this->hasMany(ScannedDelegate::class, 'delegate_id')->where('delegate_type', 'sub');
    }
}
