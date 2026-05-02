<?php

namespace Irajul\FilamentShadcnTheme;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Enums\ThemeMode;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
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
use Irajul\FilamentShadcnTheme\Support\CssAssetManager;
use Irajul\FilamentShadcnTheme\Support\CssRenderer;
use Irajul\FilamentShadcnTheme\Support\FontRegistry;
use Irajul\FilamentShadcnTheme\Support\PaletteRegistry;

class FilamentShadcnThemePlugin implements Plugin
{
    public const Id = 'irajul-filament-shadcn-theme';

    protected ThemeConfig $config;

    public function __construct(?ThemeConfig $config = null)
    {
        $this->config = $config ?? ThemeConfig::fromConfig();
    }

    public static function make(?ThemeConfig $config = null): self
    {
        return new self($config);
    }

    public function getId(): string
    {
        return self::Id;
    }

    public function register(Panel $panel): void
    {
        $panel->colors(app(PaletteRegistry::class)->filamentColors($this->config));

        if ($this->config->applyPanelFont) {
            $panel->font(app(FontRegistry::class)->panelFamily($this->config->font));
        }

        if ($this->config->defaultThemeMode instanceof ThemeMode) {
            $panel->defaultThemeMode($this->config->defaultThemeMode);
        }

        if ($this->config->darkMode !== null) {
            $panel->darkMode($this->config->darkMode, isForced: (bool) $this->config->forceDarkMode);
        }

        $panel->renderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn (): HtmlString => $this->renderStyles($panel->getId()),
        );
    }

    public function boot(Panel $panel): void {}

    public function config(): ThemeConfig
    {
        return $this->config;
    }

    public function configure(ThemeConfig|array|Closure $configuration): self
    {
        if ($configuration instanceof ThemeConfig) {
            $this->config = $configuration;

            return $this;
        }

        if ($configuration instanceof Closure) {
            $result = $configuration($this->config);

            if ($result instanceof ThemeConfig) {
                $this->config = $result;
            }

            return $this;
        }

        $this->config->fill($configuration);

        return $this;
    }

    public function style(StyleVariant|string $style): self
    {
        $this->config->style($style);

        return $this;
    }

    public function baseColor(BaseColor|string $color): self
    {
        $this->config->baseColor($color);

        return $this;
    }

    public function themeColor(ThemeColor|string $color): self
    {
        $this->config->themeColor($color);

        return $this;
    }

    public function chartColor(ThemeColor|string|null $color): self
    {
        $this->config->chartColor($color);

        return $this;
    }

    public function font(string $font): self
    {
        $this->config->font($font);

        return $this;
    }

    public function headingFont(string $font): self
    {
        $this->config->headingFont($font);

        return $this;
    }

    public function iconLibrary(IconLibrary|string $library): self
    {
        $this->config->iconLibrary($library);

        return $this;
    }

    public function cssMode(CssMode|string $mode): self
    {
        $this->config->cssMode($mode);

        return $this;
    }

    public function radius(Radius|string $radius): self
    {
        $this->config->radius($radius);

        return $this;
    }

    public function menuColor(MenuColor|string $color): self
    {
        $this->config->menuColor($color);

        return $this;
    }

    public function menuAccent(MenuAccent|string $accent): self
    {
        $this->config->menuAccent($accent);

        return $this;
    }

    public function sidebarVariant(SidebarVariant|string $variant): self
    {
        $this->config->sidebarVariant($variant);

        return $this;
    }

    public function surfaceShadow(SurfaceShadow|string $shadow): self
    {
        $this->config->surfaceShadow($shadow);

        return $this;
    }

    /**
     * @param  array<string, string>  $light
     * @param  array<string, string>  $dark
     */
    public function tokens(array $light = [], array $dark = []): self
    {
        $this->config->tokens($light, $dark);

        return $this;
    }

    /**
     * @param  array<string, string>  $variables
     */
    public function styleVariables(array $variables): self
    {
        $this->config->styleVariables($variables);

        return $this;
    }

    /**
     * @param  array<string, string>  $selectorMap
     */
    public function selectorMap(array $selectorMap): self
    {
        $this->config->selectorMap($selectorMap);

        return $this;
    }

    public function defaultThemeMode(ThemeMode|string|null $mode): self
    {
        $this->config->defaultThemeMode($mode);

        return $this;
    }

    public function darkMode(?bool $enabled = true, ?bool $forced = null): self
    {
        $this->config->darkMode($enabled, $forced);

        return $this;
    }

    private function renderStyles(?string $panelId = null): HtmlString
    {
        if ($this->config->cssMode === CssMode::CachedAsset) {
            $asset = app(CssAssetManager::class)->ensureAsset($this->config, $panelId);

            return new HtmlString(
                '<link data-filament-shadcn-theme="'.self::Id.'" rel="stylesheet" href="'.$this->escapeAttribute($asset['url']).'">',
            );
        }

        return new HtmlString(
            '<style data-filament-shadcn-theme="'.self::Id.'">'.app(CssRenderer::class)->render($this->config).'</style>',
        );
    }

    private function escapeAttribute(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
