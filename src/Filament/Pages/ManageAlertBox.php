<?php

namespace Agencetwogether\AlertBox\Filament\Pages;

use Agencetwogether\AlertBox\AlertBox;
use Agencetwogether\AlertBox\AlertBoxPlugin;
use Agencetwogether\AlertBox\Concerns\HasPageShieldSupport;
use Agencetwogether\AlertBox\Enums\Block as BlockEnum;
use Agencetwogether\AlertBox\Settings\SettingAlertBox;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Panel;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ManageAlertBox extends SettingsPage
{
    use HasPageShieldSupport;

    protected static string $settings = SettingAlertBox::class;

    public function getTitle(): string
    {
        return AlertBoxPlugin::tryGet()?->getTitle()
            ?? __('filament-alert-box::alert-box.page.title');
    }

    public static function getNavigationLabel(): string
    {
        return AlertBoxPlugin::tryGet()?->getNavigationLabel()
            ?? __('filament-alert-box::alert-box.page.navigation_label');
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return AlertBoxPlugin::tryGet()?->getNavigationIcon();
    }

    public static function getNavigationGroup(): ?string
    {
        return AlertBoxPlugin::tryGet()?->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return AlertBoxPlugin::tryGet()?->getNavigationSort();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return config('filament-alert-box.page.slug', 'alert-box');
    }

    public static function getCluster(): ?string
    {
        return config('filament-alert-box.page.cluster');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $raw = $data['alerts'] ?? [];

        $data['alerts'] = AlertBox::isLegacyAlertsFormat($raw)
            ? $raw
            : collect($raw)->get($this->getCurrentPanelId(), []);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $raw = app(static::$settings)->alerts;

        if (AlertBox::isLegacyAlertsFormat($raw)) {
            $allAlerts = collect(Filament::getPanels())
                ->keys()
                ->mapWithKeys(fn (string $panelId): array => [$panelId => $raw])
                ->toArray();
        } else {
            $allAlerts = $raw;
        }

        $allAlerts[$this->getCurrentPanelId()] = $data['alerts'] ?? [];

        $data['alerts'] = $allAlerts;

        return $data;
    }

    protected function getCurrentPanelId(): string
    {
        return Filament::getCurrentPanel()?->getId() ?? 'default';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Builder::make('alerts')
                    ->blocks([
                        Block::make(BlockEnum::RESOURCE->value)
                            ->label(fn (Block $component, ?array $state): string => AlertBox::getBlockLabel($component->getName(), $state))
                            ->icon(AlertBoxPlugin::tryGet()?->getIconResource())
                            ->schema([
                                Select::make('resources')
                                    ->label(__('filament-alert-box::alert-box.form.resource.resources'))
                                    ->placeholder(__('filament-alert-box::alert-box.placeholder.resource.resources'))
                                    ->formatStateUsing(fn ($state) => blank($state) ? null : $state)
                                    ->live()
                                    ->options(fn () => AlertBox::getResources())
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('pages', null);
                                        $set('mustBeScoped', false);
                                    })
                                    ->native(false)
                                    ->required(),

                                Toggle::make('mustBeScoped')
                                    ->label(__('filament-alert-box::alert-box.form.resource.must-be-scoped'))
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('pages', null);
                                    })
                                    ->default(false),

                                Select::make('pages')
                                    ->label(__('filament-alert-box::alert-box.form.resource.pages'))
                                    ->placeholder(__('filament-alert-box::alert-box.placeholder.resource.pages'))
                                    ->multiple()
                                    ->options(fn (Get $get) => AlertBox::getResourcePages($get('resources')))
                                    ->visibleJs(<<<'JS'
                                        $get('mustBeScoped')
                                    JS)
                                    ->requiredIf('mustBeScoped', true),
                                $this->commonFields(),
                            ]),
                        Block::make(BlockEnum::PAGE->value)
                            ->label(fn (Block $component, ?array $state): string => AlertBox::getBlockLabel($component->getName(), $state))
                            ->icon(AlertBoxPlugin::tryGet()?->getIconPage())
                            ->schema([
                                Select::make('pages')
                                    ->label(__('filament-alert-box::alert-box.form.page.pages'))
                                    ->placeholder(__('filament-alert-box::alert-box.placeholder.page.pages'))
                                    ->formatStateUsing(fn ($state) => blank($state) ? null : $state)
                                    ->live()
                                    ->options(fn () => AlertBox::getPages())
                                    ->native(false)
                                    ->required(),
                                $this->commonFields(),
                            ]),
                        Block::make(BlockEnum::GLOBAL->value)
                            ->label(fn (Block $component, ?array $state): string => AlertBox::getBlockLabel($component->getName(), $state))
                            ->icon(AlertBoxPlugin::tryGet()?->getIconGlobal())
                            ->schema([
                                $this->commonFields(),
                            ]),
                    ])
                    ->hiddenLabel()
                    ->addActionLabel(__('filament-alert-box::alert-box.form.add'))
                    ->addActionAlignment(AlertBoxPlugin::tryGet()?->getAddActionAlignment())
                    ->addBetweenAction(fn (Action $action) => $action->hidden())
                    ->blockIcons()
                    ->columnSpanFull()
                    ->collapsible(AlertBoxPlugin::tryGet()?->getBlocksAreCollapsible() ?? false)
                    ->collapsed(AlertBoxPlugin::tryGet()?->getBlocksAreCollapsed() ?? false)
                    ->blockNumbers(false)
                    ->deleteAction(fn (Action $action) => $action->requiresConfirmation()),
            ]);
    }

    public function commonFields(): Group
    {
        return Group::make()->schema([
            Select::make('hook')
                ->label(__('filament-alert-box::alert-box.form.common.hook'))
                ->placeholder(__('filament-alert-box::alert-box.placeholder.common.hook'))
                ->options(function (Get $get): array {
                    $type = $get('../type');
                    if (blank($type)) {
                        return [];
                    }

                    return AlertBox::getHooks($type);
                })
                ->native(false)
                ->live(onBlur: true)
                ->required(),

            Select::make('style')
                ->label(__('filament-alert-box::alert-box.form.common.style'))
                ->live()
                ->default('info')
                ->formatStateUsing(fn ($state) => blank($state) ? 'info' : $state)
                ->options(AlertBox::getStyleOptions())
                ->native(false)
                ->allowHtml(),

            Toggle::make('showIcon')
                ->label(__('filament-alert-box::alert-box.form.common.show-icon'))
                ->live()
                ->default(true),

            TextInput::make('title')
                ->label(__('filament-alert-box::alert-box.form.common.title'))
                ->placeholder(__('filament-alert-box::alert-box.placeholder.common.title'))
                ->formatStateUsing(fn ($state) => blank($state) ? __('filament-alert-box::alert-box.placeholder.common.title') : $state)
                ->live(),

            RichEditor::make('content')
                ->label(__('filament-alert-box::alert-box.form.common.content'))
                ->formatStateUsing(fn ($state) => blank($state) || $state === '<p></p>' ? __('filament-alert-box::alert-box.placeholder.common.content') : $state)
                ->toolbarButtons(AlertBox::getToolbarButtons())
                ->live(onBlur: true)
                ->required(),

            View::make('filament-alert-box::alert-box')
                ->viewData(['preview' => true]),
        ]);
    }

    public function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label(__('filament-alert-box::alert-box.form.save'));
    }

    public function getSavedNotificationTitle(): ?string
    {
        return __('filament-alert-box::alert-box.page.notification_success');
    }
}
