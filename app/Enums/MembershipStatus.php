<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Lapsed = 'lapsed';
    case Banned = 'banned';

    /**
     * The word shown to people on the site.
     */
    public function label(): string
    {
        return ucfirst($this->value);
    }
}
