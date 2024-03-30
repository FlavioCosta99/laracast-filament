<?php

namespace App\Enums;

enum ConferenceStatus: string
{
    case Draft = 'Draft';
    case Published = 'Published';
    case Archived = 'Archived';
}
