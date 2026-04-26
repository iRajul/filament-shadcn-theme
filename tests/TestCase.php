<?php

namespace Irajul\FilamentShadcnTheme\Tests;

use Irajul\FilamentShadcnTheme\FilamentShadcnThemeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  mixed  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            FilamentShadcnThemeServiceProvider::class,
        ];
    }
}
