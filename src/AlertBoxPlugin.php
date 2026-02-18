<?php

namespace Agencetwogether\AlertBox;

use Agencetwogether\AlertBox\Filament\Pages\ManageAlertBox;
use Filament\Contracts\Plugin;
use Filament\Panel;

class AlertBoxPlugin implements Plugin
{
    use Concerns\CanCustomizeBuilder;
    use Concerns\CanCustomizeColors;
    use Concerns\CanCustomizePage;

    public function getId(): string
    {
        return 'agencetwogether/filament-alert-box';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                ManageAlertBox::class,
            ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
