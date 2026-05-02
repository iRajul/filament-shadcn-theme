<?php

use Filament\Enums\ThemeMode;
use Irajul\FilamentShadcnTheme\Enums\BaseColor;
use Irajul\FilamentShadcnTheme\Enums\CssMode;
use Irajul\FilamentShadcnTheme\Enums\IconLibrary;
use Irajul\FilamentShadcnTheme\Enums\MenuAccent;
use Irajul\FilamentShadcnTheme\Enums\MenuColor;
use Irajul\FilamentShadcnTheme\Enums\Radius;
use Irajul\FilamentShadcnTheme\Enums\SidebarVariant;
use Irajul\FilamentShadcnTheme\Enums\StyleVariant;
use Irajul\FilamentShadcnTheme\Enums\SurfaceShadow;
use Irajul\FilamentShadcnTheme\Enums\ThemeColor;
use Irajul\FilamentShadcnTheme\FilamentShadcnThemePlugin;
use Irajul\FilamentShadcnTheme\FilamentShadcnThemeServiceProvider;
use Irajul\FilamentShadcnTheme\Support\CssAssetManager;
use Irajul\FilamentShadcnTheme\Support\CssRenderer;
use Irajul\FilamentShadcnTheme\Support\FontRegistry;
use Irajul\FilamentShadcnTheme\Support\PaletteRegistry;
use Irajul\FilamentShadcnTheme\Support\TokenRegistry;
use Irajul\FilamentShadcnTheme\ThemeConfig;

function filamentShadcnThemeTestRenderer(): CssRenderer
{
    $palettes = new PaletteRegistry;

    return new CssRenderer(
        tokens: new TokenRegistry($palettes),
        palettes: $palettes,
        fonts: new FontRegistry,
    );
}

function filamentShadcnThemeDeleteTestAssets(): void
{
    $directory = public_path('vendor/filament-shadcn-theme');

    foreach (glob($directory.'/*.css') ?: [] as $file) {
        unlink($file);
    }

    if (is_dir($directory)) {
        rmdir($directory);
    }
}

it('uses lyra and taupe as publishable package defaults', function (): void {
    $config = ThemeConfig::make();

    expect($config->style)->toBe(StyleVariant::Lyra)
        ->and($config->baseColor)->toBe(BaseColor::Taupe)
        ->and($config->themeColor)->toBe(ThemeColor::Taupe)
        ->and($config->font)->toBe('inter')
        ->and($config->headingFont)->toBe('inherit')
        ->and($config->iconLibrary)->toBe(IconLibrary::Lucide)
        ->and($config->cssMode)->toBe(CssMode::Inline)
        ->and($config->radius)->toBe(Radius::None)
        ->and($config->menuColor)->toBe(MenuColor::Default)
        ->and($config->menuAccent)->toBe(MenuAccent::Subtle)
        ->and($config->sidebarVariant)->toBe(SidebarVariant::Sidebar)
        ->and($config->surfaceShadow)->toBe(SurfaceShadow::ExtraSmall)
        ->and($config->style->variables()['fs-sidebar-section-gap'])->toBe('0.375rem');
});

it('writes hashed CSS assets and clears stale generated files', function (): void {
    filamentShadcnThemeDeleteTestAssets();

    $manager = new CssAssetManager(filamentShadcnThemeTestRenderer());
    $config = ThemeConfig::make()
        ->cssMode(CssMode::CachedAsset)
        ->style(StyleVariant::Lyra)
        ->baseColor(BaseColor::Olive);

    $asset = $manager->ensureAsset($config, 'admin');
    $stalePath = dirname($asset['path']).'/panel-admin-stale.css';

    file_put_contents($stalePath, 'old css');

    $freshAsset = $manager->ensureAsset($config, 'admin');

    expect($asset['filename'])->toStartWith('panel-admin-')
        ->toEndWith('.css')
        ->and($asset['url'])->toContain('/vendor/filament-shadcn-theme/'.$asset['filename'])
        ->and(file_exists($freshAsset['path']))->toBeTrue()
        ->and(file_get_contents($freshAsset['path']))->toContain('data-filament-shadcn-generated')
        ->and(file_exists($stalePath))->toBeFalse();

    $changedAsset = $manager->ensureAsset(
        $config->styleVariables(['fs-main-padding-y' => '9rem']),
        'admin',
    );

    expect($changedAsset['path'])
        ->not->toBe($freshAsset['path'])
        ->and(file_exists($freshAsset['path']))->toBeFalse()
        ->and(file_get_contents($changedAsset['path']))->toContain('--fs-main-padding-y: 9rem;')
        ->and($manager->clear('admin'))->toBe(1)
        ->and(file_exists($changedAsset['path']))->toBeFalse();
});

