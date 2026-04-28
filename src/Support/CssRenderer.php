<?php

namespace Irajul\FilamentShadcnTheme\Support;

use Irajul\FilamentShadcnTheme\ThemeConfig;

class CssRenderer
{
    public function __construct(
        private readonly TokenRegistry $tokens,
        private readonly PaletteRegistry $palettes,
        private readonly FontRegistry $fonts,
    ) {}

    public function render(ThemeConfig $config): string
    {
        $tokens = $this->tokens->tokens($config);
        $selectors = $this->selectors($config);

        $rootVariables = array_replace(
            [
                'radius' => $config->radius->cssValue(),
                'radius-sm' => 'calc(var(--radius) * 0.6)',
                'radius-md' => 'calc(var(--radius) * 0.8)',
                'radius-lg' => 'var(--radius)',
                'radius-xl' => 'calc(var(--radius) * 1.4)',
                'radius-2xl' => 'calc(var(--radius) * 1.8)',
                'radius-3xl' => 'calc(var(--radius) * 2.2)',
                'radius-4xl' => 'calc(var(--radius) * 2.6)',
                'font-sans' => $this->fonts->stack($config->font),
                'font-heading' => $this->fonts->headingStack($config->headingFont, $config->font),
                'fs-icon-library' => "\"{$config->iconLibrary->value}\"",
                'fs-surface-shadow' => $config->surfaceShadow->cssValue(),
                '--sidebar-width' => 'var(--fs-sidebar-width) !important',
                '--collapsed-sidebar-width' => 'var(--fs-collapsed-sidebar-width) !important',
            ],
            $config->style->variables(),
            $config->sidebarVariant->variables(),
            $config->styleOverrides,
            $this->paletteVariables($config),
            $tokens['light'],
        );

        return trim(implode("\n\n", [
            '/* data-filament-shadcn-generated */',
            "html.fi {\n{$this->declarations(array_merge(['color-scheme' => 'light'], $rootVariables))}\n}",
            "html.fi.dark {\n{$this->declarations(array_merge(['color-scheme' => 'dark'], $tokens['dark']))}\n}",
            $this->componentCss($selectors, $config),
        ]))."\n";
    }

    /**
     * @return array<string, string>
     */
    private function paletteVariables(ThemeConfig $config): array
    {
        $palettes = $this->palettes->filamentColors($config);
        $variables = [];

        foreach ($palettes as $name => $palette) {
            foreach ($palette as $shade => $value) {
                $variables["{$name}-{$shade}"] = $value;
            }
        }

        return $variables;
    }

