<?php

namespace Agencetwogether\AlertBox;

use Agencetwogether\AlertBox\Commands\InstallCommand;
use Agencetwogether\AlertBox\Commands\MigrateAlertsToPanelsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AlertBoxServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-alert-box';

    public static string $viewNamespace = 'filament-alert-box';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$viewNamespace)
            ->hasMigrations($this->getMigrations())
            ->hasCommands($this->getCommands());
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        $this->app->booted(function () {
            AlertBoxPlugin::registerCustomHookRenderHooks();
        });
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            InstallCommand::class,
            MigrateAlertsToPanelsCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_alert_box_settings',
        ];
    }
}
