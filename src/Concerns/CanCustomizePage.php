<?php

namespace Agencetwogether\AlertBox\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Icons\Heroicon;

trait CanCustomizePage
{
    protected null|Closure|string $title = null;

    protected string|Closure|null $navigationLabel = null;

    protected string|Closure|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected string|Closure|null $navigationGroup = null;

    protected null|int|Closure $navigationSort = null;

    protected bool|Closure $authorizeUsing = true;

    public function navigationIcon(string|Closure|BackedEnum $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): string|BackedEnum|null
    {
        return $this->evaluate($this->navigationIcon);
    }

    public function navigationGroup(string|Closure|null $navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->evaluate($this->navigationGroup);
    }

    public function navigationSort(null|int|Closure $navigationSort): static
    {
        $this->navigationSort = $navigationSort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->evaluate($this->navigationSort);
    }

    public function navigationLabel(string|Closure|null $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->evaluate($this->navigationLabel)
            ?? __('filament-alert-box::alert-box.page.navigation_label');
    }

    public function authorize(bool|Closure $callback = true): static
    {
        $this->authorizeUsing = $callback;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return $this->evaluate($this->authorizeUsing) === true;
    }

    public function title(string|Closure $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->evaluate($this->title)
            ?? __('filament-alert-box::alert-box.page.title');
    }
}
