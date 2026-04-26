# Filament Shadcn Theme

A configurable shadcn-inspired theme plugin for Filament v5 panels.

The package maps shadcn-style tokens onto Filament's generated HTML, so it can theme navigation, topbar, cards, forms, tables, pagination, checkboxes, empty states, modals, dropdowns, widgets, and light/dark mode without requiring a Vite asset build.

## Requirements

- PHP 8.2 or higher
- Laravel 11 or 12
- Filament 5

## Installation

If the package is available through your private Composer repository:

```bash
composer require irajul/filament-shadcn-theme
php artisan vendor:publish --tag=filament-shadcn-theme-config
```

The package is auto-discovered by Laravel. Register the plugin on each Filament panel that should use the theme.

```php
<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
use Filament\Panel;
use Filament\PanelProvider;
use Irajul\FilamentShadcnTheme\Enums\BaseColor;
use Irajul\FilamentShadcnTheme\Enums\MenuAccent;
use Irajul\FilamentShadcnTheme\Enums\Radius;
use Irajul\FilamentShadcnTheme\Enums\SidebarVariant;
use Irajul\FilamentShadcnTheme\Enums\StyleVariant;
use Irajul\FilamentShadcnTheme\Enums\SurfaceShadow;
use Irajul\FilamentShadcnTheme\Enums\ThemeColor;
use Irajul\FilamentShadcnTheme\FilamentShadcnThemePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->defaultThemeMode(ThemeMode::Dark)
            ->darkMode(true)
            ->plugin(
                FilamentShadcnThemePlugin::make()
                    ->style(StyleVariant::Lyra)
                    ->baseColor(BaseColor::Taupe)
                    ->themeColor(ThemeColor::Taupe)
                    ->chartColor(ThemeColor::Taupe)
                    ->font('inter')
                    ->headingFont('inherit')
                    ->radius(Radius::None)
                    ->menuAccent(MenuAccent::Subtle)
                    ->sidebarVariant(SidebarVariant::Sidebar)
                    ->surfaceShadow(SurfaceShadow::ExtraSmall)
            );
    }
}
```

## Defaults

The default package look is intentionally sharp and shadcn-like:

```php
[
    'style' => 'lyra',
    'base_color' => 'taupe',
    'theme_color' => 'taupe',
    'chart_color' => null,
    'font' => 'inter',
    'heading_font' => 'inherit',
    'icon_library' => 'lucide',
    'radius' => 'none',
    'menu_color' => 'default',
    'menu_accent' => 'subtle',
    'sidebar_variant' => 'sidebar',
    'surface_shadow' => 'xs',
]
```

You may change defaults in `config/filament-shadcn-theme.php`, or override them fluently in the panel provider.

## Configuration Options

### Style

Controls density, spacing, table rhythm, sidebar spacing, card padding, and radius personality.

Available values:

```php
StyleVariant::Vega
StyleVariant::Nova
StyleVariant::Maia
StyleVariant::Lyra
StyleVariant::Mira
StyleVariant::Luma
StyleVariant::Sera
```

String values: `vega`, `nova`, `maia`, `lyra`, `mira`, `luma`, `sera`.

### Base Color

Controls neutral surfaces, borders, text, sidebar background, muted text, and table backgrounds.

Available values:

```php
BaseColor::Neutral
BaseColor::Stone
BaseColor::Zinc
BaseColor::Mauve
BaseColor::Olive
BaseColor::Mist
BaseColor::Taupe
```

String values: `neutral`, `stone`, `zinc`, `mauve`, `olive`, `mist`, `taupe`.

### Theme Color

Controls primary buttons, active navigation, focus rings, and primary accents.

Available values:

```php
ThemeColor::Neutral
ThemeColor::Stone
ThemeColor::Zinc
ThemeColor::Mauve
ThemeColor::Olive
ThemeColor::Mist
ThemeColor::Taupe
ThemeColor::Amber
ThemeColor::Blue
ThemeColor::Cyan
ThemeColor::Emerald
ThemeColor::Fuchsia
ThemeColor::Green
ThemeColor::Indigo
ThemeColor::Lime
ThemeColor::Orange
ThemeColor::Pink
ThemeColor::Purple
ThemeColor::Red
ThemeColor::Rose
ThemeColor::Sky
ThemeColor::Teal
ThemeColor::Violet
ThemeColor::Yellow
```

String values: `neutral`, `stone`, `zinc`, `mauve`, `olive`, `mist`, `taupe`, `amber`, `blue`, `cyan`, `emerald`, `fuchsia`, `green`, `indigo`, `lime`, `orange`, `pink`, `purple`, `red`, `rose`, `sky`, `teal`, `violet`, `yellow`.

### Chart Color

Controls `--chart-*` tokens. If omitted, the chart palette follows `theme_color`.

```php
->chartColor(ThemeColor::Emerald)
```

### Font And Heading Font

Controls the generated `--font-sans` and `--font-heading` tokens. The panel font is also applied through Filament by default.

Built-in font keys:

```text
inter
geist
figtree
manrope
dm-sans
public-sans
noto-sans
nunito-sans
space-grotesk
montserrat
ibm-plex-sans
jetbrains-mono
geist-mono
```