    /**
     * @return array<string, string>
     */
    private function selectors(ThemeConfig $config): array
    {
        return array_replace([
            'shell' => 'html.fi, html.fi body, .fi-body',
            'layout' => '.fi-layout, .fi-main, .fi-simple-main',
            'main' => '.fi-main',
            'page' => '.fi-page, .fi-page-content, .fi-page-main, .fi-sc',
            'pageHeaderMain' => '.fi-page-header-main-ctn',
            'heading' => '.fi-header-heading, .fi-section-header-heading, .fi-modal-heading, .fi-ta-header-heading',
            'subtleText' => '.fi-section-header-description, .fi-modal-description, .fi-wi-stats-overview-stat-label, .fi-wi-stats-overview-stat-description, .fi-ta-empty-state-description',
            'sidebar' => '.fi-sidebar',
            'topbar' => '.fi-topbar, .fi-topbar-ctn',
            'sidebarNav' => '.fi-sidebar-nav, .fi-sidebar-nav-groups',
            'sidebarGroup' => '.fi-sidebar-group',
            'sidebarGroupButton' => '.fi-sidebar-group-btn, .fi-sidebar-group-dropdown-trigger-btn, .fi-sidebar-database-notifications-btn',
            'sidebarGroupLabel' => '.fi-sidebar-group-label',
            'sidebarGroupItems' => '.fi-sidebar-group-items, .fi-sidebar-sub-group-items',
            'sidebarItem' => '.fi-sidebar-item-btn, .fi-topbar-item-btn',
            'sidebarItemActive' => '.fi-sidebar-item.fi-active > .fi-sidebar-item-btn, .fi-topbar-item.fi-active > .fi-topbar-item-btn',
            'card' => '.fi-section:not(.fi-section-not-contained), .fi-ta-ctn, .fi-in-entry-wrp, .fi-wi-widget:not(.fi-wi-stats-overview), .fi-wi-chart, .fi-wi-table, .fi-wi-stats-overview-stat, .fi-fo-repeater-item, .fi-fieldset',
            'cardHeader' => '.fi-section-header, .fi-ta-header-ctn, .fi-modal-header, .fi-fo-repeater-item-header',
            'cardContent' => '.fi-section-content-ctn, .fi-section-content, .fi-ta-content-ctn, .fi-modal-content',
            'cardFooter' => '.fi-section-footer, .fi-ta-footer-ctn, .fi-modal-footer',
            'inputWrapper' => '.fi-input-wrp, .fi-select-input, .fi-tags-input, .fi-fo-rich-editor, .fi-fo-rich-editor-toolbar, .fi-fo-rich-editor-panel',
            'input' => '.fi-input, .fi-select-input, .fi-textarea, .fi-tags-input input',
            'fieldLabel' => '.fi-fo-field-label, .fi-input-wrp-label, .fi-ta-filters-heading',
            'fieldError' => '.fi-fo-field-wrp-error-message, .fi-fo-field-label-required-mark',
            'button' => '.fi-btn',
            'primaryButton' => '.fi-btn.fi-color-primary, .fi-icon-btn.fi-color-primary',
            'secondaryButton' => '.fi-btn.fi-color-gray, .fi-icon-btn.fi-color-gray',
            'outlinedButton' => '.fi-btn.fi-outlined, .fi-icon-btn.fi-outlined, .fi-btn.fi-color-gray, .fi-icon-btn.fi-color-gray',
            'iconButton' => '.fi-icon-btn',
            'buttonGroup' => '.fi-btn-group',
            'dropdown' => '.fi-dropdown-panel, .fi-modal-window, .fi-select-dropdown, .fi-date-time-picker-panel',
            'dropdownList' => '.fi-dropdown-list, .fi-dropdown-header',
            'dropdownItem' => '.fi-dropdown-list-item',
            'table' => '.fi-ta-table, .fi-ta-content-ctn',
            'tableContainer' => '.fi-ta-ctn',
            'tableHeader' => '.fi-ta-header, .fi-ta-header-ctn, .fi-ta-header-toolbar',
            'tableHeaderButton' => '.fi-ta-header-cell-sort-btn',
            'tableRow' => '.fi-ta-row',
            'tableCell' => '.fi-ta-cell, .fi-ta-header-cell, .fi-ta-actions-header-cell, .fi-ta-selection-cell',
            'tableEmptyState' => '.fi-ta-empty-state, .fi-ta-empty-state-icon-bg',
            'tabs' => '.fi-tabs',
            'tabItem' => '.fi-tabs-item',
            'badge' => '.fi-badge',
            'pagination' => '.fi-pagination-item-btn',
            'toggle' => '.fi-toggle, .fi-checkbox-input, .fi-radio-input',
        ], $config->selectorMap);
    }

