<?php

namespace Irajul\FilamentShadcnTheme;

use BackedEnum;
use Filament\Enums\ThemeMode;
use Irajul\FilamentShadcnTheme\Enums\BaseColor;
use Irajul\FilamentShadcnTheme\Enums\CssMode;
use Irajul\FilamentShadcnTheme\Enums\IconLibrary;
use Irajul\FilamentShadcnTheme\Enums\MenuAccent;
use Irajul\FilamentShadcnTheme\Enums\MenuColor;
use Irajul\FilamentShadcnTheme\Enums\Radius;
use Irajul\FilamentShadcnTheme\Enums\SidebarVariant;
use Irajul\FilamentShadcnTheme\Enums\StyleVariant;
use Irajul\FilamentShadcnTheme\Enums\SurfaceShadow;
use Irajul\FilamentShadcnTheme\Enums\ThemeColor;

class ThemeConfig
{
    /**
     * @param  array{light?: array<string, string>, dark?: array<string, string>}  $tokenOverrides
     * @param  array<string, string>  $styleOverrides
     * @param  array<string, string>  $selectorMap
     */
    public function __construct(
        public StyleVariant $style = StyleVariant::Lyra,
        public BaseColor $baseColor = BaseColor::Taupe,
        public ThemeColor $themeColor = ThemeColor::Taupe,
        public ?ThemeColor $chartColor = null,
        public string $font = 'inter',
        public string $headingFont = 'inherit',
        public IconLibrary $iconLibrary = IconLibrary::Lucide,
        public CssMode $cssMode = CssMode::Inline,
        public Radius $radius = Radius::None,
        public MenuColor $menuColor = MenuColor::Default,
        public MenuAccent $menuAccent = MenuAccent::Subtle,
        public SidebarVariant $sidebarVariant = SidebarVariant::Sidebar,
        public SurfaceShadow $surfaceShadow = SurfaceShadow::ExtraSmall,
        public bool $applyPanelFont = true,
        public ?ThemeMode $defaultThemeMode = null,
        public ?bool $darkMode = null,
        public ?bool $forceDarkMode = null,
        public array $tokenOverrides = [],
        public array $styleOverrides = [],
        public array $selectorMap = [],
    ) {}

    public bool $radiusWasConfigured = false;

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function fromArray(array $values): self
    {
        return self::make()->fill($values);
    }

