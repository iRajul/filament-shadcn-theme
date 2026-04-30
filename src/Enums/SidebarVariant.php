<?php

namespace Irajul\FilamentShadcnTheme\Enums;

enum SidebarVariant: string
{
    case Sidebar = 'sidebar';
    case Floating = 'floating';
    case Inset = 'inset';

    /**
     * @return array<string, string>
     */
    public function variables(): array
    {
        return match ($this) {
            self::Sidebar => [
                'fs-layout-desktop-height' => 'auto',
                'fs-layout-desktop-min-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-sidebar-rail-display' => 'block',
                'fs-sidebar-rail-width' => 'var(--fs-sidebar-width)',
                'fs-sidebar-offset' => '0',
                'fs-sidebar-border-radius' => '0',
                'fs-sidebar-border-width' => '0 1px 0 0',
                'fs-sidebar-shadow' => 'none',
                'fs-sidebar-desktop-align-self' => 'stretch',
                'fs-sidebar-desktop-position' => 'relative',
                'fs-sidebar-desktop-top' => 'auto',
                'fs-sidebar-desktop-bottom' => 'auto',
                'fs-sidebar-desktop-height' => 'auto',
                'fs-sidebar-desktop-min-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-collapsed-sidebar-top' => 'auto',
                'fs-collapsed-sidebar-height' => 'auto',
            ],
            self::Floating => [
                'fs-layout-desktop-height' => '100%',
                'fs-layout-desktop-min-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-sidebar-rail-display' => 'none',
                'fs-sidebar-rail-width' => 'var(--fs-sidebar-width)',
                'fs-sidebar-offset' => '0.5rem',
                'fs-sidebar-border-radius' => 'var(--radius-xl)',
                'fs-sidebar-border-width' => '1px',
                'fs-sidebar-shadow' => 'var(--fs-surface-shadow)',
                'fs-sidebar-desktop-align-self' => 'flex-start',
                'fs-sidebar-desktop-position' => 'sticky',
                'fs-sidebar-desktop-top' => 'var(--fs-topbar-height)',
                'fs-sidebar-desktop-bottom' => 'auto',
                'fs-sidebar-desktop-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-sidebar-desktop-min-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-collapsed-sidebar-top' => 'var(--fs-topbar-height)',
                'fs-collapsed-sidebar-height' => 'calc(100svh - var(--fs-topbar-height))',
            ],
            self::Inset => [
                'fs-layout-desktop-height' => '100%',
                'fs-layout-desktop-min-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-sidebar-rail-display' => 'none',
                'fs-sidebar-rail-width' => 'var(--fs-sidebar-width)',
                'fs-sidebar-offset' => '0.5rem',
                'fs-sidebar-border-radius' => 'var(--radius-xl)',
                'fs-sidebar-border-width' => '1px',
                'fs-sidebar-shadow' => 'var(--fs-surface-shadow)',
                'fs-sidebar-desktop-align-self' => 'flex-start',
                'fs-sidebar-desktop-position' => 'sticky',
                'fs-sidebar-desktop-top' => 'var(--fs-topbar-height)',
                'fs-sidebar-desktop-bottom' => 'auto',
                'fs-sidebar-desktop-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-sidebar-desktop-min-height' => 'var(--fs-sidebar-topbar-height)',
                'fs-collapsed-sidebar-top' => 'var(--fs-topbar-height)',
                'fs-collapsed-sidebar-height' => 'calc(100svh - var(--fs-topbar-height))',
            ],
        };
    }
}
