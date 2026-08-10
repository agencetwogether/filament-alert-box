<?php

namespace Agencetwogether\AlertBox;

use Agencetwogether\AlertBox\Filament\Pages\ManageAlertBox;
use Agencetwogether\AlertBox\Settings\SettingAlertBox;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Throwable;

class AlertBoxPlugin implements Plugin
{
    use Concerns\CanCustomizeBuilder;
    use Concerns\CanCustomizeColors;
    use Concerns\CanCustomizePage;

    public const ID = 'agencetwogether/filament-alert-box';

    public function getId(): string
    {
        return self::ID;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                ManageAlertBox::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        $this->registerPanelScopedRenderHooks($panel);
    }

    protected function registerPanelScopedRenderHooks(Panel $panel): void
    {
        $alerts = static::getAlertsForPanel($panel->getId());

        if (! filled($alerts)) {
            return;
        }

        foreach ($alerts as $alert) {
            $data = $alert['data'];
            $type = $alert['type'];

            if (AlertBox::isCustomHook($data['hook'])) {
                continue;
            }

            FilamentView::registerRenderHook(
                name: $data['hook'],
                hook: fn (): string | View => view('filament-alert-box::alert-box', ['preview' => false, 'config' => $data]),
                scopes: AlertBox::getScopesPages($type, $data)
            );
        }
    }

    public static function registerCustomHookRenderHooks(): void
    {
        $alertsList = static::getAllAlertsFlat();

        if ($alertsList->isEmpty()) {
            return;
        }

        foreach ($alertsList as $alert) {
            $data = $alert['data'] ?? null;

            if (! is_array($data) || ! AlertBox::isCustomHook($data['hook'] ?? '')) {
                continue;
            }

            FilamentView::registerRenderHook(
                name: $data['hook'],
                hook: fn (): string | View => view('filament-alert-box::alert-box', ['preview' => false, 'config' => $data]),
            );
        }
    }

    public static function getAlertsForPanel(string $panelId): array
    {
        $raw = static::getRawAlerts();

        if (empty($raw)) {
            return [];
        }

        if (AlertBox::isLegacyAlertsFormat($raw)) {
            return $raw;
        }

        return $raw[$panelId] ?? [];
    }

    protected static function getAllAlertsFlat(): Collection
    {
        $raw = static::getRawAlerts();

        if (empty($raw)) {
            return collect();
        }

        if (AlertBox::isLegacyAlertsFormat($raw)) {
            return collect($raw);
        }

        return collect($raw)->flatten(1);
    }

    protected static function getRawAlerts(): array
    {
        try {
            return app(SettingAlertBox::class)->alerts;
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function tryGet(): ?static
    {
        try {
            /** @var static $plugin */
            $plugin = filament(app(static::class)->getId());

            return $plugin;
        } catch (Throwable) {
            return null;
        }
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
