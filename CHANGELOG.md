# Changelog

All notable changes to `filament-alert-box` will be documented in this file.

## 2.0.0 - 2026-08-10

### [2.0.0]

#### ⚠️ Breaking change: alerts are now scoped per panel

Previously, `AlertBoxPlugin` registered all render hooks during `Panel::boot()`,
and the entire alert list stored by `SettingAlertBox` was shared across **every**
panel where the plugin was installed. This meant an alert created in one panel's
**Manage alerts** page was also visible — and editable — from any other panel
using the plugin, with no way to isolate them.

Starting with this release:

- Alerts are stored **per panel** internally (`['admin' => [...], 'members' => [...]]`
  instead of a single shared list).
- The **Manage alerts** page in each panel now only shows and edits the alerts
  that belong to that panel. Alerts created in `admin` are no longer visible from
  `members`, and vice versa.
- **Custom hooks** (the ones you declare in `config('filament-alert-box.custom_hooks')`)
  are the one exception: they are meant to be rendered from views that live
  outside of any Filament panel (e.g. a public-facing Blade view), so they are
  never restricted to a single panel and are always registered globally,
  regardless of which panel's Manage alerts page was used to create them.

##### Upgrading

After updating to this version, run:

```bash
php artisan filament-alert-box:migrate-alerts

```
This interactively lets you choose which panels should receive a copy of your
existing (shared) alerts — nothing is deleted. Once duplicated, open the
**Manage alerts** page of each panel and remove whatever alert doesn't belong
there.

For non-interactive environments (CI/CD, deployment pipelines):

```bash
php artisan filament-alert-box:migrate-alerts --all --no-interaction

```
This duplicates existing alerts to **every** registered panel without prompting.

Use `--dry-run` to preview what the command would do without writing anything:

```bash
php artisan filament-alert-box:migrate-alerts --dry-run

```
> [!NOTE]
If you skip this command, the migration still happens automatically and
safely the first time you save the **Manage alerts** page in any panel: the
plugin detects the legacy format, duplicates it into every registered panel,
then applies your edit to the panel you just saved. Nothing is lost either
way — the command simply gives you explicit, scriptable control over the
process instead of leaving it to whichever panel happens to be saved first.

##### Added

- `php artisan filament-alert-box:migrate-alerts` command to migrate stored
  alerts from the legacy shared format to the new per-panel format, with
  `--all` and `--dry-run` options.
- `AlertBox::isCustomHook()` and `AlertBox::isLegacyAlertsFormat()` helpers.

##### Changed

- `AlertBoxPlugin::boot()` now only registers render hooks belonging to the
  current panel (native `panels::*` hooks, Resource/Page scoped alerts).
- Custom hooks are registered once, globally, from `AlertBoxServiceProvider`,
  independently of any panel's boot lifecycle — fixing cases where a custom
  hook rendered from a view outside of any panel (e.g. a public front-end page)
  never displayed its content.
- `AlertBoxSeeder` now seeds demo alerts into every panel where `AlertBoxPlugin`
  is registered, using the new per-panel storage format, instead of a single
  shared list.


---

## 1.1.0 - 2026-05-13

- Add Filament Shield Support & ensure hooks registered for current panel

**Full Changelog**: https://github.com/agencetwogether/filament-alert-box/compare/1.0.0...1.1.0

## 1.0.0 - 2026-05-10

- initial release
