# WordPress hooks reference

Hooks used by this package and extension points on [Block](block.md).

## Block class (custom blocks)

| Hook | Type |
|------|------|
| `{blockName}/before` | Action |
| `{blockName}/after` | Action |
| `{blockName}/render` | Filter |
| `{blockName}/attributes` | Filter |

## Internal filters by class

| Filter | Used by |
|--------|---------|
| `block_type_metadata` | [BlockDefaults](block-defaults.md) |
| `block_type_metadata_settings` | [BlockStyles](block-styles.md), [BlockVariations](block-variations.md) |
| `get_block_type_variations` | [BlockVariations](block-variations.md) |
| `register_block_type_args` | [BlockSupports](block-supports.md) |
| `should_load_remote_block_patterns` | [BlockPatterns](block-patterns.md) |

[BlockManifest](block-manifest.md) relies on core registration APIs on `init`.
