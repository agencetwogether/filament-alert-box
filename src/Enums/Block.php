<?php

namespace Agencetwogether\AlertBox\Enums;

use Filament\Support\Contracts\HasLabel;

enum Block: string implements HasLabel
{
    case RESOURCE = 'resource';
    case PAGE = 'page';
    case GLOBAL = 'global';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RESOURCE => __('filament-alert-box::alert-box.blocks.resource'),
            self::PAGE => __('filament-alert-box::alert-box.blocks.page'),
            self::GLOBAL => __('filament-alert-box::alert-box.blocks.global'),
        };
    }
}