it('registers Artisan commands for warming and clearing cached CSS assets', function (): void {
    filamentShadcnThemeDeleteTestAssets();

    config()->set('filament-shadcn-theme.css_mode', 'cached-asset');

    $this->artisan('filament-shadcn-theme:cache', ['--panel' => 'admin'])
        ->assertExitCode(0);

    $files = glob(public_path('vendor/filament-shadcn-theme/panel-admin-*.css')) ?: [];

    expect($files)->toHaveCount(1)
        ->and(file_get_contents($files[0]))->toContain('data-filament-shadcn-generated');

    $this->artisan('filament-shadcn-theme:clear', ['--panel' => 'admin'])
        ->assertExitCode(0);

    expect(glob(public_path('vendor/filament-shadcn-theme/panel-admin-*.css')) ?: [])->toBe([]);
});

it('keeps Lyra sharp by default and honours explicitly configured radius', function (): void {
    $sharpConfig = ThemeConfig::make()
        ->style(StyleVariant::Lyra);

    $roundedConfig = ThemeConfig::make()
        ->style(StyleVariant::Lyra)
        ->radius(Radius::Small);

    $renderer = filamentShadcnThemeTestRenderer();
    $sharpCss = $renderer->render($sharpConfig);
    $roundedCss = $renderer->render($roundedConfig);

    expect($sharpConfig->hasExplicitRadius())->toBeFalse()
        ->and($roundedConfig->hasExplicitRadius())->toBeTrue()
        ->and($sharpConfig->style->variables()['fs-card-radius'])->toBe('0')
        ->and($sharpConfig->style->variables()['fs-control-radius'])->toBe('0')
        ->and($sharpConfig->style->variables()['fs-button-radius'])->toBe('0')
        ->and($sharpConfig->style->variables()['fs-dropdown-radius'])->toBe('0')
        ->and($sharpConfig->style->variables()['fs-sidebar-item-radius'])->toBe('0')
        ->and($sharpConfig->style->variables()['fs-badge-radius'])->toBe('0')
        ->and($sharpConfig->style->variables()['fs-checkbox-radius'])->toBe('0')
        ->and($sharpCss)->toContain('--radius: 0;')
        ->toContain('--fs-card-radius: 0;')
        ->toContain('--fs-control-radius: 0;')
        ->toContain('--fs-button-radius: 0;')
        ->toContain('--fs-checkbox-radius: 0;')
        ->toContain('--fs-skeleton-line-radius: 0;')
        ->and($roundedCss)->toContain('--radius: 0.45rem;')
        ->toContain('--fs-card-radius: var(--radius-xl);')
        ->toContain('--fs-control-radius: var(--radius-md);')
        ->toContain('--fs-button-radius: var(--radius-md);')
        ->toContain('--fs-button-group-radius: var(--radius-md);')
        ->toContain('--fs-dropdown-radius: var(--radius-lg);')
        ->toContain('--fs-sidebar-item-radius: var(--radius-md);')
        ->toContain('--fs-dropdown-item-radius: var(--radius-sm);')
        ->toContain('--fs-badge-radius: var(--radius-md);')
        ->toContain('--fs-checkbox-radius: var(--radius-sm);')
        ->toContain('--fs-table-header-button-radius: var(--radius-sm);')
        ->toContain('--fs-pagination-radius: var(--radius-md);')
        ->toContain('--fs-tab-list-radius: var(--radius-md);')
        ->toContain('--fs-tab-item-radius: var(--radius-sm);')
        ->toContain('--fs-rich-editor-tool-radius: var(--radius-sm);')
        ->toContain('--fs-skeleton-line-radius: var(--radius-md);')
        ->toContain('border-radius: var(--fs-control-radius) !important;')
        ->toContain('border-radius: var(--fs-button-radius) !important;')
        ->toContain('border-radius: var(--fs-dropdown-radius) !important;')
        ->toContain('.fi-input-wrp .fi-select-input')
        ->toContain('border: 0 !important;')
        ->toContain('.fi-fo-toggle-buttons-btn-ctn:has(.fi-fo-toggle-buttons-input:checked) .fi-btn')
        ->toContain('background: color-mix(in oklch, var(--color-400, var(--primary)) 18%, transparent) !important;')
        ->toContain('html.fi.dark .fi-fo-toggle-buttons-btn-ctn:has(.fi-fo-toggle-buttons-input:checked) .fi-btn')
        ->toContain('.fi-toggle.fi-toggle-off')
        ->toContain('background-color: var(--input) !important;')
        ->toContain('html.fi.dark .fi-toggle.fi-toggle-off > div')
        ->toContain('background: var(--foreground) !important;')
        ->toContain('.fi-toggle.fi-toggle-on')
        ->toContain('background-color: var(--primary) !important;')
        ->toContain('.fi-toggle.fi-toggle-on > div')
        ->toContain('background: var(--primary-foreground) !important;')
        ->toContain('.fi-fo-rich-editor-toolbar')
        ->toContain('border-bottom: 1px solid var(--border) !important;')
        ->toContain('.fi-fo-table-repeater')
        ->toContain('.fi-fo-table-repeater table')
        ->toContain('border-radius: var(--fs-card-radius) !important;')
        ->toContain('.fi-fo-table-repeater tbody tr:last-child td:last-child')
        ->toContain('border-end-end-radius: var(--fs-card-radius) !important;')
        ->toContain('.fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-badge');
});

