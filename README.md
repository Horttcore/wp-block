# WP Block

[![Tests](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/tests.yml)
[![Code Quality](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/code-quality.yml)
[![Security](https://github.com/Horttcore/wp-block/actions/workflows/security.yml/badge.svg)](https://github.com/Horttcore/wp-block/actions/workflows/security.yml)
[![codecov](https://codecov.io/gh/Horttcore/wp-block/branch/master/graph/badge.svg)](https://codecov.io/gh/Horttcore/wp-block)

A composer wrapper for ServerSideRender blocks

## Installation

`$ composer require ralfhortt/wp-block`

## Usage

There are two main ways to register WordPress blocks with this package:

1. **BlockManifest** - For registering multiple blocks from a manifest file (recommended for modern block development)
2. **Block** - For programmatic block registration with custom PHP rendering

## Examples

### BlockManifest Example

Use `BlockManifest` when you have a `block.json` file or a manifest that describes multiple blocks. This is the modern WordPress approach and integrates well with build tools.

```php
<?php
use RalfHortt\WPBlock\BlockManifest;

// Register blocks from a manifest file
$manifest = new BlockManifest(
    manifestPath: __DIR__ . '/build/block-manifest.json',
    blocksPath: __DIR__ . '/build/blocks'  // Optional: defaults to manifest directory
);

// Register the service (typically in your plugin's main file)
$manifest->register();
```

**Example manifest structure:**

```json
{
  "blocks": [
    {
      "name": "my-plugin/hero-block",
      "title": "Hero Block",
      "category": "widgets",
      "path": "./hero-block"
    },
    {
      "name": "my-plugin/testimonial-block",
      "title": "Testimonial Block",
      "category": "widgets",
      "path": "./testimonial-block"
    }
  ]
}
```

**Directory structure example:**

```
build/
├── block-manifest.json
└── blocks/
    ├── hero-block/
    │   ├── block.json
    │   ├── index.js
    │   └── style.css
    └── testimonial-block/
        ├── block.json
        ├── index.js
        └── style.css
```

### Block Class Examples

Use the `Block` class for custom PHP-rendered blocks with full control over the rendering process.

```php
<?php
use RalfHortt\WPBlock\Block;

class MyBlock extends Block {
	protected $name = 'ralfhortt/myblock';
	protected $attributes = [
		'postType' => [
			'type' => 'string',
			'default' => '',
		],
		// …
	];

	protected function render($atts, $content): void
	{
		$query = new WP_Query([
			'post_type' => $atts['postType'],
			'showposts' => 5,
		]);

		if ( $query->have_posts()) {
			while ( $query->have_posts() ) {
				$query->the_post();
				the_title();
			}
		}

		wp_reset_query();
	}
}

class MyOtherBlock extends Block {
	protected string $name = 'ralfhortt/myotherblock';
	protected string $title = 'My other Block';
	protected string $blockJson = 'block.json';
	// …
}

// Register your blocks (typically in your plugin's main file)
$myBlock = new MyBlock();
$myBlock->register();

$myOtherBlock = new MyOtherBlock();
$myOtherBlock->register();
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

````

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

## Development

### Requirements

- PHP 8.1 or higher
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
````

### Code Quality

This project uses:

- **PestPHP** for testing
- **PHPStan** for static analysis (level 8)
- **GitHub Actions** for continuous integration
- **Brain\Monkey** for WordPress function mocking

## Changelog

### v2.0 - 2025-10-11

- Adding support for block manifest

### v1.2 - 2022-05-10

- Add support for `block.json`

### v1.1 - 2020-05-25

- Apply fixes from StyleCI

### v1.0 - 2020-01-17

- Initial release
