<?php

namespace Irajul\FilamentShadcnTheme\Support;

use BackedEnum;
use Filament\Support\Colors\Color as FilamentColor;
use InvalidArgumentException;
use Irajul\FilamentShadcnTheme\ThemeConfig;

class PaletteRegistry
{
    /**
     * @var array<string, string>
     */
    private const FilamentColorConstants = [
        'neutral' => 'Neutral',
        'stone' => 'Stone',
        'zinc' => 'Zinc',
        'mauve' => 'Mauve',
        'olive' => 'Olive',
        'mist' => 'Mist',
        'taupe' => 'Taupe',
        'amber' => 'Amber',
        'blue' => 'Blue',
        'cyan' => 'Cyan',
        'emerald' => 'Emerald',
        'fuchsia' => 'Fuchsia',
        'green' => 'Green',
        'indigo' => 'Indigo',
        'lime' => 'Lime',
        'orange' => 'Orange',
        'pink' => 'Pink',
        'purple' => 'Purple',
        'red' => 'Red',
        'rose' => 'Rose',
        'sky' => 'Sky',
        'teal' => 'Teal',
        'violet' => 'Violet',
        'yellow' => 'Yellow',
    ];

    /**
     * @return array<int, string>
     */
    public function palette(BackedEnum|string $color): array
    {
        $name = $color instanceof BackedEnum ? (string) $color->value : $color;
        $constant = self::FilamentColorConstants[$name] ?? null;

        if ($constant === null) {
            throw new InvalidArgumentException("Unknown shadcn color [{$name}].");
        }

        return constant(FilamentColor::class."::{$constant}");
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function filamentColors(ThemeConfig $config): array
    {
        return [
            'primary' => $this->palette($config->themeColor),
            'gray' => $this->palette($config->baseColor),
            'danger' => $this->palette('red'),
            'success' => $this->palette('emerald'),
            'warning' => $this->palette('amber'),
            'info' => $this->palette('sky'),
        ];
    }
}