it('renders configurable shadcn tokens and Filament selectors without a host app test case', function (): void {
    $config = ThemeConfig::make()
        ->style(StyleVariant::Mira)
        ->baseColor(BaseColor::Mist)
        ->themeColor(ThemeColor::Lime)
        ->chartColor(ThemeColor::Emerald)
        ->font('geist')
        ->headingFont('space-grotesk')
        ->iconLibrary(IconLibrary::Lucide)
        ->radius(Radius::Medium)
        ->menuColor(MenuColor::DefaultTranslucent)
        ->menuAccent(MenuAccent::Bold)
        ->sidebarVariant(SidebarVariant::Floating)
        ->surfaceShadow(SurfaceShadow::Small)
        ->tokens(
            light: ['background' => 'oklch(0.99 0 0)'],
            dark: ['background' => 'oklch(0.16 0 0)'],
        )
        ->styleVariables([
            'fs-sidebar-width' => '17rem',
        ])
        ->selectorMap([
            'button' => '.fi-btn, .custom-theme-button',
        ]);

    $css = filamentShadcnThemeTestRenderer()->render($config);

    expect($css)
        ->toContain('data-filament-shadcn-generated')
        ->toContain('html.fi {')
        ->toContain('html.fi.dark {')
        ->toContain('--background: oklch(0.99 0 0);')
        ->toContain('--background: oklch(0.16 0 0);')
        ->toContain('--chart-1:')
        ->toContain('--font-sans: Geist, ui-sans-serif, system-ui, sans-serif;')
        ->toContain('--font-heading: "Space Grotesk", ui-sans-serif, system-ui, sans-serif;')
        ->toContain('--radius: 0.625rem;')
        ->toContain('--fs-sidebar-width: 17rem;')
        ->toContain('--fs-sidebar-offset: 0.5rem;')
        ->toContain('--fs-sidebar-group-rail-display: none;')
        ->toContain('--fs-sidebar-header-with-topbar-display: none;')
        ->toContain('--fs-topbar-collapse-button-order: 2;')
        ->toContain('--fs-shell-divider-width: 1px;')
        ->toContain('--fs-collapsed-sidebar-width: 4rem;')
        ->toContain('--fs-collapsed-topbar-brand-display: none;')
        ->toContain('--fs-collapsed-sidebar-top: var(--fs-topbar-height);')
        ->toContain('--fs-table-content-padding: 0;')
        ->toContain('--fs-table-header-background:')
        ->toContain('--fs-table-edge-padding-x:')
        ->toContain('--fs-table-selection-cell-width:')
        ->toContain('--fs-pagination-height:')
        ->toContain('--fs-sidebar-section-gap:')
        ->toContain('--fs-sidebar-topbar-height:')
        ->toContain('--fs-sidebar-item-padding-y:')
        ->toContain('--fs-sidebar-sub-item-padding-start:')
        ->toContain('--fs-checkbox-background:')
        ->toContain('--fs-dark-checkbox-checked-icon:')
        ->toContain('--fs-page-header-main-padding-y:')
        ->toContain('--fs-skeleton-animation-duration: 1.4s;')
        ->toContain('--fs-surface-shadow: 0 1px 3px')
        ->toContain('--sidebar-width: var(--fs-sidebar-width) !important;')
        ->toContain('--sidebar-accent: var(--primary);')
        ->toContain('.fi-sidebar')
        ->toContain('.fi-btn.fi-color-primary')
        ->toContain('.custom-theme-button:focus-visible')
        ->toContain('.fi-sidebar-group-btn')
        ->toContain('.fi-sidebar-nav-groups')
        ->toContain('.fi-sidebar-sub-group-items::before')
        ->toContain('.fi-body-has-topbar .fi-main-sidebar')
        ->toContain('.fi-body-has-topbar .fi-sidebar-header-ctn')
        ->toContain('.fi-sidebar-group.fi-collapsed .fi-sidebar-group-items')
        ->toContain('.fi-topbar-collapse-sidebar-btn-ctn')
        ->toContain('.fi-topbar > .fi-topbar-open-sidebar-btn')
        ->toContain('.fi-topbar > .fi-topbar-close-sidebar-btn')
        ->toContain('.fi-page-header-main-ctn')
        ->toContain('.fi-topbar::before')
        ->toContain('html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-topbar-start > .fi-logo')
        ->toContain('html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-main-sidebar')
        ->not->toContain('.fi-sidebar-group-button')
        ->toContain('.fi-dropdown-list-item')
        ->toContain('.fi-modal-footer')
        ->toContain('.fi-ta-header-cell-sort-btn')
        ->toContain('.fi-ta-header-toolbar')
        ->toContain('.fi-ta-content-ctn')
        ->toContain('padding: var(--fs-table-content-padding) !important;')
        ->toContain('.fi-ta-table thead tr')
        ->toContain('.fi-ta-row:has(.fi-ta-record-checkbox:checked)')
        ->toContain('.fi-ta-table thead .fi-ta-header-cell:not(.fi-ta-selection-cell)')
        ->toContain('padding-inline: var(--fs-table-cell-padding-x) !important;')
        ->toContain('text-align: start !important;')
        ->toContain('.fi-ta-table thead .fi-ta-header-cell:not(.fi-ta-selection-cell):first-child')
        ->toContain('padding-inline-start: calc(var(--fs-table-edge-padding-x) + var(--fs-table-cell-padding-x)) !important;')
        ->toContain('.fi-ta-table thead .fi-ta-selection-cell + .fi-ta-header-cell')
        ->toContain('.fi-ta-table tbody .fi-ta-selection-cell + .fi-ta-cell')
        ->toContain('.fi-ta-table .fi-ta-col > .fi-ta-text')
        ->toContain('padding: var(--fs-table-cell-padding-y) var(--fs-table-cell-padding-x) !important;')
        ->toContain('.fi-ta-table .fi-ta-actions .fi-icon-btn.fi-ac-icon-btn-group')
        ->toContain('.fi-ta-table .fi-ta-actions .fi-btn.fi-ac-btn-group')
        ->toContain('background: color-mix(in oklch, var(--secondary) 68%, transparent) !important;')
        ->toContain('padding-inline: 0.625rem !important;')
        ->toContain('.fi-pagination')
        ->toContain('display: flex !important;')
        ->toContain('.fi-pagination-items')
        ->toContain('.fi-pagination-next-btn')
        ->toContain('.fi-checkbox-input')
        ->toContain('background-color: var(--fs-checkbox-background) !important;')
        ->toContain('background-image: var(--fs-checkbox-checked-icon) !important;')
        ->toContain('html.fi.dark .fi-checkbox-input:checked')
        ->toContain('background-color: var(--fs-checkbox-checked-background) !important;')
        ->toContain('.fi-checkbox-input:checked')
        ->not->toContain('.fi-toggle, .fi-checkbox-input, .fi-radio-input:checked')
        ->toContain('.fi-ta-empty-state')
        ->toContain('.fi-loading-section')
        ->toContain('@keyframes filament-shadcn-skeleton-pulse')
        ->toContain('.fi-wi-widget')
        ->toContain('.fi-fo-repeater-item')
        ->toContain('.fi-btn-group')
        ->toContain('html.fi.dark .fi-input-wrp');
});

