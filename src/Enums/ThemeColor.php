<?php

namespace Irajul\FilamentShadcnTheme\Enums;

enum ThemeColor: string
{
    case Neutral = 'neutral';
    case Stone = 'stone';
    case Zinc = 'zinc';
    case Mauve = 'mauve';
    case Olive = 'olive';
    case Mist = 'mist';
    case Taupe = 'taupe';
    case Amber = 'amber';
    case Blue = 'blue';
    case Cyan = 'cyan';
    case Emerald = 'emerald';
    case Fuchsia = 'fuchsia';
    case Green = 'green';
    case Indigo = 'indigo';
    case Lime = 'lime';
    case Orange = 'orange';
    case Pink = 'pink';
    case Purple = 'purple';
    case Red = 'red';
    case Rose = 'rose';
    case Sky = 'sky';
    case Teal = 'teal';
    case Violet = 'violet';
    case Yellow = 'yellow';

    public function isBaseColor(): bool
    {
        return in_array($this->value, array_map(
            fn (BaseColor $color): string => $color->value,
            BaseColor::cases(),
        ), true);
    }
}
