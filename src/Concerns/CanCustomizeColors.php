<?php

namespace Agencetwogether\AlertBox\Concerns;

use Agencetwogether\AlertBox\AlertBox;
use Closure;
use InvalidArgumentException;

trait CanCustomizeColors
{
    protected null|Closure|string $colorInfo = null;

    protected null|Closure|string $colorTip = null;

    protected null|Closure|string $colorSuccess = null;

    protected null|Closure|string $colorWarning = null;

    protected null|Closure|string $colorDanger = null;

    public function colorInfo(string|Closure $color): static
    {
        $this->verifyColorExists($color);
        $this->colorInfo = $color;

        return $this;
    }

    public function colorTip(string|Closure $color): static
    {
        $this->verifyColorExists($color);
        $this->colorTip = $color;

        return $this;
    }

    public function colorSuccess(string|Closure $color): static
    {
        $this->verifyColorExists($color);
        $this->colorSuccess = $color;

        return $this;
    }

    public function colorWarning(string|Closure $color): static
    {
        $this->verifyColorExists($color);
        $this->colorWarning = $color;

        return $this;
    }

    public function colorDanger(string|Closure $color): static
    {
        $this->verifyColorExists($color);
        $this->colorDanger = $color;

        return $this;
    }

    public function getColorInfo(): ?string
    {
        return $this->evaluate($this->colorInfo);
    }

    public function getColorTip(): ?string
    {
        return $this->evaluate($this->colorTip);
    }

    public function getColorSuccess(): ?string
    {
        return $this->evaluate($this->colorSuccess);
    }

    public function getColorWarning(): ?string
    {
        return $this->evaluate($this->colorWarning);
    }

    public function getColorDanger(): ?string
    {
        return $this->evaluate($this->colorDanger);
    }

    public function verifyColorExists(string $color)
    {
        if (! in_array($color, AlertBox::getFilamentColors())) {
            throw new InvalidArgumentException("Color [{$color}] not found.");
        }
    }
}
