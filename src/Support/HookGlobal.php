<?php

namespace Agencetwogether\AlertBox\Support;

use Agencetwogether\AlertBox\AlertBox;
use Illuminate\Support\Arr;

class HookGlobal
{
    const array HOOKS = [
        'panels::auth.login.form.after' => 'AUTH_LOGIN_FORM_AFTER',
        'panels::auth.login.form.before' => 'AUTH_LOGIN_FORM_BEFORE',
        'panels::auth.password-reset.request.form.after' => 'AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER',
        'panels::auth.password-reset.request.form.before' => 'AUTH_PASSWORD_RESET_REQUEST_FORM_BEFORE',
        'panels::auth.password-reset.reset.form.after' => 'AUTH_PASSWORD_RESET_RESET_FORM_AFTER',
        'panels::auth.password-reset.reset.form.before' => 'AUTH_PASSWORD_RESET_RESET_FORM_BEFORE',
        'panels::auth.register.form.after' => 'AUTH_REGISTER_FORM_AFTER',
        'panels::auth.register.form.before' => 'AUTH_REGISTER_FORM_BEFORE',
        'panels::content.after' => 'CONTENT_AFTER',
        'panels::content.before' => 'CONTENT_BEFORE',
        'panels::content.end' => 'CONTENT_END',
        'panels::content.start' => 'CONTENT_START',
        'panels::footer' => 'FOOTER',
        'panels::page.end' => 'PAGE_END',
        'panels::page.footer-widgets.after' => 'PAGE_FOOTER_WIDGETS_AFTER',
        'panels::page.footer-widgets.before' => 'PAGE_FOOTER_WIDGETS_BEFORE',
        'panels::page.header-widgets.after' => 'PAGE_HEADER_WIDGETS_AFTER',
        'panels::page.header-widgets.before' => 'PAGE_HEADER_WIDGETS_BEFORE',
        'panels::page.start' => 'PAGE_START',
        'panels::sidebar.nav.end' => 'SIDEBAR_NAV_END',
        'panels::sidebar.nav.start' => 'SIDEBAR_NAV_START',
        'panels::sidebar.footer' => 'SIDEBAR_FOOTER',
        'panels::sidebar.start' => 'SIDEBAR_START',
        'panels::simple-layout.end' => 'SIMPLE_LAYOUT_END',
        'panels::simple-layout.start' => 'SIMPLE_LAYOUT_START',
        'panels::simple-page.end' => 'SIMPLE_PAGE_END',
        'panels::simple-page.start' => 'SIMPLE_PAGE_START',
        'panels::tenant-menu.after' => 'TENANT_MENU_AFTER',
        'panels::tenant-menu.before' => 'TENANT_MENU_BEFORE',
        'panels::topbar.after' => 'TOPBAR_AFTER',
        'panels::topbar.before' => 'TOPBAR_BEFORE',
        'panels::user-menu.profile.after' => 'USER_MENU_PROFILE_AFTER',
        'panels::user-menu.profile.before' => 'USER_MENU_PROFILE_BEFORE',
    ];

    public static function getHooks(): array
    {
        return array_merge(self::hooksAvailable(), [__('filament-alert-box::alert-box.placeholder.common.custom_hook') => self::customHooks()]);
    }

    public static function customHooks(): array
    {
        return Arr::mapWithKeys(config('filament-alert-box.custom_hooks', []), fn (string $item) => [$item => AlertBox::cleanLabel($item)]);
    }

    public static function hooksAvailable(): array
    {
        return self::HOOKS;
    }
}
