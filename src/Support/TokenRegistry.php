<?php

namespace Irajul\FilamentShadcnTheme\Support;

use Irajul\FilamentShadcnTheme\Enums\MenuAccent;
use Irajul\FilamentShadcnTheme\ThemeConfig;

class TokenRegistry
{
    public function __construct(
        private readonly PaletteRegistry $palettes,
    ) {}

    /**
     * @return array{light: array<string, string>, dark: array<string, string>}
     */
    public function tokens(ThemeConfig $config): array
    {
        $base = $this->palettes->palette($config->baseColor);
        $theme = $this->palettes->palette($config->themeColor);
        $chart = $this->palettes->palette($config->chartColor ?? $config->themeColor);

        $lightPrimary = $config->themeColor->isBaseColor() ? $theme[900] : $theme[600];
        $darkPrimary = $config->themeColor->isBaseColor() ? $theme[50] : $theme[400];

        $tokens = [
            'light' => [
                'background' => 'oklch(1 0 0)',
                'foreground' => $base[950],
                'card' => 'oklch(1 0 0)',
                'card-foreground' => $base[950],
                'popover' => 'oklch(1 0 0)',
                'popover-foreground' => $base[950],
                'primary' => $lightPrimary,
                'primary-foreground' => $config->themeColor->isBaseColor() ? $theme[50] : 'oklch(0.985 0 0)',
                'secondary' => $base[100],
                'secondary-foreground' => $base[900],
                'muted' => $base[100],
                'muted-foreground' => $base[500],
                'accent' => $base[100],
                'accent-foreground' => $base[900],
                'destructive' => $this->palettes->palette('red')[600],
                'destructive-foreground' => 'oklch(0.985 0 0)',
                'border' => $base[200],
                'input' => $base[200],
                'ring' => $config->themeColor->isBaseColor() ? $theme[400] : $theme[500],
                'chart-1' => $chart[600],
                'chart-2' => $chart[500],
                'chart-3' => $chart[400],
                'chart-4' => $chart[700],
                'chart-5' => $chart[300],
                'surface' => $base[50],
                'surface-foreground' => $base[950],
                'sidebar' => $base[50],
                'sidebar-foreground' => $base[950],
                'sidebar-primary' => $lightPrimary,
                'sidebar-primary-foreground' => $config->themeColor->isBaseColor() ? $theme[50] : 'oklch(0.985 0 0)',
                'sidebar-accent' => $base[100],
                'sidebar-accent-foreground' => $base[900],
                'sidebar-border' => $base[200],
                'sidebar-ring' => $config->themeColor->isBaseColor() ? $theme[400] : $theme[500],
            ],
            'dark' => [
                'background' => $base[950],
                'foreground' => $base[50],
                'card' => $base[900],
                'card-foreground' => $base[50],
                'popover' => $base[900],
                'popover-foreground' => $base[50],
                'primary' => $darkPrimary,
                'primary-foreground' => $config->themeColor->isBaseColor() ? $theme[900] : $theme[950],
                'secondary' => $base[800],
                'secondary-foreground' => $base[50],
                'muted' => $base[800],
                'muted-foreground' => $base[400],
                'accent' => $base[800],
                'accent-foreground' => $base[50],
                'destructive' => $this->palettes->palette('red')[400],
                'destructive-foreground' => $this->palettes->palette('red')[950],
                'border' => 'oklch(1 0 0 / 10%)',
                'input' => 'oklch(1 0 0 / 15%)',
                'ring' => $config->themeColor->isBaseColor() ? $theme[500] : $theme[400],
                'chart-1' => $chart[400],
                'chart-2' => $chart[500],
                'chart-3' => $chart[300],
                'chart-4' => $chart[600],
                'chart-5' => $chart[700],
                'surface' => $base[900],
                'surface-foreground' => $base[50],
                'sidebar' => $base[900],
                'sidebar-foreground' => $base[50],
                'sidebar-primary' => $darkPrimary,
                'sidebar-primary-foreground' => $config->themeColor->isBaseColor() ? $theme[900] : $theme[950],
                'sidebar-accent' => $base[800],
                'sidebar-accent-foreground' => $base[50],
                'sidebar-border' => 'oklch(1 0 0 / 10%)',
                'sidebar-ring' => $config->themeColor->isBaseColor() ? $theme[500] : $theme[400],
            ],
        ];

        $tokens = $this->applyMenuTokens($tokens, $config, $base);

        return [
            'light' => array_replace($tokens['light'], $config->tokenOverrides['light'] ?? []),
            'dark' => array_replace($tokens['dark'], $config->tokenOverrides['dark'] ?? []),
        ];
    }

    /**
     * @param  array{light: array<string, string>, dark: array<string, string>}  $tokens
     * @param  array<int, string>  $base
     * @return array{light: array<string, string>, dark: array<string, string>}
     */
    private function applyMenuTokens(array $tokens, ThemeConfig $config, array $base): array
    {
        if ($config->menuColor->isInverted()) {
            $tokens['light'] = array_replace($tokens['light'], [
                'sidebar' => $base[950],
                'sidebar-foreground' => $base[50],
                'sidebar-accent' => $base[800],
                'sidebar-accent-foreground' => $base[50],
                'sidebar-border' => $base[800],
            ]);

            $tokens['dark'] = array_replace($tokens['dark'], [
                'sidebar' => $base[50],
                'sidebar-foreground' => $base[950],
                'sidebar-accent' => $base[200],
                'sidebar-accent-foreground' => $base[950],
                'sidebar-border' => $base[200],
            ]);
        }

        if ($config->menuColor->isTranslucent()) {
            $tokens['light']['sidebar'] = "color-mix(in oklch, {$tokens['light']['sidebar']} 86%, transparent)";
            $tokens['dark']['sidebar'] = "color-mix(in oklch, {$tokens['dark']['sidebar']} 82%, transparent)";
        }

        if ($config->menuAccent === MenuAccent::Bold) {
            $tokens['light']['sidebar-accent'] = 'var(--primary)';
            $tokens['light']['sidebar-accent-foreground'] = 'var(--primary-foreground)';
            $tokens['dark']['sidebar-accent'] = 'var(--primary)';
            $tokens['dark']['sidebar-accent-foreground'] = 'var(--primary-foreground)';
        }

        return $tokens;
    }
}
