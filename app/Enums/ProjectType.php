<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectType: string implements HasLabel
{
    case Game = 'game';
    case Drawing = 'drawing';
    case Writing = 'writing';

    public function getLabel(): string
    {
        return __("enums.project_type.{$this->value}");
    }
}
