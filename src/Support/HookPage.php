<?php

namespace Agencetwogether\AlertBox\Support;

class HookPage
{
    const array HOOKS = [
        'panels::content.after' => 'CONTENT_AFTER',
        'panels::content.before' => 'CONTENT_BEFORE',
        'panels::content.end' => 'CONTENT_END',
        'panels::content.start' => 'CONTENT_START',
        'panels::footer' => 'FOOTER',
        'panels::topbar.after' => 'TOPBAR_AFTER',
        'panels::topbar.before' => 'TOPBAR_BEFORE',
        'panels::simple-layout.end' => 'SIMPLE_LAYOUT_END',
        'panels::simple-layout.start' => 'SIMPLE_LAYOUT_START',
        'panels::simple-page.end' => 'SIMPLE_PAGE_END',
        'panels::simple-page.start' => 'SIMPLE_PAGE_START',
    ];

    public static function getHooks(): array
    {
        return self::hooksAvailable();
    }

    public static function hooksAvailable(): array
    {
        return self::HOOKS;
    }
}
