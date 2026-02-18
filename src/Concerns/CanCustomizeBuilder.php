<?php

namespace Agencetwogether\AlertBox\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

trait CanCustomizeBuilder
{
    use EvaluatesClosures;

    protected string|BackedEnum|null $iconResource = Heroicon::OutlinedRectangleStack;

    protected string|BackedEnum|null $iconPage = Heroicon::OutlinedDocument;

    protected string|BackedEnum|null $iconGlobal = Heroicon::OutlinedCursorArrowRays;

    protected Alignment|string|Closure|null $addActionAlignment = null;

    protected bool|Closure $blocksAreCollapsible = false;

    protected bool|Closure $blocksAreCollapsed = false;

    public function getBlocksAreCollapsible(): bool
    {
        return (bool) $this->evaluate($this->blocksAreCollapsible);
    }

    public function getBlocksAreCollapsed(): bool
    {
        return (bool) $this->evaluate($this->blocksAreCollapsed);
    }

    public function getIconResource(): string|BackedEnum|Htmlable|null
    {
        return $this->iconResource;
    }

    public function getIconPage(): string|BackedEnum|Htmlable|null
    {
        return $this->iconPage;
    }

    public function getIconGlobal(): string|BackedEnum|Htmlable|null
    {
        return $this->iconGlobal;
    }

    public function getAddActionAlignment(): Alignment|string|null
    {
        $alignment = $this->evaluate($this->addActionAlignment);

        if (is_string($alignment)) {
            $alignment = Alignment::tryFrom($alignment) ?? $alignment;
        }

        return $alignment;
    }

    public function blocksCollapsible(bool|Closure $condition = true): static
    {
        $this->blocksAreCollapsible = $condition;

        return $this;
    }

    public function blocksCollapsed(bool|Closure $condition = true): static
    {
        $this->blocksAreCollapsed = $condition;

        $this->blocksCollapsible(true);

        return $this;
    }

    public function iconResource(string|BackedEnum $icon): static
    {
        $this->iconResource = $icon;

        return $this;
    }

    public function iconPage(string|BackedEnum $icon): static
    {
        $this->iconPage = $icon;

        return $this;
    }

    public function iconGlobal(string|BackedEnum $icon): static
    {
        $this->iconGlobal = $icon;

        return $this;
    }

    public function addActionAlignment(Alignment|string|Closure|null $addActionAlignment): static
    {
        $this->addActionAlignment = $addActionAlignment;

        return $this;
    }
}
