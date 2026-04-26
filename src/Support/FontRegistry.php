<?php

namespace Irajul\FilamentShadcnTheme\Support;

class FontRegistry
{
    /**
     * @var array<string, array{name: string, stack: string}>
     */
    private const Fonts = [
        'inter' => ['name' => 'Inter', 'stack' => 'Inter, ui-sans-serif, system-ui, sans-serif'],
        'geist' => ['name' => 'Geist', 'stack' => 'Geist, ui-sans-serif, system-ui, sans-serif'],
        'figtree' => ['name' => 'Figtree', 'stack' => 'Figtree, ui-sans-serif, system-ui, sans-serif'],
        'manrope' => ['name' => 'Manrope', 'stack' => 'Manrope, ui-sans-serif, system-ui, sans-serif'],
        'dm-sans' => ['name' => 'DM Sans', 'stack' => '"DM Sans", ui-sans-serif, system-ui, sans-serif'],
        'public-sans' => ['name' => 'Public Sans', 'stack' => '"Public Sans", ui-sans-serif, system-ui, sans-serif'],
        'noto-sans' => ['name' => 'Noto Sans', 'stack' => '"Noto Sans", ui-sans-serif, system-ui, sans-serif'],
        'nunito-sans' => ['name' => 'Nunito Sans', 'stack' => '"Nunito Sans", ui-sans-serif, system-ui, sans-serif'],
        'space-grotesk' => ['name' => 'Space Grotesk', 'stack' => '"Space Grotesk", ui-sans-serif, system-ui, sans-serif'],
        'montserrat' => ['name' => 'Montserrat', 'stack' => 'Montserrat, ui-sans-serif, system-ui, sans-serif'],
        'ibm-plex-sans' => ['name' => 'IBM Plex Sans', 'stack' => '"IBM Plex Sans", ui-sans-serif, system-ui, sans-serif'],
        'jetbrains-mono' => ['name' => 'JetBrains Mono', 'stack' => '"JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, monospace'],
        'geist-mono' => ['name' => 'Geist Mono', 'stack' => '"Geist Mono", ui-monospace, SFMono-Regular, Menlo, monospace'],
    ];

    public function stack(string $font): string
    {
        $key = $this->normalize($font);

        return self::Fonts[$key]['stack'] ?? "{$this->quote($font)}, ui-sans-serif, system-ui, sans-serif";
    }

    public function headingStack(string $font, string $bodyFont): string
    {
        if ($font === 'inherit') {
            return 'var(--font-sans)';
        }

        return $this->stack($font ?: $bodyFont);
    }

    public function panelFamily(string $font): string
    {
        $key = $this->normalize($font);

        return self::Fonts[$key]['name'] ?? trim($font);
    }

    private function normalize(string $font): string
    {
        return str($font)->lower()->replace(' ', '-')->toString();
    }

    private function quote(string $font): string
    {
        $font = trim(str_replace(['\\', '"'], ['', ''], $font));

        return "\"{$font}\"";
    }
}
