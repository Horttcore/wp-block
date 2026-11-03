# BlockVariations

Add or remove block variations for existing block types.

## When to use

- Extend core blocks with custom inserter variations from PHP.
- Remove core variations you do not want in a theme or plugin.

Prefer declaring variations in `block.json` when you own the block.

## Usage

Constructor with initial map:

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
```

Fluent API:

```php
(new BlockVariations())
    ->addVariation('core/button', [
        'name' => 'cta-button',
        'title' => __('CTA Button'),
        'attributes' => ['className' => 'cta-button'],
    ])
    ->removeVariation('core/button', 'outline')
    ->register();
```

Lower-level names `add()`, `remove()`, and `removeAll()` are also available with the block name as the first argument.

## Variation properties

| Property | Description |
|----------|-------------|
| `name` (required) | Unique identifier |
| `title` | Inserter label |
| `description` | Help text |
| `attributes` | Default attribute values |
| `innerBlocks` | Default inner blocks for container blocks |
| `scope` | `['inserter']`, `['block']`, or both |
| `isDefault` | Mark as default variation |

See [WordPress block variations](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/).

## WordPress hooks

- `block_type_metadata_settings` — removes variations from metadata.
- `get_block_type_variations` — appends registered variations.