    public static function fromConfig(): self
    {
        return self::fromArray(config('filament-shadcn-theme', []));
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function fill(array $values): self
    {
        if ($value = $this->value($values, 'style')) {
            $this->style($value);
        }

        if ($value = $this->value($values, 'baseColor', 'base_color')) {
            $this->baseColor($value);
        }

        if ($value = $this->value($values, 'themeColor', 'theme_color')) {
            $this->themeColor($value);
        }

        if (array_key_exists('chartColor', $values) || array_key_exists('chart_color', $values)) {
            $this->chartColor($this->value($values, 'chartColor', 'chart_color'));
        }

        if ($value = $this->value($values, 'font')) {
            $this->font((string) $value);
        }

        if ($value = $this->value($values, 'headingFont', 'heading_font')) {
            $this->headingFont((string) $value);
        }

        if ($value = $this->value($values, 'iconLibrary', 'icon_library')) {
            $this->iconLibrary($value);
        }

        if ($value = $this->value($values, 'cssMode', 'css_mode')) {
            $this->cssMode($value);
        }

        if ($value = $this->value($values, 'radius')) {
            $this->radius($value);
        }

        if ($value = $this->value($values, 'menuColor', 'menu_color')) {
            $this->menuColor($value);
        }

        if ($value = $this->value($values, 'menuAccent', 'menu_accent')) {
            $this->menuAccent($value);
        }

        if ($value = $this->value($values, 'sidebarVariant', 'sidebar_variant')) {
            $this->sidebarVariant($value);
        }

        if ($value = $this->value($values, 'surfaceShadow', 'surface_shadow')) {
            $this->surfaceShadow($value);
        }

        if (array_key_exists('applyPanelFont', $values) || array_key_exists('apply_panel_font', $values)) {
            $this->applyPanelFont((bool) $this->value($values, 'applyPanelFont', 'apply_panel_font'));
        }

        if (array_key_exists('defaultThemeMode', $values) || array_key_exists('default_theme_mode', $values)) {
            $mode = $this->value($values, 'defaultThemeMode', 'default_theme_mode');

            if ($mode !== null) {
                $this->defaultThemeMode($mode);
            }
        }

        if (array_key_exists('darkMode', $values) || array_key_exists('dark_mode', $values)) {
            $enabled = $this->value($values, 'darkMode', 'dark_mode');
            $forced = $this->value($values, 'forceDarkMode', 'force_dark_mode');

            if ($enabled !== null) {
                $this->darkMode((bool) $enabled, $forced === null ? null : (bool) $forced);
            }
        }

        if ($value = $this->value($values, 'tokenOverrides', 'token_overrides')) {
            $this->tokenOverrides = (array) $value;
        }

        if ($value = $this->value($values, 'styleOverrides', 'style_overrides')) {
            $this->styleOverrides = (array) $value;
        }

        if ($value = $this->value($values, 'selectorMap', 'selector_map')) {
            $this->selectorMap = (array) $value;
        }

        return $this;
    }

    public function style(StyleVariant|string $style): self
    {
        $this->style = $this->enum($style, StyleVariant::class);

        return $this;
    }

    public function baseColor(BaseColor|string $color): self
    {
        $this->baseColor = $this->enum($color, BaseColor::class);

        return $this;
    }

    public function themeColor(ThemeColor|string $color): self
    {
        $this->themeColor = $this->enum($color, ThemeColor::class);

        return $this;
    }

    public function chartColor(ThemeColor|string|null $color): self
    {
        $this->chartColor = ($color === null) ? null : $this->enum($color, ThemeColor::class);

        return $this;
    }

    public function font(string $font): self
    {
        $this->font = $font;

        return $this;
    }

    public function headingFont(string $font): self
    {
        $this->headingFont = $font;

        return $this;
    }

    public function iconLibrary(IconLibrary|string $library): self
    {
        $this->iconLibrary = $this->enum($library, IconLibrary::class);

        return $this;
    }

    public function cssMode(CssMode|string $mode): self
    {
        $this->cssMode = $this->enum($mode, CssMode::class);

        return $this;
    }

    public function radius(Radius|string $radius): self
    {
        $this->radius = $this->enum($radius, Radius::class);
        $this->radiusWasConfigured = true;

        return $this;
    }

    public function hasExplicitRadius(): bool
    {
        return $this->radiusWasConfigured || $this->radius !== Radius::None;
    }

    public function menuColor(MenuColor|string $color): self
    {
        $this->menuColor = $this->enum($color, MenuColor::class);

        return $this;
    }

    public function menuAccent(MenuAccent|string $accent): self
    {
        $this->menuAccent = $this->enum($accent, MenuAccent::class);

        return $this;
    }

    public function sidebarVariant(SidebarVariant|string $variant): self
    {
        $this->sidebarVariant = $this->enum($variant, SidebarVariant::class);

        return $this;
    }

    public function surfaceShadow(SurfaceShadow|string $shadow): self
    {
        $this->surfaceShadow = $this->enum($shadow, SurfaceShadow::class);

        return $this;
    }

    public function applyPanelFont(bool $apply = true): self
    {
        $this->applyPanelFont = $apply;

        return $this;
    }

    public function defaultThemeMode(ThemeMode|string|null $mode): self
    {
        $this->defaultThemeMode = ($mode === null) ? null : $this->enum($mode, ThemeMode::class);

        return $this;
    }

    public function darkMode(?bool $enabled, ?bool $forced = null): self
    {
        $this->darkMode = $enabled;
        $this->forceDarkMode = $forced;

        return $this;
    }

    /**
     * @param  array<string, string>  $light
     * @param  array<string, string>  $dark
     */
    public function tokens(array $light = [], array $dark = []): self
    {
        $this->tokenOverrides = array_replace_recursive($this->tokenOverrides, [
            'light' => $light,
            'dark' => $dark,
        ]);

        return $this;
    }

    /**
     * @param  array<string, string>  $variables
     */
    public function styleVariables(array $variables): self
    {
        $this->styleOverrides = array_replace($this->styleOverrides, $variables);

        return $this;
    }

    /**
     * @param  array<string, string>  $selectorMap
     */
    public function selectorMap(array $selectorMap): self
    {
        $this->selectorMap = array_replace($this->selectorMap, $selectorMap);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'style' => $this->style->value,
            'base_color' => $this->baseColor->value,
            'theme_color' => $this->themeColor->value,
            'chart_color' => $this->chartColor?->value,
            'font' => $this->font,
            'heading_font' => $this->headingFont,
            'icon_library' => $this->iconLibrary->value,
            'css_mode' => $this->cssMode->value,
            'radius' => $this->radius->value,
            'menu_color' => $this->menuColor->value,
            'menu_accent' => $this->menuAccent->value,
            'sidebar_variant' => $this->sidebarVariant->value,
            'surface_shadow' => $this->surfaceShadow->value,
            'apply_panel_font' => $this->applyPanelFont,
            'default_theme_mode' => $this->defaultThemeMode?->value,
            'dark_mode' => $this->darkMode,
            'force_dark_mode' => $this->forceDarkMode,
            'token_overrides' => $this->tokenOverrides,
            'style_overrides' => $this->styleOverrides,
            'selector_map' => $this->selectorMap,
        ];
    }

    /**
     * @param  class-string<BackedEnum>  $enum
     */
    private function enum(BackedEnum|string $value, string $enum): BackedEnum
    {
        if ($value instanceof $enum) {
            return $value;
        }

        return $enum::from($value);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private function value(array $values, string $camelKey, ?string $snakeKey = null): mixed
    {
        if (array_key_exists($camelKey, $values)) {
            return $values[$camelKey];
        }

        if ($snakeKey !== null && array_key_exists($snakeKey, $values)) {
            return $values[$snakeKey];
        }

        return null;
    }
}
