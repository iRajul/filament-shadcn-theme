<?php

namespace Irajul\FilamentShadcnTheme\Enums;

enum Radius: string
{
    case Default = 'default';
    case None = 'none';
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';

    public function cssValue(): string
    {
        return match ($this) {
            self::Default, self::Medium => '0.625rem',
            self::None => '0',
            self::Small => '0.45rem',
            self::Large => '0.875rem',
        };
    }
}