it('renders JetBrains Mono when configured through the plugin API', function (): void {
    $plugin = FilamentShadcnThemePlugin::make(ThemeConfig::make())
        ->font('jetbrains-mono');

    $css = filamentShadcnThemeTestRenderer()->render($plugin->config());

    expect($plugin->config()->font)->toBe('jetbrains-mono')
        ->and((new FontRegistry)->panelFamily($plugin->config()->font))->toBe('JetBrains Mono')
        ->and($css)->toContain('--font-sans: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, monospace;')
        ->toContain('--font-heading: var(--font-sans);')
        ->toContain('font-family: var(--font-sans);');
});

it('keeps table action group buttons compact and theme-colored', function (): void {
    $css = filamentShadcnThemeTestRenderer()->render(ThemeConfig::make());

    expect($css)
        ->toContain(".fi-ta-table .fi-ta-actions .fi-btn.fi-ac-btn-group {\n    background: color-mix(in oklch, var(--secondary) 68%, transparent) !important;")
        ->toContain('border: 1px solid var(--border) !important;')
        ->toContain('min-height: var(--fs-control-height-sm);')
        ->toContain('padding-inline: 0.625rem !important;')
        ->toContain(".fi-ta-table .fi-ta-actions .fi-btn.fi-ac-btn-group:hover {\n    background: var(--accent) !important;");
});

