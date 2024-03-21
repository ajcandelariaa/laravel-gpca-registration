<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwardsParticipantDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_category',
        'participant_id',
        'document',
        'document_file_name',
        'document_type',
    ];
}
