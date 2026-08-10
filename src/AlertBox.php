<?php

namespace Agencetwogether\AlertBox;

use Agencetwogether\AlertBox\Enums\AlertType;
use Agencetwogether\AlertBox\Enums\Block;
use Agencetwogether\AlertBox\Support\HookGlobal;
use Agencetwogether\AlertBox\Support\HookPage;
use Agencetwogether\AlertBox\Support\HookResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\PageRegistration;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

use function Filament\Support\get_color_css_variables;

class AlertBox
{
    private static array $styleOptionsCache = [];

    public static function getToolbarButtons(): array
    {
        return config('filament-alert-box.toolbar_buttons');
    }

    public static function getStyleOptions(): array
    {
        $panelId = Filament::getCurrentPanel()?->getId() ?? 'default';

        if (isset(self::$styleOptionsCache[$panelId])) {
            return self::$styleOptionsCache[$panelId];
        }

        self::$styleOptionsCache[$panelId] = Arr::mapWithKeys(
            AlertType::cases(),
            function (AlertType $type) {
                return [
                    $type->value => view('filament-alert-box::option-style')
                        ->with('type', $type)
                        ->with('color', self::getColor($type->value) ?? $type->getColor())
                        ->render(),
                ];
            }
        );

        return self::$styleOptionsCache[$panelId];
    }

    public static function resolveColorStyles(?string $color, array $shades = [100, 400, 500, 600, 950]): string
    {
        if ($color === null) {
            return '';
        }

        $registeredColor = FilamentColor::getColors()[$color] ?? null;

        if (! is_array($registeredColor)) {
            $constant = ucfirst($color);
            if (defined(Color::class . '::' . $constant)) {
                $registeredColor = constant(Color::class . '::' . $constant);
            }
        }

        if (! is_array($registeredColor)) {
            return '';
        }

        return Arr::toCssStyles([
            get_color_css_variables($registeredColor, shades: $shades) => true,
        ]);
    }

    public static function getColor(string $type): ?string
    {
        try {
            $plugin = AlertBoxPlugin::get();
        } catch (Throwable) {
            return null;
        }

        return match ($type) {
            'info' => $plugin->getColorInfo(),
            'tip' => $plugin->getColorTip(),
            'success' => $plugin->getColorSuccess(),
            'warning' => $plugin->getColorWarning(),
            'danger' => $plugin->getColorDanger(),
            'none' => null,
            default => null
        };
    }

    public static function getBlockLabel(string $type, ?array $state): string
    {
        if ($state === null) {
            return Block::tryFrom($type)?->getLabel() ?? __('filament-alert-box::alert-box.blocks.default');
        }

        $label = __('filament-alert-box::alert-box.builder_block_label.alert', ['style' => AlertType::tryFrom($state['style'])?->getLabel() ?? __('filament-alert-box::alert-box.styles.none')]);

        $label .= match ($type) {
            Block::RESOURCE->value => self::getBlockLabelResource($state),
            Block::PAGE->value => self::getBlockLabelPage($state),
            Block::GLOBAL->value => self::getBlockLabelGlobal($state),
            default => '',
        };

        return Str::squish($label);
    }

    private static function getBlockLabelResource(?array $state): string
    {
        $label = '';

        if ($state['hook']) {
            $label .= Str::wrap(__('filament-alert-box::alert-box.builder_block_label.on_hook', ['hook' => self::cleanLabel($state['hook'])]), ' ');
        }
        if ($state['resources']) {
            $resource = Str::afterLast($state['resources'], '\\');

            $label .= Str::wrap(__('filament-alert-box::alert-box.builder_block_label.for_resource', ['resource' => $resource]), ' ');
        }
        if ($state['pages']) {
            $scopes = implode(', ', Arr::map($state['pages'], fn (string $value) => Str::afterLast($value, '\\')));

            $label .= Str::wrap(__('filament-alert-box::alert-box.builder_block_label.for_pages', ['scopes' => $scopes]), ' ');

        }

        return $label;
    }

    private static function getBlockLabelPage(?array $state): string
    {
        $label = '';

        if ($state['hook']) {
            $label .= Str::wrap(__('filament-alert-box::alert-box.builder_block_label.on_hook', ['hook' => self::cleanLabel($state['hook'])]), ' ');
        }
        if ($state['pages']) {
            $page = Str::afterLast($state['pages'], '\\');

            $label .= Str::wrap(__('filament-alert-box::alert-box.builder_block_label.for_page', ['page' => $page]), ' ');
        }

        return $label;
    }

    private static function getBlockLabelGlobal(?array $state): string
    {

        $label = '';

        if ($state['hook']) {
            $label .= Str::wrap(__('filament-alert-box::alert-box.builder_block_label.on_hook', ['hook' => self::cleanLabel($state['hook'])]), ' ');
        }

        return $label;
    }

    public static function cleanLabel(string $label): string
    {
        $label = Str::afterLast($label, '::');

        return Str::upper(str_replace('.', '_', $label));
    }

    public static function getResources(): array
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel) {
            return [];
        }

        return collect($panel->getResources())
            ->mapWithKeys(function (string $resource): array {
                try {
                    $label = $resource::getNavigationLabel();
                } catch (Throwable) {
                    $label = Str::afterLast($resource, '\\');
                }

                return [$resource => $label . ' (' . Str::afterLast($resource, '\\') . ')'];
            })
            ->sortKeys()
            ->toArray();
    }

    public static function getPages(): array
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel) {
            return [];
        }

        return collect($panel->getPages())
            ->mapWithKeys(function (string $page): array {
                try {
                    $label = $page::getNavigationLabel();
                } catch (Throwable) {
                    $label = Str::afterLast($page, '\\');
                }

                return [$page => $label . ' (' . Str::afterLast($page, '\\') . ')'];
            })
            ->sortKeys()
            ->toArray();
    }

    public static function getResourcePages(?string $resource): array
    {
        if (blank($resource)) {
            return [];
        }

        return Arr::mapWithKeys($resource::getPages(), function (PageRegistration $item) {
            return [$item->getPage() => Str::ucwords($item->getPage()::getResourcePageName()) . ' (' . Str::afterLast($item->getPage(), '\\') . ')'];
        });
    }

    public static function getHooks(string $type): array
    {
        return match ($type) {
            Block::RESOURCE->value => HookResource::getHooks(),
            Block::PAGE->value => HookPage::getHooks(),
            Block::GLOBAL->value => HookGlobal::getHooks(),
            default => [],
        };
    }

    public static function getScopesPages(string $type, array $data): ?array
    {
        if ($type === Block::GLOBAL->value) {
            return null;
        }

        $pages = data_get($data, 'pages');
        if (empty($pages) && data_get($data, 'resources')) {
            return Arr::wrap($data['resources']);
        }

        return Arr::wrap($pages);

    }

    public static function isCustomHook(string $hook): bool
    {
        return in_array($hook, config('filament-alert-box.custom_hooks', []), true);
    }

    public static function isLegacyAlertsFormat(array $alerts): bool
    {
        if (empty($alerts)) {
            return false;
        }

        $first = reset($alerts);

        return is_array($first) && array_key_exists('type', $first) && array_key_exists('data', $first);
    }
}
