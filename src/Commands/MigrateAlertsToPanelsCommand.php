<?php

namespace Agencetwogether\AlertBox\Commands;

use Agencetwogether\AlertBox\AlertBox;
use Agencetwogether\AlertBox\Settings\SettingAlertBox;
use Filament\Facades\Filament;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;

class MigrateAlertsToPanelsCommand extends Command
{
    public $signature = 'filament-alert-box:migrate-alerts
                        {--all : Duplicate to every registered panel, skipping the interactive prompt}
                        {--dry-run : Show what would be done without writing anything to the database}';

    public $description = 'Migrate AlertBox alerts from the legacy shared format (v1.x) to the new per-panel format (v2.x).';

    public function handle(): int
    {
        /** @var SettingAlertBox $settings */
        $settings = app(SettingAlertBox::class);
        $raw = $settings->alerts;

        if (! AlertBox::isLegacyAlertsFormat($raw)) {
            $this->components->info('Nothing to migrate: the format is already up to date (or no alerts are stored).');

            return self::SUCCESS;
        }

        $count = count($raw);
        $this->components->warn("Legacy format detected: {$count} alert(s) currently shared across all panels");

        $panels = array_keys(Filament::getPanels());

        if (empty($panels)) {
            $this->components->error('No Filament panel is registered. Migration cannot proceed.');

            return self::FAILURE;
        }

        $targets = $this->option('all')
            ? $panels
            : multiselect(
                label: 'Which panels should receive a copy of the existing alerts?',
                options: $panels,
                default: $panels,
                hint: 'You can remove irrelevant alerts from each panel afterwards, on its AlertBox page.',
                required: true,
            );

        if (empty($targets)) {
            $this->components->warn('No panel selected, migration cancelled.');

            return self::SUCCESS;
        }

        $newFormat = collect($targets)
            ->mapWithKeys(fn (string $panelId): array => [$panelId => $raw])
            ->toArray();

        $this->table(
            ['Panel', 'Alerts duplicated'],
            collect($newFormat)->map(fn (array $alerts, string $panelId): array => [$panelId, count($alerts)])->toArray()
        );

        if ($this->option('dry-run')) {
            $this->components->info('Dry-run: nothing was written to the database.');

            return self::SUCCESS;
        }

        if (! $this->option('all') && ! confirm('Confirm writing to the database?', default: true)) {
            $this->components->warn('Migration cancelled.');

            return self::SUCCESS;
        }

        $settings->alerts = $newFormat;
        $settings->save();

        $this->components->info('Migration complete.');
        $this->components->info('Open the AlertBox page of each panel to remove any alert that does not belong there.');

        return self::SUCCESS;
    }
}