it('keeps collapsed sidebar group spacing stable while hiding collapsed children', function (): void {
    $config = ThemeConfig::make()
        ->style(StyleVariant::Lyra);
    $css = filamentShadcnThemeTestRenderer()->render($config);

    expect($config->style->variables()['fs-sidebar-section-gap'])->toBe('0.375rem')
        ->and($css)->toContain('--fs-sidebar-section-gap: 0.375rem;')
        ->toContain(".fi-sidebar-nav-groups {\n    margin-inline: 0 !important;\n    gap: var(--fs-sidebar-section-gap) !important;")
        ->toContain(".fi-sidebar-group.fi-collapsed .fi-sidebar-group-items,\n.fi-sidebar-group.fi-collapsed .fi-sidebar-sub-group-items {\n    display: none !important;")
        ->not->toContain('.fi-sidebar-nav-groups:has(.fi-sidebar-group.fi-collapsed)')
        ->not->toContain('--fs-sidebar-collapsed-section-gap')
        ->not->toContain('--fs-sidebar-collapsed-group-padding-y')
        ->not->toContain(".fi-sidebar-group.fi-collapsed {\n    padding-block:");
});

it('indents sidebar sub navigation with a continuous rail', function (): void {
    $config = ThemeConfig::make()
        ->style(StyleVariant::Lyra);
    $css = filamentShadcnThemeTestRenderer()->render($config);

    expect($config->style->variables()['fs-sidebar-sub-group-gap'])->toBe('0.375rem')
        ->and($config->style->variables()['fs-sidebar-sub-group-margin-y'])->toBe('0.25rem')
        ->and($css)->toContain('--fs-sidebar-sub-item-padding-start: calc(var(--fs-sidebar-item-padding-x) + var(--fs-icon-size) + 0.5rem);')
        ->toContain(".fi-sidebar-sub-group-items {\n    position: relative;\n    gap: var(--fs-sidebar-sub-group-gap) !important;")
        ->toContain(".fi-sidebar-sub-group-items::before {\n    content: \"\";\n    position: absolute;")
        ->toContain('inset-inline-start: var(--fs-sidebar-sub-group-rail-left);')
        ->toContain(".fi-sidebar-sub-group-items > .fi-sidebar-item > .fi-sidebar-item-btn {\n    padding-inline-start: var(--fs-sidebar-sub-item-padding-start) !important;")
        ->toContain(".fi-sidebar-sub-group-items .fi-sidebar-item-grouped-border {\n    display: none !important;");
});

