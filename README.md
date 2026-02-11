# WP Block

[![Tests](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml)
[![Code Quality](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml)
[![Security](https://github.com/Horttcore/wp-block/actions/workflows/security.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/security.yml)
[![codecov](https://codecov.io/gh/Horttcore/wp-block/branch/master/graph/badge.svg)](https://codecov.io/gh/Horttcore/wp-block)

A modern, fluent PHP wrapper for WordPress block development.

## Installation

```bash
composer require ralfhortt/wp-block
```

## Features

- 🚀 **BlockManifest** - Register multiple blocks from manifest files
- 🎨 **BlockVariations** - Create variations of existing blocks
- 🎭 **BlockStyles** - Manage block styles
- ⚙️ **BlockDefaults** - Override block attribute defaults with callback support
- 🧩 **Block** - Custom PHP-rendered blocks

## Quick Start

### BlockManifest

Register blocks from a manifest file (recommended for modern block development):

```php
use RalfHortt\WPBlock\BlockManifest;

(new BlockManifest(__DIR__ . '/build/blocks/blocks-manifest.php'))->register();
```

### BlockDefaults

Override default attribute values with support for dynamic callbacks:

```php
use RalfHortt\WPBlock\BlockDefaults;

// Static defaults
BlockDefaults::for('core/image')
    ->set('sizeSlug', 'large')
    ->set('linkDestination', 'media')
    ->register();

// Dynamic defaults with callbacks
BlockDefaults::for('core/image')
    ->set('sizeSlug', fn($metadata) => wp_is_mobile() ? 'thumbnail' : 'large')
    ->register();

// Multiple blocks with shared and individual defaults
BlockDefaults::for(['core/image', 'core/paragraph'])
    ->set('className', 'custom-block') // Both blocks
    ->set('core/image', 'sizeSlug', 'large') // Only core/image
    ->register();
```

### BlockVariations

Create variations of existing blocks:

```php
use RalfHortt\WPBlock\BlockVariations;

(new BlockVariations([
    'core/image' => [
        [
            'name' => 'hero-image',
            'title' => __('Hero Image'),
            'attributes' => [
                'align' => 'wide',
                'className' => 'hero-image',
            ],
        ],
    ],
]))->register();

// Or use fluent interface
(new BlockVariations())
    ->addVariation('core/button', [
        'name' => 'cta-button',
        'title' => __('CTA Button'),
        'attributes' => ['className' => 'cta-button'],
    ])
    ->removeVariation('core/button', 'outline')
    ->register();
```

### BlockStyles

Remove unwanted block styles:

```php
use RalfHortt\WPBlock\BlockStyles;

(new BlockStyles())
    ->removeStyle('core/button', 'outline')
    ->removeAllStyles('core/separator')
    ->register();
```

> **Note:** For adding styles, use `theme.json` or `block.json` instead.

### Custom Block Class

Create custom PHP-rendered blocks:

```php
use RalfHortt\WPBlock\Block;

class MyBlock extends Block {
    protected string $name = 'myplugin/custom-block';
    protected string $title = 'My Custom Block';
    protected string $blockJson = 'block.json';
    
    public function render(array $attributes, string $content): string {
        return '<div class="my-block">' . esc_html($attributes['title'] ?? '') . '</div>';
    }
}

(new MyBlock())->register();
```

## API Reference

### Block Variation Properties

Each variation supports these properties:

- `name` (required) - Unique identifier
- `title` - Display name in block inserter
- `description` - Description text
- `attributes` - Default attribute values
- `innerBlocks` - Default inner blocks for containers
- `scope` - Where variation appears: `['inserter']`, `['block']`, or both
- `isDefault` - Set as default variation

See [WordPress Block Variations documentation](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/) for more details.

### Available Hooks

**Block Class:**
- `{$blockName}/before` - Action before block output
- `{$blockName}/after` - Action after block output  
- `{$blockName}/render` - Filter render callback

**WordPress Filters (used internally):**
- `block_type_metadata` - Used by BlockDefaults and BlockManifest
- `get_block_type_variations` - Used by BlockVariations

## Development

### Requirements

- PHP 8.3 or higher
- Composer

### Testing

```bash
composer install
composer test              # Run tests
composer test:coverage     # With coverage
composer phpstan           # Static analysis
composer ci                # Full CI suite
```

### Code Quality

- **PestPHP** for testing
- **PHPStan** (level 8) for static analysis
- **GitHub Actions** for CI/CD
- **Brain\Monkey** for WordPress mocking

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and migration guides.

## License

MIT License - see LICENSE file for details.

## Credits

Developed and maintained by [Ralf Hortt](https://horttcore.de).
