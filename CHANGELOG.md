# Changelog

All notable changes to this project will be documented in this file.

## [3.1.0] - 2026-02-11

### Added
- **Callback support for dynamic defaults**: Set attribute values using callbacks that receive block metadata
  ```php
  BlockDefaults::for('core/image')
      ->set('sizeSlug', fn($metadata) => wp_is_mobile() ? 'thumbnail' : 'large')
  ```
- Added `getFocusedBlocks()` method to inspect currently focused blocks in `BlockDefaults`

### Changed
- Simplified `BlockDefaults` API by removing rarely-used query methods
- Focused the class on its core purpose: setting and overriding defaults
- Callbacks are executed during `overrideAttributes()` filter with full block metadata access
- Better type hints: `set()` now accepts `callable` for dynamic values

### Removed
- **BREAKING:** Removed `has()` method - use `getDefaults()` to inspect configured blocks
- **BREAKING:** Removed `hasAttribute()` method - use `getDefaults()` to inspect attributes
- **BREAKING:** Removed `getBlockDefaults()` method - use `getDefaults()` instead (returns all blocks)

### Migration from 3.0 to 3.1
```php
// Old query methods (v3.0)
if ($defaults->has('core/image')) { ... }
if ($defaults->hasAttribute('sizeSlug')) { ... }
$blockDefaults = $defaults->getBlockDefaults('core/image');

// New approach (v3.1) - direct inspection
$allDefaults = $defaults->getDefaults();
if (isset($allDefaults['core/image'])) { ... }
if (isset($allDefaults['core/image']['sizeSlug'])) { ... }
$blockDefaults = $allDefaults['core/image'] ?? [];

// NEW: Callbacks for dynamic values
BlockDefaults::for('core/image')
    ->set('sizeSlug', function($metadata) {
        return $metadata['customFlag'] ? 'large' : 'medium';
    })
    ->register();
```

## [3.0.0] - 2026-02-11

### Changed
- **BREAKING:** `BlockDefaults` constructor is now private - use `BlockDefaults::for()` static factory method instead
- **BREAKING:** Multi-block configuration now uses array of block names instead of associative array
- **BREAKING:** All methods now return `self` for proper fluent interface support

### Added
- Laravel-style fluent API: `BlockDefaults::for('core/image')->set('attr', 'value')`
- Simplified multi-block mode: `BlockDefaults::for(['core/image', 'core/paragraph'])->set('attr', 'value')` applies to all blocks
- Mix focused and explicit mode: Set shared defaults for multiple blocks, then override specific blocks
- Clean, simple API with only essential methods: `for()`, `set()`, `remove()`, `register()`

### Removed
- **BREAKING:** Removed `addBlock()` method - use `set()` method instead
- **BREAKING:** Removed `setAttribute()` method - use `set()` method instead
- **BREAKING:** Removed `setAttributes()` method - use `set()` method instead

### Migration from 2.x to 3.0
```php
// Old API (v2.x)
(new BlockDefaults('core/image', ['sizeSlug' => 'large']))->register();
$defaults->setAttribute('core/image', 'sizeSlug', 'large');
$defaults->addBlock('core/paragraph', ['fontSize' => 'large']);

// New API (v3.0)
BlockDefaults::for('core/image')->set('sizeSlug', 'large')->register();
BlockDefaults::for('core/paragraph')->set('fontSize', 'large')->register();

// Old multi-block API (v2.x)
(new BlockDefaults([
    'core/image' => ['sizeSlug' => 'large'],
    'core/paragraph' => ['fontSize' => 'large'],
]))->register();

// New multi-block API (v3.0) - for shared defaults
BlockDefaults::for(['core/image', 'core/paragraph'])
    ->set('className', 'custom')
    ->set('core/image', 'sizeSlug', 'large') // block-specific
    ->set('core/paragraph', 'fontSize', 'large') // block-specific
    ->register();
```

## [2.4.0] - 2026-02-03

### Added
- Support for block styles with `BlockStyles` class
- Ability to remove or add block styles to existing blocks
- Support for fluent interface for style management
- Full test coverage for block styles

## [2.3.0] - 2025-12-05

### Added
- Support for block attribute defaults override with `BlockDefaults` class
- Support for single and multiple blocks in one instance
- Fluent interface for flexible attribute management
- Override defaults using WordPress `block_type_metadata` filter

## [2.2.0] - 2025-11-02

### Added
- Support for block variations with `BlockVariations` class
- Support for fluent interface for adding/removing variations
- Proper integration with WordPress `get_block_type_variations` filter
- Comprehensive test coverage for block variations

## [2.1.0] - 2025-10-22

### Added
- Support to register block pattern categories

## [2.0.0] - 2025-10-11

### Added
- Support for block manifest

---

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