it('indents sidebar links nested under icon navigation groups', function (): void {
    $css = filamentShadcnThemeTestRenderer()->render(ThemeConfig::make());

    expect($css)
        ->toContain('.fi-sidebar-group:has(> .fi-sidebar-group-btn > .fi-icon) > .fi-sidebar-group-items')
        ->toContain(".fi-sidebar-group:has(> .fi-sidebar-group-btn > .fi-icon) > .fi-sidebar-group-btn {\n    background: transparent !important;\n    color: var(--sidebar-foreground) !important;")
        ->toContain('font-size: var(--fs-font-size-sm);')
        ->toContain('gap: 0.5rem;')
        ->toContain(".fi-sidebar-group:has(> .fi-sidebar-group-btn > .fi-icon) > .fi-sidebar-group-btn > .fi-sidebar-group-label {\n    color: inherit !important;")
        ->toContain('.fi-sidebar-group:has(> .fi-sidebar-group-btn > .fi-icon) > .fi-sidebar-group-items::before')
        ->toContain('.fi-sidebar-group:has(> .fi-sidebar-group-btn > .fi-icon) > .fi-sidebar-group-items > .fi-sidebar-item > .fi-sidebar-item-btn')
        ->toContain('padding-inline-start: var(--fs-sidebar-sub-item-padding-start) !important;')
        ->toContain('justify-content: flex-start;')
        ->toContain('.fi-sidebar-group:has(> .fi-sidebar-group-btn > .fi-icon) > .fi-sidebar-group-items .fi-sidebar-item-grouped-border')
        ->toContain('display: none !important;');
});

it('keeps the standard sidebar in document flow when a topbar is present', function (): void {
    $standardCss = filamentShadcnThemeTestRenderer()->render(
        ThemeConfig::make()->sidebarVariant(SidebarVariant::Sidebar),
    );
    $floatingCss = filamentShadcnThemeTestRenderer()->render(
        ThemeConfig::make()->sidebarVariant(SidebarVariant::Floating),
    );

    expect($standardCss)
        ->toContain('--fs-layout-desktop-height: auto;')
        ->toContain('--fs-sidebar-rail-display: block;')
        ->toContain('--fs-sidebar-desktop-position: relative;')
        ->toContain('--fs-sidebar-desktop-align-self: stretch;')
        ->toContain('--fs-sidebar-desktop-top: auto;')
        ->toContain('--fs-sidebar-desktop-bottom: auto;')
        ->toContain('--fs-sidebar-desktop-height: auto;')
        ->toContain('--fs-sidebar-desktop-min-height: var(--fs-sidebar-topbar-height);')
        ->toContain('--fs-collapsed-sidebar-top: auto;')
        ->toContain('--fs-collapsed-sidebar-height: auto;')
        ->toContain(".fi-body-has-topbar .fi-layout {\n        height: var(--fs-layout-desktop-height) !important;\n        min-height: var(--fs-layout-desktop-min-height) !important;\n        position: relative;\n    }")
        ->toContain(".fi-body-has-topbar .fi-layout::before {\n        background: var(--sidebar);\n        border-inline-end: var(--fs-shell-divider-width) solid var(--sidebar-border);\n        content: \"\";\n        display: var(--fs-sidebar-rail-display);\n        inset-block: 0;\n        inset-inline-start: 0;\n        pointer-events: none;\n        position: absolute;\n        width: var(--fs-sidebar-rail-width);\n        z-index: 0;\n    }")
        ->toContain("html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-layout {\n        --fs-sidebar-rail-width: var(--fs-collapsed-sidebar-width);\n    }")
        ->toContain(".fi-body-has-topbar .fi-main-sidebar {\n        align-self: var(--fs-sidebar-desktop-align-self) !important;\n        bottom: var(--fs-sidebar-desktop-bottom) !important;\n        height: var(--fs-sidebar-desktop-height) !important;\n        min-height: var(--fs-sidebar-desktop-min-height) !important;\n        position: var(--fs-sidebar-desktop-position) !important;\n        top: var(--fs-sidebar-desktop-top) !important;\n    }")
        ->toContain(".fi-body-has-topbar .fi-main-ctn {\n        position: relative;\n        z-index: 1;\n    }");

    expect($floatingCss)
        ->toContain('--fs-layout-desktop-height: 100%;')
        ->toContain('--fs-sidebar-rail-display: none;')
        ->toContain('--fs-sidebar-desktop-position: sticky;')
        ->toContain('--fs-sidebar-desktop-align-self: flex-start;')
        ->toContain('--fs-sidebar-desktop-top: var(--fs-topbar-height);')
        ->toContain('--fs-sidebar-desktop-height: var(--fs-sidebar-topbar-height);')
        ->toContain('--fs-collapsed-sidebar-top: var(--fs-topbar-height);');
});

