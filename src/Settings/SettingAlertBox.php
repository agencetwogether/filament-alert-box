<?php

namespace Agencetwogether\AlertBox\Settings;

use Spatie\LaravelSettings\Settings;

class SettingAlertBox extends Settings
{
    public array $alerts;

    public static function group(): string
    {
        return 'alert-box';
    }
}
