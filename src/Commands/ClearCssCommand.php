<?php

namespace Irajul\FilamentShadcnTheme\Commands;

use Illuminate\Console\Command;
use Irajul\FilamentShadcnTheme\Support\CssAssetManager;

class ClearCssCommand extends Command
{
    protected $signature = 'filament-shadcn-theme:clear
        {--panel= : Only clear generated CSS assets for a specific panel identifier}';

    protected $description = 'Delete generated CSS assets for the Filament shadcn theme package.';

    public function handle(CssAssetManager $assets): int
    {
        $panel = $this->option('panel');
        $deleted = $assets->clear(is_string($panel) && $panel !== '' ? $panel : null);

        $this->info("Deleted {$deleted} Filament shadcn theme CSS asset(s).");

        return self::SUCCESS;
    }
}
