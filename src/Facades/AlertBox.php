<?php

namespace Agencetwogether\AlertBox\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Agencetwogether\AlertBox\AlertBox
 */
class AlertBox extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Agencetwogether\AlertBox\AlertBox::class;
    }
}