    /**
     * @param  array<string, string>  $selectors
     */
    private function componentCss(array $selectors, ThemeConfig $config): string
    {
        $translucentMenu = $config->menuColor->isTranslucent()
            ? 'backdrop-filter: saturate(180%) blur(16px);'
            : '';

        $sidebarGroupButtonHover = $this->withSuffix($selectors['sidebarGroupButton'], ':hover');
        $sidebarItemHover = $this->withSuffix($selectors['sidebarItem'], ':hover');
        $inputWrapperFocus = $this->withSuffix($selectors['inputWrapper'], ':focus-within');
        $darkInputWrapper = $this->scoped('html.fi.dark', $selectors['inputWrapper']);
        $primaryButtonHover = $this->withSuffix($selectors['primaryButton'], ':hover');
        $secondaryButtonHover = $this->withSuffix($selectors['secondaryButton'], ':hover');
        $outlinedButtonHover = $this->withSuffix($selectors['outlinedButton'], ':hover');
        $dropdownItemHover = implode(",\n", [
            $this->withSuffix($selectors['dropdownItem'], ':hover'),
            $this->withSuffix($selectors['dropdownItem'], '[data-active="true"]'),
            $this->withSuffix($selectors['dropdownItem'], '[aria-selected="true"]'),
        ]);
        $tableHeaderButtonHover = $this->withSuffix($selectors['tableHeaderButton'], ':hover');
        $tableRowHover = $this->withSuffix($selectors['tableRow'], ':hover');
        $tabItemHover = implode(",\n", [
            $this->withSuffix($selectors['tabItem'], ':hover'),
            $this->withSuffix($selectors['tabItem'], '.fi-active'),
        ]);
        $toggleChecked = implode(",\n", [
            $this->withSuffix($selectors['toggle'], ':checked'),
            $this->withSuffix($selectors['toggle'], ':indeterminate'),
            $this->withSuffix($selectors['toggle'], '[aria-checked="true"]'),
        ]);
        $focusableFocus = implode(",\n", [
            $this->withSuffix('.fi-checkbox-input, .fi-radio-input', ':focus-visible'),
            $this->withSuffix($selectors['button'], ':focus-visible'),
            $this->withSuffix($selectors['iconButton'], ':focus-visible'),
            $this->withSuffix($selectors['pagination'], ':focus-visible'),
            $this->withSuffix($selectors['dropdownItem'], ':focus-visible'),
            $this->withSuffix($selectors['tabItem'], ':focus-visible'),
        ]);

        return <<<CSS
{$selectors['shell']} {
    background: var(--background) !important;
    color: var(--foreground);
    font-family: var(--font-sans);
    line-height: var(--fs-line-height);
    --sidebar-width: var(--fs-sidebar-width) !important;
    --collapsed-sidebar-width: var(--fs-collapsed-sidebar-width) !important;
}

.fi-body {
    font-size: var(--fs-body-font-size);
}

{$selectors['layout']} {
    background: var(--background) !important;
    color: var(--foreground);
}

{$selectors['main']} {
    padding: var(--fs-main-padding-y) var(--fs-main-padding-x) !important;
}

{$selectors['page']} {
    gap: var(--fs-page-gap) !important;
}

{$selectors['pageHeaderMain']} {
    gap: var(--fs-page-header-main-gap) !important;
    padding: var(--fs-page-header-main-padding-y) 0 !important;
}

{$selectors['heading']} {
    color: var(--foreground);
    font-family: var(--font-heading);
    letter-spacing: 0;
    font-weight: 600;
    line-height: 1.2;
}

{$selectors['subtleText']} {
    color: var(--muted-foreground) !important;
}

{$selectors['sidebar']} {
    {$translucentMenu}
    background: var(--sidebar) !important;
    border-color: var(--sidebar-border) !important;
    border-radius: var(--fs-sidebar-border-radius) !important;
    border-style: solid !important;
    border-width: var(--fs-sidebar-border-width) !important;
    color: var(--sidebar-foreground);
    box-shadow: var(--fs-sidebar-shadow) !important;
    margin: var(--fs-sidebar-offset) !important;
    height: calc(100svh - (var(--fs-sidebar-offset) * 2)) !important;
    overflow: hidden;
}

{$selectors['topbar']} {
    background: color-mix(in oklch, var(--background) 86%, transparent) !important;
    border-bottom: var(--fs-shell-divider-width) solid var(--fs-shell-divider-color) !important;
    border-color: var(--fs-shell-divider-color) !important;
    color: var(--foreground);
    min-height: var(--fs-topbar-height);
    position: relative;
    backdrop-filter: saturate(180%) blur(14px);
    box-shadow: none !important;
}

.fi-topbar {
    padding-inline: var(--fs-topbar-padding-x) !important;
}

.fi-topbar-ctn {
    border-bottom: 0 !important;
}

.fi-topbar > * {
    position: relative;
    z-index: 1;
}

.fi-topbar-start {
    align-items: center;
    display: flex;
    gap: var(--fs-topbar-start-gap) !important;
}

.fi-topbar-start > a,
.fi-topbar-start > .fi-logo,
.fi-topbar-start > .fi-brand {
    order: var(--fs-topbar-logo-order);
}

.fi-topbar-collapse-sidebar-btn-ctn {
    display: none !important;
    margin-inline-start: var(--fs-topbar-collapse-button-margin-start) !important;
    order: var(--fs-topbar-collapse-button-order);
}

@media (min-width: 1024px) {
    .fi-body-has-topbar .fi-main-sidebar {
        height: var(--fs-sidebar-topbar-height) !important;
        min-height: var(--fs-sidebar-topbar-height) !important;
    }

    .fi-topbar-collapse-sidebar-btn-ctn {
        display: block !important;
    }

    .fi-topbar::before {
        background: var(--fs-shell-divider-color);
        content: "";
        inset-block: 0;
        inset-inline-start: var(--fs-topbar-sidebar-divider-left);
        pointer-events: none;
        position: absolute;
        width: var(--fs-shell-divider-width);
        z-index: 0;
    }

    html.fi:has(.fi-main-sidebar.fi-sidebar-open) .fi-topbar {
        --fs-topbar-sidebar-divider-left: var(--fs-sidebar-width);
    }

    html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-topbar {
        --fs-topbar-sidebar-divider-left: var(--fs-collapsed-sidebar-width);
    }

    html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-topbar-start {
        gap: var(--fs-collapsed-topbar-start-gap) !important;
        justify-content: var(--fs-collapsed-topbar-start-justify-content);
        margin-inline-start: var(--fs-collapsed-topbar-start-margin-start) !important;
        min-width: var(--fs-collapsed-topbar-start-width);
        width: var(--fs-collapsed-topbar-start-width);
    }

    html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-topbar-start > a,
    html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-topbar-start > .fi-logo,
    html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-topbar-start > .fi-brand {
        display: var(--fs-collapsed-topbar-brand-display) !important;
    }

    html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-topbar-collapse-sidebar-btn-ctn {
        margin-inline-start: var(--fs-collapsed-topbar-collapse-button-margin-start) !important;
    }

    html.fi:has(.fi-main-sidebar:not(.fi-sidebar-open)) .fi-main-sidebar {
        height: var(--fs-collapsed-sidebar-height) !important;
        top: var(--fs-collapsed-sidebar-top) !important;
    }

    .fi-body-has-sidebar-collapsible-on-desktop .fi-topbar > .fi-topbar-open-sidebar-btn,
    .fi-body-has-sidebar-collapsible-on-desktop .fi-topbar > .fi-topbar-close-sidebar-btn,
    .fi-body-has-sidebar-fully-collapsible-on-desktop .fi-topbar > .fi-topbar-open-sidebar-btn,
    .fi-body-has-sidebar-fully-collapsible-on-desktop .fi-topbar > .fi-topbar-close-sidebar-btn {
        display: var(--fs-topbar-mobile-sidebar-button-desktop-display) !important;
    }
}

{$selectors['sidebarNav']} {
    gap: var(--fs-sidebar-group-gap) !important;
}

.fi-sidebar-nav {
    padding: var(--fs-sidebar-padding) !important;
}

.fi-sidebar-nav-groups {
    margin-inline: 0 !important;
    gap: var(--fs-sidebar-section-gap) !important;
    width: 100% !important;
}

.fi-sidebar-header-ctn,
.fi-sidebar-header {
    min-height: var(--fs-sidebar-header-height) !important;
    border-color: var(--sidebar-border) !important;
}

.fi-body-has-topbar .fi-sidebar-header-ctn {
    display: var(--fs-sidebar-header-with-topbar-display) !important;
    height: 0 !important;
    min-height: 0 !important;
    overflow: hidden !important;
}

{$selectors['sidebarGroup']} {
    padding: var(--fs-sidebar-group-padding) 0 !important;
}

.fi-sidebar-group.fi-collapsed .fi-sidebar-group-items,
.fi-sidebar-group.fi-collapsed .fi-sidebar-sub-group-items {
    display: none !important;
}

{$selectors['sidebarGroupButton']} {
    min-height: var(--fs-sidebar-label-height);
    border-radius: var(--fs-sidebar-item-radius) !important;
    color: color-mix(in oklch, var(--sidebar-foreground) 70%, transparent) !important;
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0;
    line-height: 1.25;
    min-height: var(--fs-sidebar-item-height);
    padding-block: var(--fs-sidebar-item-padding-y) !important;
    padding-inline: var(--fs-sidebar-item-padding-x) !important;
}

{$sidebarGroupButtonHover},
.fi-sidebar-group.fi-active > .fi-sidebar-group-btn {
    background: var(--sidebar-accent) !important;
    color: var(--sidebar-accent-foreground) !important;
}

{$selectors['sidebarGroupLabel']} {
    color: color-mix(in oklch, var(--sidebar-foreground) 70%, transparent) !important;
    font-size: 0.75rem;
    font-weight: 500;
}

{$selectors['sidebarGroupItems']} {
    gap: var(--fs-sidebar-item-gap) !important;
}

{$selectors['sidebarItem']} {
    min-height: var(--fs-sidebar-item-height);
    border-radius: var(--fs-sidebar-item-radius) !important;
    background: transparent !important;
    color: var(--sidebar-foreground) !important;
    font-size: var(--fs-font-size-sm);
    font-weight: var(--fs-font-weight-medium);
    gap: 0.5rem;
    line-height: 1.25;
    padding-block: var(--fs-sidebar-item-padding-y) !important;
    padding-inline: var(--fs-sidebar-item-padding-x) !important;
    transition: background-color var(--fs-transition-duration), color var(--fs-transition-duration);
}

.fi-sidebar-item-icon,
.fi-sidebar-group-btn > svg,
.fi-topbar svg {
    color: color-mix(in oklch, var(--sidebar-foreground) 62%, transparent) !important;
    flex-shrink: 0;
    height: var(--fs-icon-size) !important;
    width: var(--fs-icon-size) !important;
}

.fi-sidebar-item-label,
.fi-sidebar-group-label {
    line-height: 1.25 !important;
}

{$sidebarItemHover},
{$selectors['sidebarItemActive']} {
    background: var(--sidebar-accent) !important;
    color: var(--sidebar-accent-foreground) !important;
}

.fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon,
.fi-topbar-item.fi-active > .fi-topbar-item-btn .fi-sidebar-item-icon {
    color: var(--sidebar-accent-foreground) !important;
}

.fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label,
.fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-badge-ctn,
.fi-topbar-item.fi-active > .fi-topbar-item-btn .fi-sidebar-item-label {
    color: var(--sidebar-accent-foreground) !important;
}

.fi-sidebar-item-grouped-border,
.fi-sidebar-item-grouped-border-part {
    display: var(--fs-sidebar-group-rail-display) !important;
    border-color: var(--sidebar-border) !important;
    background: var(--sidebar-border) !important;
}

{$selectors['card']} {
    background: var(--card) !important;
    border: 1px solid var(--border) !important;
    border-radius: var(--fs-card-radius) !important;
    color: var(--card-foreground);
    box-shadow: var(--fs-surface-shadow) !important;
    overflow: hidden;
}

{$selectors['cardHeader']} {
    border-color: var(--border) !important;
    gap: 0.5rem !important;
    padding: var(--fs-card-padding-y) var(--fs-card-padding-x) !important;
}

{$selectors['cardContent']} {
    border-color: var(--border) !important;
    padding: var(--fs-card-padding-y) var(--fs-card-padding-x) !important;
}

{$selectors['cardFooter']} {
    background: color-mix(in oklch, var(--muted) 50%, transparent) !important;
    border-color: var(--border) !important;
    padding: var(--fs-section-padding) var(--fs-card-padding-x) !important;
}

.fi-wi-stats-overview .fi-grid {
    gap: var(--fs-stat-gap) !important;
}

.fi-section.fi-section-not-contained {
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

.fi-section.fi-section-not-contained > .fi-section-content-ctn,
.fi-section.fi-section-not-contained > .fi-section-content-ctn > .fi-section-content {
    padding: 0 !important;
}

.fi-loading-section {
    min-height: var(--fs-loading-section-min-height);
    position: relative;
}

.fi-loading-section::before,
.fi-loading-section::after {
    content: "";
    position: absolute;
    left: var(--fs-card-padding-x);
    height: var(--fs-skeleton-line-height);
    border-radius: var(--fs-skeleton-line-radius);
    background: color-mix(in oklch, var(--muted) 85%, var(--foreground));
    opacity: var(--fs-skeleton-opacity);
    animation: filament-shadcn-skeleton-pulse var(--fs-skeleton-animation-duration) ease-in-out infinite;
}

.fi-loading-section::before {
    top: var(--fs-card-padding-y);
    width: var(--fs-skeleton-title-width);
}

.fi-loading-section::after {
    top: calc(var(--fs-card-padding-y) + var(--fs-skeleton-line-gap));
    width: var(--fs-skeleton-body-width);
}

@keyframes filament-shadcn-skeleton-pulse {
    50% {
        opacity: var(--fs-skeleton-active-opacity);
    }
}

@media (prefers-reduced-motion: reduce) {
    .fi-loading-section::before,
    .fi-loading-section::after {
        animation: none;
    }
}

.fi-wi-stats-overview-stat {
    padding: var(--fs-card-padding-y) var(--fs-card-padding-x) !important;
}

.fi-wi-stats-overview-stat-value {
    color: var(--foreground) !important;
    font-size: 1.875rem !important;
    font-weight: 600 !important;
    letter-spacing: 0;
    line-height: 1.15;
}

.fi-wi-stats-overview-stat-label-ctn {
    gap: 0.5rem !important;
}

.fi-wi-stats-overview-stat svg {
    color: var(--muted-foreground) !important;
    width: var(--fs-icon-size);
    height: var(--fs-icon-size);
}

{$selectors['inputWrapper']} {
    min-height: var(--fs-control-height);
    background: var(--fs-input-background) !important;
    border: 1px solid var(--input) !important;
    border-radius: var(--radius-md) !important;
    color: var(--foreground);
    box-shadow: var(--fs-input-shadow) !important;
    transition: border-color var(--fs-transition-duration), box-shadow var(--fs-transition-duration), background-color var(--fs-transition-duration);
}

{$darkInputWrapper} {
    background: var(--fs-dark-input-background) !important;
}

{$inputWrapperFocus} {
    border-color: var(--ring) !important;
    box-shadow: 0 0 0 var(--fs-focus-ring-width) color-mix(in oklch, var(--ring) 50%, transparent) !important;
}

{$selectors['input']} {
    color: var(--foreground) !important;
    font-size: var(--fs-font-size-sm);
}

{$selectors['input']}::placeholder {
    color: var(--muted-foreground) !important;
}

{$selectors['fieldLabel']} {
    color: var(--foreground) !important;
    font-size: var(--fs-font-size-sm);
    font-weight: 500;
}

{$selectors['fieldError']} {
    color: var(--destructive) !important;
}

{$selectors['button']},
{$selectors['iconButton']},
{$selectors['pagination']} {
    min-height: var(--fs-control-height);
    align-items: center;
    border-radius: var(--radius-md) !important;
    box-shadow: none !important;
    display: inline-flex;
    gap: 0.5rem;
    font-weight: var(--fs-font-weight-medium);
    justify-content: center;
    padding-inline: var(--fs-control-padding-x) !important;
    transition: background-color var(--fs-transition-duration), border-color var(--fs-transition-duration), color var(--fs-transition-duration), box-shadow var(--fs-transition-duration);
    white-space: nowrap;
}

{$selectors['iconButton']} {
    width: var(--fs-control-height);
    padding-inline: 0 !important;
}

{$selectors['primaryButton']} {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: var(--primary-foreground) !important;
}

{$primaryButtonHover} {
    background: color-mix(in oklch, var(--primary) 88%, var(--foreground)) !important;
    border-color: color-mix(in oklch, var(--primary) 88%, var(--foreground)) !important;
}

{$selectors['secondaryButton']} {
    background: var(--secondary) !important;
    border-color: transparent !important;
    color: var(--secondary-foreground) !important;
}

{$secondaryButtonHover} {
    background: color-mix(in oklch, var(--secondary) 80%, var(--foreground)) !important;
    color: var(--secondary-foreground) !important;
}

{$selectors['outlinedButton']} {
    background: var(--background) !important;
    border-color: var(--border) !important;
    color: var(--foreground) !important;
}

{$outlinedButtonHover} {
    background: var(--accent) !important;
    color: var(--accent-foreground) !important;
}

{$selectors['buttonGroup']} {
    border-color: var(--border) !important;
    border-radius: var(--radius-md) !important;
    box-shadow: none !important;
}

{$selectors['dropdown']} {
    background: var(--popover) !important;
    border: 1px solid var(--border) !important;
    border-radius: var(--radius-lg) !important;
    color: var(--popover-foreground);
    box-shadow: var(--fs-surface-shadow) !important;
}

{$selectors['dropdownList']} {
    color: var(--popover-foreground) !important;
    padding: var(--fs-dropdown-padding) !important;
}

{$selectors['dropdownItem']} {
    min-height: var(--fs-dropdown-item-height);
    border-radius: var(--fs-dropdown-item-radius) !important;
    color: var(--popover-foreground) !important;
    font-size: var(--fs-font-size-sm);
    padding-inline: 0.5rem !important;
}

{$dropdownItemHover} {
    background: var(--accent) !important;
    color: var(--accent-foreground) !important;
}

.fi-modal-close-overlay {
    background: oklch(0 0 0 / 0.55) !important;
}

.fi-modal-icon-bg {
    background: var(--muted) !important;
    color: var(--muted-foreground) !important;
}

{$selectors['table']} {
    background: var(--card);
    color: var(--card-foreground);
    font-size: var(--fs-font-size-sm);
}

{$selectors['tableContainer']} {
    box-shadow: var(--fs-surface-shadow) !important;
}

.fi-ta-header-ctn {
    padding: 0 !important;
}

.fi-ta-header-toolbar {
    border-bottom: 1px solid var(--border) !important;
    padding: var(--fs-table-toolbar-padding-y) var(--fs-table-toolbar-padding-x) !important;
}

.fi-ta-content-ctn {
    overflow-x: auto;
    padding: var(--fs-table-content-padding) !important;
}

.fi-ta-content,
.fi-ta-table {
    width: 100%;
}

.fi-ta-table {
    border-collapse: collapse;
    border-spacing: 0;
}

{$selectors['tableHeader']} {
    background: var(--card) !important;
    border-color: var(--border) !important;
}

.fi-ta-table thead,
.fi-ta-table thead tr {
    background: var(--fs-table-header-background) !important;
}

.fi-ta-table thead tr,
.fi-ta-table tbody tr {
    border-bottom: 1px solid var(--border) !important;
}

.fi-ta-table thead .fi-ta-header-cell,
.fi-ta-table thead .fi-ta-actions-header-cell,
.fi-ta-table thead .fi-ta-selection-cell {
    color: var(--muted-foreground) !important;
    font-weight: 500;
    height: var(--fs-table-header-cell-height);
}

{$selectors['tableHeaderButton']} {
    color: var(--muted-foreground) !important;
    border-radius: var(--radius-sm) !important;
}

{$tableHeaderButtonHover} {
    background: var(--accent) !important;
    color: var(--accent-foreground) !important;
}

{$tableRowHover} {
    background: var(--fs-table-row-hover-background) !important;
    color: var(--accent-foreground);
}

.fi-ta-row.fi-selected,
.fi-ta-row[aria-selected="true"],
.fi-ta-row[data-selected="true"],
.fi-ta-row:has(.fi-ta-record-checkbox:checked) {
    background: var(--fs-table-row-selected-background) !important;
}

{$selectors['tableCell']} {
    border-color: var(--border) !important;
    height: var(--fs-table-row-height);
    min-height: var(--fs-table-row-height);
    padding: 0 !important;
    vertical-align: middle;
}

.fi-ta-selection-cell {
    padding-inline: var(--fs-table-selection-cell-padding-left) var(--fs-table-selection-cell-padding-right) !important;
    width: var(--fs-table-selection-cell-width);
}

.fi-ta-selection-cell .fi-checkbox-input {
    transform: translateY(1px);
}

.fi-ta-table thead .fi-ta-header-cell:not(.fi-ta-selection-cell):first-child,
.fi-ta-table tbody .fi-ta-cell:not(.fi-ta-selection-cell):first-child {
    padding-inline-start: var(--fs-table-edge-padding-x) !important;
}

.fi-ta-table .fi-ta-col {
    align-items: center;
    min-height: var(--fs-table-row-height);
}

.fi-ta-table .fi-ta-col > .fi-ta-text,
.fi-ta-table .fi-ta-col > .fi-ta-icon,
.fi-ta-table .fi-ta-col > .fi-ta-image {
    padding: var(--fs-table-cell-padding-y) var(--fs-table-cell-padding-x) !important;
}

.fi-ta-table .fi-ta-cell:last-child {
    padding-inline-end: var(--fs-table-actions-padding-x) !important;
}

.fi-ta-table .fi-ta-actions {
    gap: var(--fs-table-actions-gap) !important;
    justify-content: flex-end;
    white-space: nowrap;
}

.fi-ta-table .fi-ta-actions .fi-icon-btn.fi-ac-icon-btn-group {
    background: transparent !important;
    border-color: transparent !important;
    color: var(--muted-foreground) !important;
    margin: 0 !important;
    min-height: var(--fs-control-height-sm);
    width: var(--fs-control-height-sm);
}

.fi-ta-table .fi-ta-actions .fi-icon-btn.fi-ac-icon-btn-group:hover {
    background: var(--accent) !important;
    color: var(--accent-foreground) !important;
}

.fi-ta-table .fi-ta-actions .fi-btn.fi-ac-btn-group {
    background: color-mix(in oklch, var(--secondary) 68%, transparent) !important;
    border: 1px solid var(--border) !important;
    color: var(--foreground) !important;
    min-height: var(--fs-control-height-sm);
    padding-inline: 0.625rem !important;
}

.fi-ta-table .fi-ta-actions .fi-btn.fi-ac-btn-group:hover {
    background: var(--accent) !important;
    color: var(--accent-foreground) !important;
}

.fi-ta-table .fi-ta-actions .fi-link {
    min-height: var(--fs-control-height-sm);
}

.fi-pagination {
    align-items: center !important;
    border-top: 1px solid var(--border) !important;
    display: flex !important;
    flex-wrap: nowrap !important;
    gap: var(--fs-pagination-gap) !important;
    justify-content: space-between !important;
    min-height: var(--fs-pagination-height);
    padding: var(--fs-pagination-padding-y) var(--fs-pagination-padding-x) !important;
}

.fi-pagination-overview {
    color: var(--muted-foreground) !important;
    display: inline-flex !important;
    font-size: var(--fs-font-size-sm);
    font-weight: 500;
    order: 1;
    white-space: nowrap;
}

.fi-pagination-records-per-page-select-ctn {
    display: flex !important;
    justify-content: center;
    margin-inline: auto;
    order: 2;
}

.fi-pagination-records-per-page-select:not(.fi-compact) {
    display: inline-flex !important;
}

.fi-pagination-records-per-page-select.fi-compact {
    display: none !important;
}

.fi-pagination-items {
    background: var(--background) !important;
    border: 1px solid var(--border) !important;
    border-radius: var(--radius-md) !important;
    box-shadow: none !important;
    display: flex !important;
    flex-shrink: 0;
    order: 3;
}

.fi-pagination-previous-btn,
.fi-pagination-next-btn {
    display: none !important;
}

.fi-pagination-item {
    border-color: var(--border) !important;
}

@media (max-width: 640px) {
    .fi-pagination {
        justify-content: space-between !important;
    }

    .fi-pagination-overview,
    .fi-pagination-items {
        display: none !important;
    }

    .fi-pagination-records-per-page-select-ctn {
        margin-inline: 0;
    }

    .fi-pagination-records-per-page-select:not(.fi-compact) {
        display: none !important;
    }

    .fi-pagination-records-per-page-select.fi-compact {
        display: inline-flex !important;
    }

    .fi-pagination-previous-btn,
    .fi-pagination-next-btn {
        display: inline-flex !important;
    }
}

{$selectors['tableEmptyState']} {
    background: var(--muted) !important;
    color: var(--muted-foreground) !important;
}

{$selectors['tabs']} {
    background: var(--muted) !important;
    border-radius: var(--radius-md) !important;
    border: 1px solid var(--border) !important;
    box-shadow: none !important;
    padding: 0.25rem !important;
}

{$selectors['tabItem']} {
    border-radius: var(--radius-sm) !important;
    color: var(--muted-foreground) !important;
    min-height: var(--fs-control-height-sm);
}

{$tabItemHover} {
    background: var(--background) !important;
    color: var(--foreground) !important;
    box-shadow: var(--fs-input-shadow) !important;
}

{$selectors['badge']} {
    border: 1px solid color-mix(in oklch, var(--border) 70%, transparent) !important;
    border-radius: var(--fs-badge-radius) !important;
    background: color-mix(in oklch, var(--secondary) 70%, transparent) !important;
    color: var(--secondary-foreground) !important;
    box-shadow: none !important;
    font-weight: 500;
}

.fi-badge.fi-color-primary {
    background: color-mix(in oklch, var(--primary) 14%, transparent) !important;
    border-color: color-mix(in oklch, var(--primary) 28%, transparent) !important;
    color: var(--primary) !important;
}

.fi-badge.fi-color-success,
.fi-badge.fi-color-info,
.fi-badge.fi-color-warning,
.fi-badge.fi-color-danger {
    background: color-mix(in oklch, var(--color-400, var(--muted)) 14%, transparent) !important;
    border-color: color-mix(in oklch, var(--color-400, var(--border)) 28%, transparent) !important;
    color: var(--color-400, var(--foreground)) !important;
}

.fi-checkbox-input,
.fi-radio-input {
    accent-color: var(--primary);
    background-color: var(--fs-checkbox-background) !important;
    background-image: none !important;
    border: 1px solid var(--fs-checkbox-border-color) !important;
    border-radius: var(--fs-checkbox-radius) !important;
    box-shadow: var(--fs-input-shadow) !important;
    color: var(--fs-checkbox-checked-color) !important;
    height: var(--fs-checkbox-size);
    width: var(--fs-checkbox-size);
}

.fi-checkbox-input:checked {
    background-image: var(--fs-checkbox-checked-icon) !important;
    background-position: center;
    background-repeat: no-repeat;
    background-size: 100% 100%;
}

.fi-checkbox-input:indeterminate {
    background-image: var(--fs-checkbox-indeterminate-icon) !important;
    background-position: center;
    background-repeat: no-repeat;
    background-size: 100% 100%;
}

html.fi.dark .fi-checkbox-input:checked {
    background-image: var(--fs-dark-checkbox-checked-icon) !important;
}

html.fi.dark .fi-checkbox-input:indeterminate {
    background-image: var(--fs-dark-checkbox-indeterminate-icon) !important;
}

{$toggleChecked} {
    background-color: var(--fs-checkbox-checked-background) !important;
    border-color: var(--fs-checkbox-checked-border-color) !important;
    color: var(--fs-checkbox-checked-color) !important;
}

{$focusableFocus} {
    border-color: var(--ring) !important;
    box-shadow: 0 0 0 var(--fs-focus-ring-width) color-mix(in oklch, var(--ring) 50%, transparent) !important;
    outline: none !important;
}
CSS;
    }

    /**
     * @param  array<string, string>  $variables
     */
    private function declarations(array $variables): string
    {
        return collect($variables)
            ->map(fn (string $value, string $name): string => '    '.$this->variableName($name).": {$value};")
            ->implode("\n");
    }

    private function variableName(string $name): string
    {
        if (str_starts_with($name, '--')) {
            return $name;
        }

        if ($name === 'color-scheme') {
            return $name;
        }

        return "--{$name}";
    }

    private function withSuffix(string $selectors, string $suffix): string
    {
        return collect(explode(',', $selectors))
            ->map(fn (string $selector): string => trim($selector).$suffix)
            ->implode(",\n");
    }

    private function scoped(string $scope, string $selectors): string
    {
        return collect(explode(',', $selectors))
            ->map(fn (string $selector): string => "{$scope} ".trim($selector))
            ->implode(",\n");
    }
}
