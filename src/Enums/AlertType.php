<?php

namespace Agencetwogether\AlertBox\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AlertType: string implements HasColor, HasIcon, HasLabel
{
    case INFO = 'info';
    case TIP = 'tip';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case DANGER = 'danger';
    case NONE = 'none';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INFO => __('filament-alert-box::alert-box.styles.info'),
            self::TIP => __('filament-alert-box::alert-box.styles.tip'),
            self::SUCCESS => __('filament-alert-box::alert-box.styles.success'),
            self::WARNING => __('filament-alert-box::alert-box.styles.warning'),
            self::DANGER => __('filament-alert-box::alert-box.styles.danger'),
            self::NONE => __('filament-alert-box::alert-box.styles.none'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::INFO => config('filament-alert-box.default_colors.info'),
            self::TIP => config('filament-alert-box.default_colors.tip'),
            self::SUCCESS => config('filament-alert-box.default_colors.success'),
            self::WARNING => config('filament-alert-box.default_colors.warning'),
            self::DANGER => config('filament-alert-box.default_colors.danger'),
            self::NONE => config('filament-alert-box.default_colors.none'),
        };
    }

    public function getIcon(): BackedEnum|string|null
    {
        return match ($this) {
            self::INFO => config('filament-alert-box.default_icons.info'),
            self::TIP => config('filament-alert-box.default_icons.tip'),
            self::SUCCESS => config('filament-alert-box.default_icons.success'),
            self::WARNING => config('filament-alert-box.default_icons.warning'),
            self::DANGER => config('filament-alert-box.default_icons.danger'),
            self::NONE => config('filament-alert-box.default_icons.none'),
        };
    }
}
