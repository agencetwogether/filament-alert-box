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

class AlertBox
{
    public static function getToolbarButtons(): array
    {
        return config('filament-alert-box.toolbar_buttons');
    }

    public static function getStyleOptions(): array
    {
        $types = AlertType::cases();

        return once(fn () => Arr::mapWithKeys($types, function (AlertType $type) {

            return [
                $type->value => view('filament-alert-box::option-style')
                    ->with('type', $type)
                    ->with('color', self::getColor($type->value) ?? $type->getColor())
                    ->render(),
            ];
        }));
    }

    public static function getColor(string $type): ?string
    {
        return match ($type) {
            'info' => AlertBoxPlugin::get()->getColorInfo(),
            'tip' => AlertBoxPlugin::get()->getColorTip(),
            'success' => AlertBoxPlugin::get()->getColorSuccess(),
            'warning' => AlertBoxPlugin::get()->getColorWarning(),
            'danger' => AlertBoxPlugin::get()->getColorDanger(),
            'none' => null,
        };
    }

    public static function getBlockLabel(string $type, ?array $state): string
    {
        if ($state === null) {
            return Block::tryFrom($type)->getLabel();
        }

        $label = __('filament-alert-box::alert-box.builder_block_label.alert', ['style' => AlertType::tryFrom($state['style'])->getLabel()]);

        $label .= match ($type) {
            Block::RESOURCE->value => self::getBlockLabelResource($state),
            Block::PAGE->value => self::getBlockLabelPage($state),
            Block::GLOBAL->value => self::getBlockLabelGlobal($state),
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

    public static function getResources()
    {
        return collect(Filament::getCurrentPanel()->getResources())
            ->mapWithKeys(fn (string $resource): array => [
                $resource => $resource::getNavigationLabel().' ('.Str::afterLast($resource, '\\').')',
            ])
            ->sortKeys()
            ->toArray();
    }

    public static function getPages()
    {
        return collect(Filament::getCurrentPanel()->getPages())
            ->mapWithKeys(fn (string $page): array => [
                $page => $page::getNavigationLabel().' ('.Str::afterLast($page, '\\').')',
            ])
            ->sortKeys()
            ->toArray();
    }

    public static function getResourcePages(?string $resource): array
    {
        if (blank($resource)) {
            return [];
        }

        return Arr::mapWithKeys($resource::getPages(), function (PageRegistration $item) {
            return [$item->getPage() => Str::ucwords($item->getPage()::getResourcePageName()).' ('.Str::afterLast($item->getPage(), '\\').')'];
        });
    }

    public static function getHooks(string $type): array
    {
        return match ($type) {
            Block::RESOURCE->value => HookResource::getHooks(),
            Block::PAGE->value => HookPage::getHooks(),
            Block::GLOBAL->value => HookGlobal::getHooks(),
        };
    }

    public static function getScopesPages(string $type, array $data): ?array
    {
        $pages = data_get($data, 'pages');
        if (empty($pages) && data_get($data, 'resources')) {
            return Arr::wrap($data['resources']);
        }
        if ($type === Block::GLOBAL->value) {
            return null;
        }

        return Arr::wrap($pages);

    }

    public static function getFilamentColors(): array
    {
        // $filamentColors = array_keys(FilamentColor::getColors());
        $allColorsAvailable = array_keys(Color::all());

        return $allColorsAvailable;

        return array_merge($filamentColors, $allColorsAvailable);
    }
}
