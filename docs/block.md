# Block (abstract class)

Base class for custom blocks with a PHP `render_callback` and optional `block.json`.

## When to use

- Legacy or hybrid blocks rendered in PHP while metadata lives in `block.json`.
- You need actions/filters around render output (`before` / `after` / `render`).

For manifest-based block folders, prefer [BlockManifest](block-manifest.md).

## Usage

```php
use RalfHortt\WPBlock\Block;

class MyBlock extends Block {
    protected string $name = 'myplugin/custom-block';
    protected string $title = 'My Custom Block';
    protected string $blockJson = 'block.json';

    protected function render(array $attributes, string $content): void {
        echo '<div class="my-block">' . esc_html($attributes['title'] ?? '') . '</div>';
    }
}

(new MyBlock())->register();
```

Without `block.json`, set `$name`, `$title`, and optionally `$attributes`; registration passes them to `register_block_type()`.

## Paths

Relative `block.json` paths resolve next to the **subclass** file (via reflection). Absolute paths are used as-is.

## Extension points

| Hook | Type | Purpose |
|------|------|---------|
| `{blockName}/attributes` | Filter | Block attribute definitions |
| `{blockName}/before` | Action | Before render output |
| `{blockName}/after` | Action | After render output |
| `{blockName}/render` | Filter | Filter buffered render output |

Override `args(): array` to pass extra arguments to `register_block_type()`.