it('builds fluent plugin configuration without reading host application config', function (): void {
    $plugin = FilamentShadcnThemePlugin::make(ThemeConfig::make())
        ->style('mira')
        ->baseColor('mist')
        ->themeColor('lime')
        ->chartColor('emerald')
        ->font('geist')
        ->headingFont('space-grotesk')
        ->iconLibrary('lucide')
        ->cssMode('cached-asset')
        ->radius('medium')
        ->menuColor('default-translucent')
        ->menuAccent('bold')
        ->sidebarVariant('floating')
        ->surfaceShadow('sm')
        ->defaultThemeMode(ThemeMode::Dark)
        ->darkMode(true)
        ->tokens(
            light: ['background' => 'oklch(1 0 0)'],
            dark: ['background' => 'oklch(0 0 0)'],
        )
        ->styleVariables([
            'fs-sidebar-width' => '18rem',
        ])
        ->selectorMap([
            'button' => '.fi-btn, .package-button',
        ]);

    expect($plugin->config()->style)->toBe(StyleVariant::Mira)
        ->and($plugin->config()->baseColor)->toBe(BaseColor::Mist)
        ->and($plugin->config()->themeColor)->toBe(ThemeColor::Lime)
        ->and($plugin->config()->chartColor)->toBe(ThemeColor::Emerald)
        ->and($plugin->config()->font)->toBe('geist')
        ->and($plugin->config()->headingFont)->toBe('space-grotesk')
        ->and($plugin->config()->iconLibrary)->toBe(IconLibrary::Lucide)
        ->and($plugin->config()->cssMode)->toBe(CssMode::CachedAsset)
        ->and($plugin->config()->radius)->toBe(Radius::Medium)
        ->and($plugin->config()->menuColor)->toBe(MenuColor::DefaultTranslucent)
        ->and($plugin->config()->menuAccent)->toBe(MenuAccent::Bold)
        ->and($plugin->config()->sidebarVariant)->toBe(SidebarVariant::Floating)
        ->and($plugin->config()->surfaceShadow)->toBe(SurfaceShadow::Small)
        ->and($plugin->config()->defaultThemeMode)->toBe(ThemeMode::Dark)
        ->and($plugin->config()->darkMode)->toBeTrue()
        ->and($plugin->config()->tokenOverrides['light']['background'])->toBe('oklch(1 0 0)')
        ->and($plugin->config()->tokenOverrides['dark']['background'])->toBe('oklch(0 0 0)')
        ->and($plugin->config()->styleOverrides['fs-sidebar-width'])->toBe('18rem')
        ->and($plugin->config()->selectorMap['button'])->toBe('.fi-btn, .package-button');
});

it('loads publishable defaults through the Laravel service provider', function (): void {
    expect(config('filament-shadcn-theme.style'))->toBe('lyra')
        ->and(config('filament-shadcn-theme.base_color'))->toBe('taupe')
        ->and(config('filament-shadcn-theme.css_mode'))->toBe('inline')
        ->and(config('filament-shadcn-theme.radius'))->toBe('none')
        ->and(config('filament-shadcn-theme.apply_panel_font'))->toBeTrue();

    $plugin = FilamentShadcnThemePlugin::make();

    expect($plugin->config()->style)->toBe(StyleVariant::Lyra)
        ->and($plugin->config()->baseColor)->toBe(BaseColor::Taupe)
        ->and($plugin->config()->radius)->toBe(Radius::None);
});

