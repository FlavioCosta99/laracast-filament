<?php

namespace App\Enums;

enum TalkLength: string
{
    case LIGHTNING = "Lightning - 15 Minutes";
    case NORMAL = "Normal - 30 Minutes";
    case KEYNOTE = "Keynote";

    public function getIcon()
    {
        return match ($this) {
            self::LIGHTNING => 'heroicon-o-bolt',
            self::NORMAL => 'heroicon-o-megaphone',
            self::KEYNOTE => 'heroicon-o-key',
        };
    }
}
