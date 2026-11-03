# BlockStyles

Remove block styles from block metadata, or register additional styles via the metadata filter.

## When to use

- Strip core block styles (e.g. button outline) in a theme or plugin.
- Register extra styles in PHP when you are not using `theme.json` / `block.json`.

**Best practice:** prefer adding styles in [`theme.json`](https://developer.wordpress.org/themes/global-settings-and-styles/) or each block’s `block.json`. Use this class mainly for **removals** or legacy PHP-only setups.

## Usage

```php
use RalfHortt\WPBlock\BlockStyles;

(new BlockStyles())
    ->removeStyle('core/button', 'outline')
    ->removeAllStyles('core/separator')
    ->register();
```

Add styles (when needed):

```php
(new BlockStyles())
    ->addStyle('core/image', [
        'name' => 'rounded',
        'label' => __('Rounded'),
    ])
    ->register();
```

Aliases: `addStyle`, `removeStyle`, `removeAllStyles`. Generic `add()`, `remove()`, and `removeAll()` accept the block name first.

## WordPress hooks

- `block_type_metadata_settings` — merges added styles and filters removed ones.
