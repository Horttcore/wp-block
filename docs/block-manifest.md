# BlockManifest

Register multiple blocks from a [WordPress block metadata collection](https://developer.wordpress.org/reference/functions/wp_register_block_types_from_metadata_collection/) (manifest + `block.json` files).

## When to use

- You build blocks with `@wordpress/scripts` (or similar) and ship a `blocks-manifest.php` under `build/`.
- You want one registration call instead of listing each block in PHP.

## Usage

```php
use RalfHortt\WPBlock\BlockManifest;

(new BlockManifest(__DIR__ . '/build/blocks/blocks-manifest.php'))->register();
```

Optional second argument: base path to block folders (defaults to the manifest directory):

```php
(new BlockManifest($manifestPath, $blocksPath))->register();
```

## Behavior

- Throws `RuntimeException` if the manifest file does not exist.
- Hooks `init` and calls `wp_register_block_types_from_metadata_collection()`.

## WordPress hooks

Uses core block registration; no custom filters in this class.
