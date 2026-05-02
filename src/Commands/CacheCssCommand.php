<?php

namespace Irajul\FilamentShadcnTheme\Commands;

use Illuminate\Console\Command;
use Irajul\FilamentShadcnTheme\Support\CssAssetManager;
use Irajul\FilamentShadcnTheme\ThemeConfig;

class CacheCssCommand extends Command
{
    protected $signature = 'filament-shadcn-theme:cache
        {--panel=default : Panel identifier used in the generated CSS filename}
        {--clear : Clear existing generated package CSS assets before caching}';

    protected $description = 'Generate a cached CSS asset for the Filament shadcn theme package.';

    public function handle(CssAssetManager $assets): int
    {
        $panel = (string) $this->option('panel');

        if ($this->option('clear')) {
            $assets->clear();
        }

        $asset = $assets->ensureAsset(ThemeConfig::fromConfig(), $panel);

        $this->info("Cached Filament shadcn theme CSS: {$asset['path']}");

        return self::SUCCESS;
    }
}
