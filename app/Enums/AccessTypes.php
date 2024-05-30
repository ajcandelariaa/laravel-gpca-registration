<?php

namespace App\Enums;

enum AccessTypes: string
{
    case FULL_EVENT = 'fullEvent';
    case CONFERENCE_ONLY = 'conferenceOnly';
    case WORKSHOP_ONLY = 'workshopOnly';
}