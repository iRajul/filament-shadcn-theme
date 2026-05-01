# Contributing

Thanks for helping improve Filament Shadcn Theme.

## Local Setup

```bash
composer install
composer validate --strict
composer test
```

The package test suite is intentionally self-contained. Tests live in `tests/` and use Orchestra Testbench, so changes should not require a separate Laravel host application.

## Pull Requests

- Keep changes focused on one behavior or documentation area.
- Add or update package-local tests when behavior changes.
- Update `README.md` when adding configuration options, fluent methods, commands, or user-visible behavior.
- Update `CHANGELOG.md` under `Unreleased`.
- Run `composer validate --strict` and `composer test` before opening the PR.

## Design Notes

This package should keep the Laravel and Filament integration small:

- Prefer configuration values and fluent methods over app-specific assumptions.
- Keep generated CSS deterministic and testable through `CssRenderer`.
- Keep package verification inside this repository rather than relying on a host application test suite.
