# BlockDefaults

Override default values for block attributes defined in block metadata, including dynamic values via callbacks.

## When to use

- Change core (or third-party) block defaults without editing their `block.json`.
- Set defaults from context (e.g. mobile vs desktop) using a callback that receives full metadata.

## Usage

### Single block

```php
use RalfHortt\WPBlock\BlockDefaults;

$defaults = BlockDefaults::for('core/image')
    ->set('sizeSlug', 'large')
    ->set('linkDestination', 'media');
$defaults->register();
```

### Callbacks

```php
$defaults = BlockDefaults::for('core/image')
    ->set('sizeSlug', fn (array $metadata) => wp_is_mobile() ? 'thumbnail' : 'large');
$defaults->register();
```

### Multiple blocks (shared + per-block)

```php
$defaults = BlockDefaults::for(['core/image', 'core/paragraph'])
    ->set('className', 'custom-block')
    ->set('core/image', 'sizeSlug', 'large')
    ->set('core/paragraph', 'fontSize', 'large');
$defaults->register();
```

## API

| Method | Description |
|--------|-------------|
| `BlockDefaults::for(string\|array $blocks)` | Factory; block names must be `namespace/name`. |
| `set(...)` | Focused mode: `set('attr', $value)` or `set(['attr' => $value])`. Explicit: `set('core/block', 'attr', $value)`. |
| `remove(?string $blockName)` | Clear focused blocks or one explicit block. |
| `removeAttribute(...)` | Remove one attribute from focused or explicit block. |
| `register(): void` | Hooks `block_type_metadata`. |
| `getDefaults()` | Inspect configured overrides. |
| `getFocusedBlocks()` | Block names from `for()`. |

Only attributes that already exist in metadata are overridden; unknown attribute names are ignored.

## WordPress hooks

- `block_type_metadata` — applies default overrides.
