<?php

namespace Agencetwogether\AlertBox\Settings;

use Spatie\LaravelSettings\Settings;

class SettingAlertBox extends Settings
{
    /**
     * Alerts stored PER PANEL:
     * [
     *     'admin' => [ ...admin panel blocks... ],
     *     'other' => [ ...other panel blocks... ],
     * ]
     *
     * Scoping is done in ManageAlertBox (mutateFormDataBeforeFill/Save),
     * not in the form schema.
     */
    public array $alerts = [];

    public static function group(): string
    {
        return 'alert-box';
    }
}
