# BlockSupports

Merge or remove `supports` for a single block type at registration time.

## When to use

- Enable features on a core block (e.g. color on `core/group`).
- Disable supports (spacing, typography, etc.) without editing block JSON.

## Usage

```php
use RalfHortt\WPBlock\BlockSupports;

BlockSupports::for('core/image')
    ->add(['color' => true, 'alignment' => true])
    ->register();
```

Nested configuration:

```php
BlockSupports::for('core/button')
    ->add([
        'color' => [
            'palette' => [
                ['name' => 'Red', 'slug' => 'red', 'color' => '#ff0000'],
            ],
        ],
    ])
    ->register();
```

Remove supports:

```php
BlockSupports::for('core/image')
    ->remove(['spacing', 'padding', 'margin'])
    ->register();
```

Combine add and remove (removals win):

```php
BlockSupports::for('core/paragraph')
    ->add(['color' => true, 'typography' => true])
    ->remove(['spacing'])
    ->register();
```

Shorthand boolean supports:

```php
BlockSupports::for('core/paragraph')
    ->add(['color', 'typography'])
    ->register();
```

You can also use `new BlockSupports('core/image')` instead of `::for()`.

## WordPress hooks

- `register_block_type_args` — deep-merges additions, then unsets removals for the target block only.
