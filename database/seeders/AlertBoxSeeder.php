<?php

namespace Agencetwogether\AlertBox\Database\Seeders;

use Agencetwogether\AlertBox\AlertBoxPlugin;
use Agencetwogether\AlertBox\Settings\SettingAlertBox;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AlertBoxSeeder extends Seeder
{
    public function run(): void
    {
        $panelIds = $this->resolvePanelsWithPlugin();

        if (empty($panelIds)) {
            $this->command?->warn(
                'AlertBoxPlugin is not registered on any panel. Nothing to seed. '
                . 'Make sure AlertBoxPlugin::make() is added to your PanelProvider before running this seeder.'
            );

            return;
        }

        /** @var SettingAlertBox $settings */
        $settings = app(SettingAlertBox::class);
        $allAlerts = $settings->alerts;

        foreach ($panelIds as $panelId) {
            $existing = $allAlerts[$panelId] ?? [];
            $allAlerts[$panelId] = array_merge($existing, $this->buildAlerts());

            $this->command?->info("Seeded demo alerts for panel [{$panelId}].");
        }

        $settings->alerts = $allAlerts;
        $settings->save();
    }

    /**
     * Returns the ids of every panel where AlertBoxPlugin is currently registered.
     */
    protected function resolvePanelsWithPlugin(): array
    {
        return collect(Filament::getPanels())
            ->filter(fn (Panel $panel): bool => $panel->hasPlugin(AlertBoxPlugin::ID))
            ->keys()
            ->all();
    }

    protected function buildAlerts(): array
    {
        $alerts = [
            [
                'data' => [
                    'hook' => 'panels::page.start',
                    'style' => 'info',
                    'title' => 'Need help ?',
                    'content' => '<p>You can contact us by phone/email</p>',
                    'showIcon' => true,
                ],
                'type' => 'global',
            ],
        ];

        $pageClass = $this->resolveFilamentPageClass();

        if ($pageClass !== null) {
            $alerts[] = [
                'data' => [
                    'hook' => 'panels::page.end',
                    'pages' => $pageClass,
                    'style' => 'tip',
                    'title' => 'Change your password',
                    'content' => '<p>For safety, don&#039;t forget to change your password after your first logging.</p>',
                    'showIcon' => true,
                ],
                'type' => 'page',
            ];
        }

        return $alerts;
    }

    protected function resolveFilamentPageClass(string $keyword = 'Dashboard'): ?string
    {
        $directory = app_path('Filament/Pages');

        if (! File::isDirectory($directory)) {
            return 'Filament\\Pages\\Dashboard';
        }

        $files = File::files($directory);

        if (empty($files)) {
            return 'Filament\\Pages\\Dashboard';
        }

        $preferred = collect($files)->first(
            fn ($file) => str_contains($file->getFilenameWithoutExtension(), $keyword)
        );

        if (! $preferred) {
            return 'Filament\\Pages\\Dashboard';
        }

        return $this->extractFullyQualifiedClassName($preferred->getPathname());
    }

    protected function extractFullyQualifiedClassName(string $filePath): ?string
    {
        $content = File::get($filePath);

        preg_match('/^namespace\s+(.+?);/m', $content, $nsMatch);
        preg_match('/^class\s+(\w+)/m', $content, $classMatch);

        if (empty($nsMatch[1]) || empty($classMatch[1])) {
            return null;
        }

        return $nsMatch[1] . '\\' . $classMatch[1];
    }
}
