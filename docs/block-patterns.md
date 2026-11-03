# BlockPatterns

Register block pattern categories and optionally disable core or remote patterns.

## When to use

- Register custom pattern categories for theme-defined patterns.
- Turn off core block patterns or patterns loaded from WordPress.org.

## Usage

```php
use RalfHortt\WPBlock\BlockPatterns;

(new BlockPatterns())
    ->patternCategories([
        'featured' => __('Featured'),
        'hero' => __('Hero sections'),
    ])
    ->removeCorePatterns()
    ->disableRemotePatterns()
    ->register();
```

Categories are registered on `init` via `register_block_pattern_category()`.

## Methods

| Method | Description |
|--------|-------------|
| `patternCategories(array $categories)` | Map of category slug to label |
| `removeCorePatterns()` | Calls `remove_theme_support('core-block-patterns')` |
| `disableRemotePatterns()` | Filters `should_load_remote_block_patterns` to false |
| `register(): void` | Hooks WordPress |
| `registerCategories(array $categories)` | Public helper; normally called from `register()` |
