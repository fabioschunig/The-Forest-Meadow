<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasColor, HasLabel
{
    case Idea = 'idea';
    case Prototype = 'prototype';
    case InDevelopment = 'in_development';
    case Released = 'released';
    case Paused = 'paused';

    public function getLabel(): string
    {
        return __("enums.project_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Idea => 'gray',
            self::Prototype => 'info',
            self::InDevelopment => 'warning',
            self::Released => 'success',
            self::Paused => 'danger',
        };
    }
}
