<?php

namespace Agencetwogether\AlertBox\Filament\Pages;

use Agencetwogether\AlertBox\AlertBox;
use Agencetwogether\AlertBox\AlertBoxPlugin;
use Agencetwogether\AlertBox\Enums\Block as BlockEnum;
use Agencetwogether\AlertBox\Settings\SettingAlertBox;
use BackedEnum;
use Filament\Actions\Action;
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
    protected static string $settings = SettingAlertBox::class;

    public function getTitle(): string
    {
        return AlertBoxPlugin::get()->getTitle();
    }

    public static function getNavigationLabel(): string
    {
        return AlertBoxPlugin::get()->getNavigationLabel();
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return AlertBoxPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationGroup(): ?string
    {
        return AlertBoxPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return AlertBoxPlugin::get()->getNavigationSort();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return config('filament-alert-box.page.slug', 'alert-box');
    }

    public static function getCluster(): ?string
    {
        return config('filament-alert-box.page.cluster');
    }

    public static function canAccess(): bool
    {
        return AlertBoxPlugin::get()->isAuthorized();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Builder::make('alerts')
                    ->blocks([
                        Block::make(BlockEnum::RESOURCE->value)
                            ->label(fn (Block $component, ?array $state): string => AlertBox::getBlockLabel($component->getName(), $state))
                            ->icon(AlertBoxPlugin::get()->getIconResource())
                            ->schema([
                                Select::make('resources')
                                    ->label(__('filament-alert-box::alert-box.form.resource.resources'))
                                    ->placeholder(__('filament-alert-box::alert-box.placeholder.resource.resources'))
                                    ->formatStateUsing(fn ($state) => empty($state) ? null : $state)
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
                                    ->native(false)
                                    ->searchable(false)
                                    ->visibleJs(<<<'JS'
                                        $get('mustBeScoped')
                                    JS)
                                    ->requiredIf('mustBeScoped', true),
                                $this->commonFields(),
                            ]),
                        Block::make(BlockEnum::PAGE->value)
                            ->label(fn (Block $component, ?array $state): string => AlertBox::getBlockLabel($component->getName(), $state))
                            ->icon(AlertBoxPlugin::get()->getIconPage())
                            ->schema([
                                Select::make('pages')
                                    ->label(__('filament-alert-box::alert-box.form.page.pages'))
                                    ->placeholder(__('filament-alert-box::alert-box.placeholder.page.pages'))
                                    ->formatStateUsing(fn ($state) => empty($state) ? null : $state)
                                    ->live()
                                    ->options(fn () => AlertBox::getPages())
                                    ->native(false)
                                    ->required(),
                                $this->commonFields(),
                            ]),
                        Block::make(BlockEnum::GLOBAL->value)
                            ->label(fn (Block $component, ?array $state): string => AlertBox::getBlockLabel($component->getName(), $state))
                            ->icon(AlertBoxPlugin::get()->getIconGlobal())
                            ->schema([
                                $this->commonFields(),
                            ]),
                    ])
                    ->hiddenLabel()
                    ->addActionLabel(__('filament-alert-box::alert-box.form.add'))
                    ->addActionAlignment(AlertBoxPlugin::get()->getAddActionAlignment())
                    ->addBetweenAction(fn (Action $action) => $action->hidden())
                    ->blockIcons()
                    ->columnSpanFull()
                    ->collapsible(AlertBoxPlugin::get()->getBlocksAreCollapsible())
                    ->collapsed(AlertBoxPlugin::get()->getBlocksAreCollapsed())
                    ->blockNumbers(false),
            ]);
    }

    public function commonFields(): Group
    {
        return Group::make()->schema([
            Select::make('hook')
                ->label(__('filament-alert-box::alert-box.form.common.hook'))
                ->placeholder(__('filament-alert-box::alert-box.placeholder.common.hook'))
                ->options(function (Get $get): array {
                    return AlertBox::getHooks($get('../type'));
                })
                ->native(false)
                ->live(onBlur: true)
                ->required(),

            Select::make('style')
                ->label(__('filament-alert-box::alert-box.form.common.style'))
                ->live()
                ->default('info')
                ->formatStateUsing(fn ($state) => empty($state) ? 'info' : $state)
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
                ->formatStateUsing(fn ($state) => empty($state) ? __('filament-alert-box::alert-box.placeholder.common.title') : $state)
                ->live(),

            RichEditor::make('content')
                ->label(__('filament-alert-box::alert-box.form.common.content'))
                ->formatStateUsing(fn ($state) => empty($state) || $state === '<p></p>' ? __('filament-alert-box::alert-box.placeholder.common.content') : $state)
                ->toolbarButtons(AlertBox::getToolbarButtons())
                ->live()
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