it('hydrates configuration from snake case or camel case arrays', function (): void {
    $config = ThemeConfig::fromArray([
        'style' => 'sera',
        'base_color' => 'olive',
        'themeColor' => 'violet',
        'chart_color' => 'rose',
        'headingFont' => 'manrope',
        'icon_library' => 'tabler',
        'css_mode' => 'cached-asset',
        'menu_color' => 'inverted-translucent',
        'sidebarVariant' => 'inset',
        'surface_shadow' => 'md',
        'default_theme_mode' => 'dark',
        'dark_mode' => true,
        'force_dark_mode' => false,
        'style_overrides' => ['fs-main-padding-y' => '1rem'],
        'selectorMap' => ['card' => '.custom-card'],
    ]);

    expect($config->style)->toBe(StyleVariant::Sera)
        ->and($config->baseColor)->toBe(BaseColor::Olive)
        ->and($config->themeColor)->toBe(ThemeColor::Violet)
        ->and($config->chartColor)->toBe(ThemeColor::Rose)
        ->and($config->headingFont)->toBe('manrope')
        ->and($config->iconLibrary)->toBe(IconLibrary::Tabler)
        ->and($config->cssMode)->toBe(CssMode::CachedAsset)
        ->and($config->menuColor)->toBe(MenuColor::InvertedTranslucent)
        ->and($config->sidebarVariant)->toBe(SidebarVariant::Inset)
        ->and($config->surfaceShadow)->toBe(SurfaceShadow::Medium)
        ->and($config->defaultThemeMode)->toBe(ThemeMode::Dark)
        ->and($config->darkMode)->toBeTrue()
        ->and($config->forceDarkMode)->toBeFalse()
        ->and($config->styleOverrides['fs-main-padding-y'])->toBe('1rem')
        ->and($config->selectorMap['card'])->toBe('.custom-card');
});

it('keeps option enums aligned with the shadcn customizer surface', function (): void {
    expect(array_map(fn (BaseColor $color): string => $color->value, BaseColor::cases()))
        ->toBe(['neutral', 'stone', 'zinc', 'mauve', 'olive', 'mist', 'taupe'])
        ->and(array_map(fn (StyleVariant $style): string => $style->value, StyleVariant::cases()))
        ->toBe(['vega', 'nova', 'maia', 'lyra', 'mira', 'luma', 'sera'])
        ->and(array_map(fn (ThemeColor $color): string => $color->value, ThemeColor::cases()))
        ->toContain('taupe', 'lime', 'emerald', 'violet', 'rose', 'neutral')
        ->and(array_map(fn (CssMode $mode): string => $mode->value, CssMode::cases()))
        ->toBe(['inline', 'cached-asset'])
        ->and(array_map(fn (Radius $radius): string => $radius->value, Radius::cases()))
        ->toBe(['default', 'none', 'small', 'medium', 'large'])
        ->and(array_map(fn (IconLibrary $library): string => $library->value, IconLibrary::cases()))
        ->toBe(['lucide', 'heroicons', 'tabler', 'phosphor', 'radix'])
        ->and(array_map(fn (MenuColor $color): string => $color->value, MenuColor::cases()))
        ->toBe(['default', 'inverted', 'default-translucent', 'inverted-translucent'])
        ->and(array_map(fn (SidebarVariant $variant): string => $variant->value, SidebarVariant::cases()))
        ->toBe(['sidebar', 'floating', 'inset'])
        ->and(array_map(fn (SurfaceShadow $shadow): string => $shadow->value, SurfaceShadow::cases()))
        ->toBe(['none', 'xs', 'sm', 'md']);
});

it('declares package discovery metadata and publishable defaults', function (): void {
    $composer = json_decode(
        json: file_get_contents(__DIR__.'/../../composer.json'),
        associative: true,
        flags: JSON_THROW_ON_ERROR,
    );
    $config = require __DIR__.'/../../config/filament-shadcn-theme.php';

    expect($composer['name'])->toBe('irajul/filament-shadcn-theme')
        ->and($composer['type'])->toBe('library')
        ->and($composer['extra']['laravel']['providers'])->toContain(FilamentShadcnThemeServiceProvider::class)
        ->and($composer['scripts']['test'])->toBe('vendor/bin/pest')
        ->and($composer['require']['php'])->toBe('^8.3')
        ->and($composer['require']['illuminate/support'])->toBe('^12.0 || ^13.0')
        ->and($composer['require-dev']['orchestra/testbench'])->toBe('^10.0 || ^11.0')
        ->and($composer['require-dev']['pestphp/pest'])->toBe('^3.8 || ^4.0')
        ->and($config['style'])->toBe('lyra')
        ->and($config['base_color'])->toBe('taupe')
        ->and($config['theme_color'])->toBe('taupe')
        ->and($config['css_mode'])->toBe('inline')
        ->and($config['radius'])->toBe('none')
        ->and($config['surface_shadow'])->toBe('xs');
});
