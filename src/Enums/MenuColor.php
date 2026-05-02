<?php

namespace Irajul\FilamentShadcnTheme\Enums;

enum MenuColor: string
{
    case Default = 'default';
    case Inverted = 'inverted';
    case DefaultTranslucent = 'default-translucent';
    case InvertedTranslucent = 'inverted-translucent';

    public function isInverted(): bool
    {
        return in_array($this, [self::Inverted, self::InvertedTranslucent], true);
    }

    public function isTranslucent(): bool
    {
        return in_array($this, [self::DefaultTranslucent, self::InvertedTranslucent], true);
    }
}
