# Private Composer Distribution

This package is ready to distribute through a private Composer repository. The simplest low-maintenance route is Satis: it scans one or more VCS repositories and generates static Composer metadata that you can host behind HTTPS.

## What You Need

- A private Git repository for this package.
- A version tag for each release, for example `v1.0.0`.
- A web host for the generated Satis output, for example `https://packages.example.com`.
- Server or CI credentials that can read the private Git repository.
- Composer authentication for every consuming app if the Satis host or Git source is private.

## Build The Satis Repository

Create a Satis project on the package server or in CI:

```bash
composer create-project composer/satis satis dev-main
cd satis
```

Copy this package's `satis.json.example` to `satis.json`, then update:

- `homepage` to the public or private HTTPS URL serving the generated output.
- `repositories[0].url` to this package's private Git URL.
- `require` if you rename the Composer package.

Generate the static Composer repository:

```bash
php bin/satis build satis.json public/
```

Serve the generated `public/` directory from your web server. Rebuild Satis after each push or tag. In production, trigger the rebuild from CI or a Git webhook.

## Install From A Consuming Laravel App

In the consuming app:

```bash
composer config repositories.private-packages composer https://packages.example.com
composer require irajul/filament-shadcn-theme:^1.0
php artisan vendor:publish --tag=filament-shadcn-theme-config
```

If the package repository is protected with HTTP basic auth:

```bash
composer config --global http-basic.packages.example.com USERNAME TOKEN
```

If Composer needs direct GitHub access to the private source repository:

```bash
composer config --global github-oauth.github.com GITHUB_TOKEN
```

Use a read-only, scoped, expiring token where possible. Do not commit credentials into `composer.json`.

## Release Checklist

Before tagging a release:

```bash
composer validate --strict
composer install
composer test
git tag v1.0.0
git push origin main --tags
```

Then rebuild Satis so the new tag appears in `packages.json`.
