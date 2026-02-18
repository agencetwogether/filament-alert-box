<?php

use Filament\Support\Icons\Heroicon;

return [
    /*
     * This is the name of the table that will be created by the migration and
     * used by the AlertBox model shipped with this package.
     */
    'table_name' => 'filament_alert_box',

    /*
     * This is the name of the table that will be created by the migration and
     * used by the AlertBox model shipped with this package.
     */
    'page' => [
        'slug' => 'alert-box',
        'cluster' => null,
    ],

    /*
     * This is the name of the table that will be created by the migration and
     * used by the AlertBox model shipped with this package.
     */
    'toolbar_buttons' => [
        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
        ['undo', 'redo'],
    ],

    /*
     * This is the name of the table that will be created by the migration and
     * used by the AlertBox model shipped with this package.
     */
    'default_icons' => [
        'info' => Heroicon::OutlinedInformationCircle,
        'tip' => Heroicon::OutlinedLightBulb,
        'success' => Heroicon::OutlinedCheckCircle,
        'warning' => Heroicon::OutlinedExclamationTriangle,
        'danger' => Heroicon::OutlinedFire,
        'none' => null,
    ],

    /*
     * This is the name of the table that will be created by the migration and
     * used by the AlertBox model shipped with this package.
     */
    'default_colors' => [
        'info' => 'sky',
        'tip' => 'purple',
        'success' => 'green',
        'warning' => 'yellow',
        'danger' => 'red',
        'none' => null,
    ],

    /*
     * This is the name of the table that will be created by the migration and
     * used by the AlertBox model shipped with this package.
     */
    'custom_hooks' => [
        //
    ],

];