Any custom font family string is accepted:

```php
->font('Geist')
->headingFont('Space Grotesk')
```

Use `heading_font => inherit` or `->headingFont('inherit')` to keep headings on the body font stack.

### Radius

Controls shadcn radius tokens and component corners.

Available values:

```php
Radius::Default // 0.625rem
Radius::None    // 0
Radius::Small   // 0.45rem
Radius::Medium  // 0.625rem
Radius::Large   // 0.875rem
```

String values: `default`, `none`, `small`, `medium`, `large`.

For the sharp Lyra look, use `Radius::None`.

### Icon Library

Currently stored as a CSS metadata token so the same config can support icon-library-specific styling later.

Available values:

```php
IconLibrary::Lucide
IconLibrary::Heroicons
IconLibrary::Tabler
IconLibrary::Phosphor
IconLibrary::Radix
```

String values: `lucide`, `heroicons`, `tabler`, `phosphor`, `radix`.

### Menu Color

Controls sidebar color treatment.

Available values:

```php
MenuColor::Default
MenuColor::Inverted
MenuColor::DefaultTranslucent
MenuColor::InvertedTranslucent
```

String values: `default`, `inverted`, `default-translucent`, `inverted-translucent`.

### Menu Accent

Controls active navigation intensity.

Available values:

```php
MenuAccent::Subtle
MenuAccent::Bold
```

String values: `subtle`, `bold`.

### Sidebar Variant

Controls sidebar offset, border, radius, and shadow.

Available values:

```php
SidebarVariant::Sidebar
SidebarVariant::Floating
SidebarVariant::Inset
```

String values: `sidebar`, `floating`, `inset`.

### Surface Shadow

Controls shared panel and floating surface shadow tokens.

Available values:

```php
SurfaceShadow::None
SurfaceShadow::ExtraSmall
SurfaceShadow::Small
SurfaceShadow::Medium
```

String values: `none`, `xs`, `sm`, `md`.

## Advanced Overrides

Use token overrides when you want to change shadcn tokens directly.

```php
FilamentShadcnThemePlugin::make()
    ->tokens(
        light: [
            'background' => 'oklch(1 0 0)',
            'primary' => 'oklch(0.55 0.18 140)',
        ],
        dark: [
            'background' => 'oklch(0.16 0.01 90)',
            'primary' => 'oklch(0.78 0.2 140)',
        ],
    );
```

Use style variables for layout and density adjustments.

```php
FilamentShadcnThemePlugin::make()
    ->styleVariables([
        'fs-sidebar-width' => '18rem',
        'fs-main-padding-y' => '1rem',
        'fs-table-cell-padding-y' => '0.35rem',
        'fs-table-edge-padding-x' => '0.875rem',
    ]);
```

Use selector overrides if your Filament build or custom plugin outputs a different class.

```php
FilamentShadcnThemePlugin::make()
    ->selectorMap([
        'card' => '.fi-section, .custom-admin-card',
        'button' => '.fi-btn, .custom-admin-button',
    ]);
```

## Config File Example

After publishing config, edit `config/filament-shadcn-theme.php`:

```php
return [
    'style' => 'lyra',
    'base_color' => 'taupe',
    'theme_color' => 'taupe',
    'chart_color' => 'emerald',
    'font' => 'inter',
    'heading_font' => 'inherit',
    'icon_library' => 'lucide',
    'radius' => 'none',
    'menu_color' => 'default',
    'menu_accent' => 'subtle',
    'sidebar_variant' => 'sidebar',
    'surface_shadow' => 'xs',
    'apply_panel_font' => true,
    'default_theme_mode' => 'dark',
    'dark_mode' => true,
    'force_dark_mode' => false,
    'token_overrides' => [
        'light' => [],
        'dark' => [],
    ],
    'style_overrides' => [],
    'selector_map' => [],
];
```

Then register the plugin with no extra arguments:

```php
->plugin(FilamentShadcnThemePlugin::make())
```

## Testing The Package

Package tests live inside this package, not in the host Laravel app.

```bash
composer install
composer test
```

The tests are intentionally standalone. They exercise configuration hydration, fluent plugin configuration, shadcn token rendering, Filament selector coverage, light/dark token output, and the exposed option enums without relying on a host application's test case.

## Private Composer Distribution

This repository includes `satis.json.example` and a full setup guide in `docs/private-composer-distribution.md`.

Short version:

```bash
composer create-project composer/satis satis dev-main
cd satis
php bin/satis build satis.json public/
```

Point `satis.json` at this package's private Git repository, serve the generated `public/` directory over HTTPS, and add it to consuming apps:

```bash
composer config repositories.private-packages composer https://packages.example.com
composer require irajul/filament-shadcn-theme:^1.0
```

Tag releases before rebuilding Satis:

```bash
composer validate --strict
composer test
git tag v1.0.0
git push origin main --tags
```

## Notes For Publishing

- Keep `composer.json` package metadata updated before publishing.
- Confirm the final Composer package name before tagging the first release.
- Keep the package private unless you replace the proprietary license.
- The package does not ship compiled assets. It injects generated CSS through Filament's panel render hook.
- Register the plugin per panel if your app has multiple Filament panels.
