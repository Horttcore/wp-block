# WP Block

[![Tests](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml)
[![Code Quality](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml)
[![Security](https://github.com/Horttcore/wp-block/actions/workflows/security.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/security.yml)
[![codecov](https://codecov.io/gh/Horttcore/wp-block/branch/master/graph/badge.svg)](https://codecov.io/gh/Horttcore/wp-block)

A composer wrapper for WordPress blocks, block variations, and block patterns

## Installation

`$ composer require ralfhortt/wp-block`

## Usage

There are three main ways to work with WordPress blocks using this package:

1. **BlockManifest** - For registering multiple blocks from a manifest file (recommended for modern block development)
2. **Block** - For programmatic block registration with custom PHP rendering
3. **BlockVariations** - For registering block variations to extend existing blocks

## Examples

### BlockManifest Example

Use `BlockManifest` when you have a `block.json` file or a manifest that describes multiple blocks. This is the modern WordPress approach and integrates well with build tools.

```php
<?php
use RalfHortt\WPBlock\BlockManifest;

// Register blocks from a manifest file
(new BlockManifest(__DIR__ . '/build/blocks/blocks-manifest.php'))->register();
```

### Block Class Examples

Use the `Block` class for custom PHP-rendered blocks with full control over the rendering process.

```php
class MyBlock extends Block {
	protected string $name = 'ralfhortt/myotherblock';
	protected string $title = 'My other Block';
	protected string $blockJson = 'block.json';
	// …
}

// Register your blocks (typically in your plugin's main file)
$myBlock = new MyBlock();
$myBlock->register();
```

### BlockVariations Examples

Use `BlockVariations` to register variations of existing WordPress blocks. Block variations allow you to provide predefined configurations of existing blocks.

#### Basic Usage

```php
<?php
use RalfHortt\WPBlock\BlockVariations;

// Register variations during instantiation
new BlockVariations([
    'core/image' => [
        [
            'name' => 'hero-image',
            'title' => __('Hero Image', 'text-domain'),
            'description' => __('A large hero image with wide alignment', 'text-domain'),
            'attributes' => [
                'align' => 'wide',
                'className' => 'hero-image',
            ],
            'scope' => ['inserter'],
        ],
        [
            'name' => 'thumbnail-image',
            'title' => __('Thumbnail Image', 'text-domain'),
            'description' => __('A small thumbnail image', 'text-domain'),
            'attributes' => [
                'className' => 'thumbnail-image',
                'width' => 150,
                'height' => 150,
            ],
        ],
    ],
    'core/button' => [
        [
            'name' => 'cta-button',
            'title' => __('Call to Action Button', 'text-domain'),
            'description' => __('A prominent call-to-action button', 'text-domain'),
            'attributes' => [
                'className' => 'wp-block-button__link cta-button',
                'style' => [
                    'color' => [
                        'background' => '#ff6b35',
                        'text' => '#ffffff',
                    ],
                ],
            ],
        ],
    ],
])->register();
```

#### Fluent Interface

```php
<?php
use RalfHortt\WPBlock\BlockVariations;

// Build variations using fluent interface
(new BlockVariations())
    ->addVariation('core/image', [
        'name' => 'featured-image',
        'title' => __('Featured Image', 'text-domain'),
        'attributes' => ['className' => 'featured-image'],
    ])
    ->addVariations('core/heading', [
        [
            'name' => 'section-heading',
            'title' => __('Section Heading', 'text-domain'),
            'attributes' => ['level' => 2, 'className' => 'section-heading'],
        ],
        [
            'name' => 'page-title',
            'title' => __('Page Title', 'text-domain'),
            'attributes' => ['level' => 1, 'className' => 'page-title'],
        ],
    ])
    ->register();
```

#### Managing Variations

```php
<?php
use RalfHortt\WPBlock\BlockVariations;

$blockVariations = new BlockVariations([
    'core/image' => [
        [
            'name' => 'variation-1',
            'title' => 'Variation 1',
        ],
        [
            'name' => 'variation-2',
            'title' => 'Variation 2',
        ],
    ],
]);

// Remove a specific variation
$blockVariations->removeVariation('core/image', 'variation-1');

// Remove all variations for a block type
$blockVariations->removeAllVariations('core/image');

// Add new variations
$blockVariations->addVariation('core/paragraph', [
    'name' => 'highlight-text',
    'title' => __('Highlighted Text', 'text-domain'),
    'attributes' => ['className' => 'highlight-text'],
]);

$blockVariations->register();
```

### When to Use Each Approach

**Use BlockManifest when:**

- You have multiple blocks to register
- You're using modern block development with build tools
- You want to leverage WordPress's native `block.json` metadata
- You're building blocks with JavaScript/React components

**Use Block class when:**

- You need custom PHP rendering logic
- You're building server-side rendered blocks
- You want fine-grained control over block behavior
- You're migrating from legacy shortcode-based solutions

**Use BlockVariations when:**

- You want to provide predefined configurations of existing blocks
- You need to create themed versions of core blocks
- You want to simplify block selection for content editors
- You're extending blocks without creating entirely new block types

## Hooks

### Block Class Hooks

#### Actions

- `{$name}/before` - Before block output
- `{$name}/after` - After block output

#### Filters

- `{$name}/name` - Block name
- `{$name}/attributes` - Block attributes
- `{$name}/render` - Overwrite render callback

### BlockManifest Hooks

The `BlockManifest` class uses WordPress's native `init` action to register blocks. You can hook into the standard WordPress block registration process:

- `block_type_metadata` - Filter block metadata before registration
- `register_block_type_args` - Filter block registration arguments

### BlockVariations Hooks

The `BlockVariations` class uses WordPress's native `get_block_type_variations` filter to register variations. This ensures proper integration with the block editor and allows for dynamic variation registration.

- `get_block_type_variations` - Filter used internally by BlockVariations to register variations for specific block types

#### Block Variation Properties

Each variation can include the following properties:

- `name` (required) - Unique identifier for the variation
- `title` - Display name in the block inserter
- `description` - Description shown in the block inserter
- `category` - Category for organizing variations
- `icon` - Icon for the variation (Dashicon slug, SVG, or icon object)
- `keywords` - Search keywords for the block inserter
- `attributes` - Default attributes for the variation
- `innerBlocks` - Default inner blocks for container blocks
- `example` - Preview configuration for the variation
- `scope` - Where the variation appears (`['inserter']`, `['block']`, or `['inserter', 'block']`)
- `isDefault` - Whether this variation is the default for the block type

## Development

### Requirements

- PHP 8.3 or higher
- Composer

### Testing

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test:coverage

# Run static analysis
composer phpstan

# Run full CI suite
composer ci
```

### Code Quality

This project uses:

- **PestPHP** for testing
- **PHPStan** for static analysis (level 8)
- **GitHub Actions** for continuous integration
- **Brain\Monkey** for WordPress function mocking

## Changelog

### v2.2 - 2025-11-02

- Adding support for block variations with `BlockVariations` class
- Support for fluent interface for adding/removing variations
- Proper integration with WordPress `get_block_type_variations` filter
- Comprehensive test coverage for block variations

### v2.1 - 2025-10-22

- Adding support to register block pattern categories

### v2.0 - 2025-10-11

- Adding support for block manifest

### v1.2 - 2022-05-10

- Add support for `block.json`

### v1.1 - 2020-05-25

- Apply fixes from StyleCI

### v1.0 - 2020-01-17

- Initial release
