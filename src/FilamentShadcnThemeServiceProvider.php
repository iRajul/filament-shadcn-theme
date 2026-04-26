<?php

namespace Irajul\FilamentShadcnTheme;

use Illuminate\Support\ServiceProvider;

class FilamentShadcnThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/filament-shadcn-theme.php',
            'filament-shadcn-theme',
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/filament-shadcn-theme.php' => config_path('filament-shadcn-theme.php'),
        ], 'filament-shadcn-theme-config');
    }
}
