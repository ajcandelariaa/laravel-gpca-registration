<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainDelegate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'access_type',
        'pass_type',
        'rate_type',
        'rate_type_string',

        'company_name',
        'company_sector',
        'company_address',
        'company_country',
        'company_city',
        'company_telephone_number',
        'company_mobile_number',
        'assistant_email_address',
        'alternative_company_name',

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

        'heard_where',

        'attending_plenary',
        'attending_symposium',
        'attending_solxchange',
        'attending_yf',
        'attending_networking_dinner',
        'attending_welcome_dinner',
        'attending_gala_dinner',
        'attending_sustainability',

        'receive_whatsapp_notifications',

        'optional_interests',

        'seat_number',

        'quantity',
        'unit_price',
        'net_amount',
        'vat_price',
        'discount_price',
        'total_amount',
        'mode_of_payment',
        'registration_status',
        'payment_status',
        'registered_date_time',
        'paid_date_time',
        'confirmation_date_time',
        'confirmation_status',


        'pc_attending_nd',
        'scc_attending_nd',


        'registration_method',
        'transaction_remarks',

        'delegate_cancelled',
        'delegate_replaced',
        'delegate_refunded',

        'delegate_replaced_by_id',
        'delegate_cancelled_datetime',
        'delegate_refunded_datetime',
        'delegate_replaced_datetime',

        'registration_confirmation_sent_count',
        'registration_confirmation_sent_datetime',

        'email_broadcast_sent_count',
        'email_broadcast_sent_datetime',
    ];


    public function additionalDelegates()
    {
        return $this->hasMany(AdditionalDelegate::class, 'main_delegate_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'delegate_id')->where('delegate_type', 'main');
    }

    public function printedBadge()
    {
        return $this->hasOne(PrintedBadge::class, 'delegate_id')->where('delegate_type', 'main');
    }

    public function printedBadges()
    {
        return $this->hasMany(PrintedBadge::class, 'delegate_id')->where('delegate_type', 'main');
    }

    public function scannedBadge()
    {
        return $this->hasOne(ScannedDelegate::class, 'delegate_id')->where('delegate_type', 'main');
    }

    public function scannedBadges()
    {
        return $this->hasMany(ScannedDelegate::class, 'delegate_id')->where('delegate_type', 'main');
    }
}
