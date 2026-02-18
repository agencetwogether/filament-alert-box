<?php

namespace Agencetwogether\AlertBox;

use Agencetwogether\AlertBox\Commands\AlertBoxCommand;
use Agencetwogether\AlertBox\Enums\AlertType;
use Agencetwogether\AlertBox\Settings\SettingAlertBox;
use Agencetwogether\AlertBox\Testing\TestsAlertBox;
use Filament\Facades\Filament;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AlertBoxServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-alert-box';

    public static string $viewNamespace = 'filament-alert-box';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('agencetwogether/filament-alert-box');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        FilamentColor::register([
            'alert-box-info' => Color::{ucwords(AlertBoxPlugin::get()->getColorInfo() ?? AlertType::INFO->getColor())},
            'alert-box-tip' => Color::{ucwords(AlertBoxPlugin::get()->getColorTip() ?? AlertType::TIP->getColor())},
            'alert-box-success' => Color::{ucwords(AlertBoxPlugin::get()->getColorSuccess() ?? AlertType::SUCCESS->getColor())},
            'alert-box-warning' => Color::{ucwords(AlertBoxPlugin::get()->getColorWarning() ?? AlertType::WARNING->getColor())},
            'alert-box-danger' => Color::{ucwords(AlertBoxPlugin::get()->getColorDanger() ?? AlertType::DANGER->getColor())},
        ]);
        $this->registerRenderHook();

        // Handle Stubs
        /*if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__.'/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-alert-box/{$file->getFilename()}"),
                ], 'filament-alert-box-stubs');
            }
        }*/

        // Testing
        Testable::mixin(new TestsAlertBox);
    }

    public function registerRenderHook(): void
    {
        if (filled($alerts = app(SettingAlertBox::class)->alerts)) {
            foreach ($alerts as $alert) {
                $data = $alert['data'];
                $type = $alert['type'];

                FilamentView::registerRenderHook(
                    name: $data['hook'],
                    // name: PanelsRenderHook::{$data['hook']},
                    hook: fn (): View => view('filament-alert-box::alert-box', ['preview' => false, 'config' => $data]),
                    scopes: AlertBox::getScopesPages($type, $data)
                );
            }
        }

    }

    protected function getAssetPackageName(): ?string
    {
        return 'agencetwogether/filament-alert-box';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-alert-box', __DIR__ . '/../resources/dist/components/filament-alert-box.js'),
            // Css::make('filament-alert-box-styles', __DIR__ . '/../resources/dist/filament-alert-box.css'),
            // Js::make('filament-alert-box-scripts', __DIR__ . '/../resources/dist/filament-alert-box.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            AlertBoxCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
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
