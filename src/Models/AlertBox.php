<?php

namespace Agencetwogether\AlertBox\Models;

use Illuminate\Database\Eloquent\Model;

class AlertBox extends Model
{
    public function getTable()
    {
        return config('filament-alert-box.table_name', 'filament_alert_box');
    }

    protected $fillable = [
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
