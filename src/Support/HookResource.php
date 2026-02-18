<?php

namespace Agencetwogether\AlertBox\Support;

class HookResource
{
    const array HOOKS = [
        'panels::page.end' => 'PAGE_END',
        'panels::page.footer-widgets.after' => 'PAGE_FOOTER_WIDGETS_AFTER',
        'panels::page.footer-widgets.before' => 'PAGE_FOOTER_WIDGETS_BEFORE',
        'panels::page.header-widgets.after' => 'PAGE_HEADER_WIDGETS_AFTER',
        'panels::page.header-widgets.before' => 'PAGE_HEADER_WIDGETS_BEFORE',
        'panels::page.start' => 'PAGE_START',
        'panels::resource.pages.list-records.table.after' => 'RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER',
        'panels::resource.pages.list-records.table.before' => 'RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE',
        'panels::resource.pages.manage-related-records.table.after' => 'RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_AFTER',
        'panels::resource.pages.manage-related-records.table.before' => 'RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_BEFORE',
        'panels::resource.relation-manager.after' => 'RESOURCE_RELATION_MANAGER_AFTER',
        'panels::resource.relation-manager.before' => 'RESOURCE_RELATION_MANAGER_BEFORE',
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
