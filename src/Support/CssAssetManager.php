<?php

namespace Irajul\FilamentShadcnTheme\Support;

use Composer\InstalledVersions;
use FilesystemIterator;
use Irajul\FilamentShadcnTheme\ThemeConfig;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;

class CssAssetManager
{
    public const CacheVersion = '1';

    private const RelativeDirectory = 'vendor/filament-shadcn-theme';

    public function __construct(
        private readonly CssRenderer $renderer,
    ) {}

    /**
     * @return array{filename: string, path: string, url: string}
     */
    public function ensureAsset(ThemeConfig $config, ?string $panelId = null): array
    {
        $directory = $this->assetDirectory();
        $filename = $this->filename($config, $panelId);
        $path = $directory.'/'.$filename;

        $this->ensureDirectoryExists($directory);
        $this->clear($panelId, except: $path);
        $this->ensureDirectoryExists($directory);

        if (! is_file($path)) {
            $this->writeAsset($path, $this->renderer->render($config));
        }

        return [
            'filename' => $filename,
            'path' => $path,
            'url' => asset(self::RelativeDirectory.'/'.$filename),
        ];
    }

    public function clear(?string $panelId = null, ?string $except = null): int
    {
        $directory = $this->assetDirectory();

        if (! is_dir($directory)) {
            return 0;
        }

        $deleted = 0;
        $pattern = $panelId === null
            ? $directory.'/panel-*.css'
            : $directory.'/'.$this->prefix($panelId).'-*.css';

        foreach (glob($pattern) ?: [] as $path) {
            if ($except !== null && realpath($path) === realpath($except)) {
                continue;
            }

            if (is_file($path) && unlink($path)) {
                $deleted++;
            }
        }

        if ($this->isDirectoryEmpty($directory)) {
            rmdir($directory);
        }

        return $deleted;
    }

    public function filename(ThemeConfig $config, ?string $panelId = null): string
    {
        return $this->prefix($panelId).'-'.$this->hash($config).'.css';
    }

    private function assetDirectory(): string
    {
        return public_path(self::RelativeDirectory);
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException("Unable to create Filament shadcn theme asset directory [{$directory}].");
        }
    }

    private function writeAsset(string $path, string $contents): void
    {
        $temporaryPath = $path.'.tmp';

        if (file_put_contents($temporaryPath, $contents, LOCK_EX) === false) {
            throw new RuntimeException("Unable to write Filament shadcn theme asset [{$temporaryPath}].");
        }

        if (! rename($temporaryPath, $path)) {
            unlink($temporaryPath);

            throw new RuntimeException("Unable to move Filament shadcn theme asset [{$path}].");
        }
    }

    private function hash(ThemeConfig $config): string
    {
        return substr(hash('sha256', json_encode([
            'cache_version' => self::CacheVersion,
            'package' => $this->packageFingerprint(),
            'source' => $this->sourceFingerprint(),
            'radius_explicit' => $config->hasExplicitRadius(),
            'config' => $config->toArray(),
        ], JSON_THROW_ON_ERROR)), 0, 16);
    }

    private function packageFingerprint(): string
    {
        if (! class_exists(InstalledVersions::class)) {
            return 'source';
        }

        try {
            return implode('|', array_filter([
                InstalledVersions::getPrettyVersion('irajul/filament-shadcn-theme'),
                InstalledVersions::getReference('irajul/filament-shadcn-theme'),
            ])) ?: 'source';
        } catch (Throwable) {
            return 'source';
        }
    }

    /**
     * @return array<string, array{mtime: int, size: int}>
     */
    private function sourceFingerprint(): array
    {
        static $fingerprint = null;

        if ($fingerprint !== null) {
            return $fingerprint;
        }

        $root = realpath(__DIR__.'/..') ?: __DIR__.'/..';
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
        );
        $fingerprint = [];

        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            $relativePath = str_replace($root.DIRECTORY_SEPARATOR, '', $path);

            $fingerprint[$relativePath] = [
                'mtime' => $file->getMTime(),
                'size' => $file->getSize(),
            ];
        }

        ksort($fingerprint);

        return $fingerprint;
    }

    private function prefix(?string $panelId = null): string
    {
        $panel = $panelId === null ? 'default' : $panelId;
        $slug = strtolower((string) preg_replace('/[^A-Za-z0-9]+/', '-', $panel));
        $slug = trim($slug, '-');

        return 'panel-'.($slug === '' ? 'default' : $slug);
    }

    private function isDirectoryEmpty(string $directory): bool
    {
        $handle = opendir($directory);

        if ($handle === false) {
            return false;
        }

        while (($entry = readdir($handle)) !== false) {
            if ($entry !== '.' && $entry !== '..') {
                closedir($handle);

                return false;
            }
        }

        closedir($handle);

        return true;
    }
}
