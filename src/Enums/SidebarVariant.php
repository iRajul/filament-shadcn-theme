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
                'fs-sidebar-offset' => '0',
                'fs-sidebar-border-radius' => '0',
                'fs-sidebar-border-width' => '0 1px 0 0',
                'fs-sidebar-shadow' => 'none',
            ],
            self::Floating => [
                'fs-sidebar-offset' => '0.5rem',
                'fs-sidebar-border-radius' => 'var(--radius-xl)',
                'fs-sidebar-border-width' => '1px',
                'fs-sidebar-shadow' => 'var(--fs-surface-shadow)',
            ],
            self::Inset => [
                'fs-sidebar-offset' => '0.5rem',
                'fs-sidebar-border-radius' => 'var(--radius-xl)',
                'fs-sidebar-border-width' => '1px',
                'fs-sidebar-shadow' => 'var(--fs-surface-shadow)',
            ],
        };
    }
}
