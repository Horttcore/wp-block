# BlockManager

Configure **supports**, **styles**, and **variations** for one block without repeating its name.

## When to use

- You tweak the same core block in several ways (supports + remove style + add variation).
- A single `BlockSupports` / `BlockStyles` / `BlockVariations` instance is not enough and repeating `'core/button'` everywhere is noisy.

For one concern only, use the dedicated class instead.

## Usage

```php
use RalfHortt\WPBlock\BlockManager;

$manager = new BlockManager('core/image');

$manager->addSupports(['color' => true, 'alignment' => true]);
$manager->removeSupports(['spacing']);
$manager->removeStyle('outline');
$manager->addVariation([
    'name' => 'hero-image',
    'title' => 'Hero Image',
    'attributes' => ['align' => 'wide'],
]);

$manager->register();
```

Chained:

```php
(new BlockManager('core/button'))
    ->addSupports(['color' => true])
    ->removeStyle('outline')
    ->addVariation(['name' => 'cta', 'title' => 'CTA Button'])
    ->register();
```

## Methods

Delegates to internal managers; block name is implied for style and variation methods:

- `addSupports` / `removeSupports`
- `addStyle` / `addStyles` / `removeStyle` / `removeAllStyles`
- `addVariation` / `addVariations` / `removeVariation` / `removeAllVariations`
- `register(): void` — registers all three sub-managers
- `getBlockName(): string`

Block name is validated as `namespace/name` in the constructor.

## Related docs

- [BlockSupports](block-supports.md)
- [BlockStyles](block-styles.md)
- [BlockVariations](block-variations.md)
