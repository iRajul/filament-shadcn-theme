<?php

namespace Irajul\FilamentShadcnTheme\Enums;

enum SurfaceShadow: string
{
    case None = 'none';
    case ExtraSmall = 'xs';
    case Small = 'sm';
    case Medium = 'md';

    public function cssValue(): string
    {
        return match ($this) {
            self::None => 'none',
            self::ExtraSmall => '0 1px 2px 0 oklch(0 0 0 / 0.05)',
            self::Small => '0 1px 3px 0 oklch(0 0 0 / 0.1), 0 1px 2px -1px oklch(0 0 0 / 0.1)',
            self::Medium => '0 4px 6px -1px oklch(0 0 0 / 0.1), 0 2px 4px -2px oklch(0 0 0 / 0.1)',
        };
    }
}
