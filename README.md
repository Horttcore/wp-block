# WP Block

[![Tests](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml)
[![Code Quality](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml)
[![Security](https://github.com/Horttcore/wp-block/actions/workflows/security.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/security.yml)
[![codecov](https://codecov.io/gh/Horttcore/wp-block/branch/master/graph/badge.svg)](https://codecov.io/gh/Horttcore/wp-block)

A fluent PHP wrapper for registering WordPress blocks and adjusting block metadata (defaults, supports, styles, variations).

## Installation

```bash
composer require ralfhortt/wp-block
```

Requires PHP 8.3+.

## Documentation

| Class | Purpose |
|-------|---------|
| [BlockManifest](docs/block-manifest.md) | Register blocks from a build manifest |
| [Block](docs/block.md) | Abstract base for PHP-rendered custom blocks |
| [BlockDefaults](docs/block-defaults.md) | Override attribute defaults (incl. callbacks) |
| [BlockSupports](docs/block-supports.md) | Add or remove block supports for one block |
| [BlockStyles](docs/block-styles.md) | Add or remove block styles |
| [BlockVariations](docs/block-variations.md) | Add or remove block variations |
| [BlockManager](docs/block-manager.md) | Supports + styles + variations for one block |
| [BlockPatterns](docs/block-patterns.md) | Pattern categories; disable core/remote patterns |

[WordPress hooks reference](docs/hooks.md)

### Quick example

```php
use RalfHortt\WPBlock\BlockManifest;

(new BlockManifest(__DIR__ . '/build/blocks/blocks-manifest.php'))->register();
```

## Development

```bash
composer install
composer test              # Run tests
composer test:coverage     # With coverage
composer phpstan           # Static analysis (level 8)
composer ci                # validate + phpstan + test
```

Pest, PHPStan, Brain Monkey, and GitHub Actions CI.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later — see [LICENSE](LICENSE).

## Credits

[Ralf Hortt](https://horttcore.de)
