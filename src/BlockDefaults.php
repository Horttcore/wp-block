<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockDefaults implements ServiceContract
{
    /**
     * Array of block configurations.
     * Format: ['block-name' => ['attributeName' => value, ...], ...].
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $blockDefaults = [];

    /**
     * The currently focused block name(s) (for single-block or multi-block mode).
     *
     * @var array<string>|null
     */
    protected ?array $focusedBlocks = null;

    /**
     * Create a new BlockDefaults instance.
     *
     * @param string|array<string>|null $blocks Optional block name(s) to focus on
     *                                           Single: 'block/name'
     *                                           Multiple: ['block/one', 'block/two']
     *                                           Null: Create empty instance
     *
     * @throws \InvalidArgumentException If block name format is invalid
     */
    public function __construct(string|array|null $blocks = null)
    {
        if ($blocks === null) {
            return;
        }

        if (is_string($blocks)) {
            // Single-block mode
            self::validateBlockName($blocks);
            $this->focusedBlocks = [$blocks];
            $this->blockDefaults[$blocks] = [];
        } else {
            // Multi-block mode: multiple block names
            foreach ($blocks as $blockName) {
                if (!is_string($blockName)) {
                    throw new \InvalidArgumentException('All block names must be strings');
                }
                self::validateBlockName($blockName);
            }

            $this->focusedBlocks = $blocks;
            foreach ($blocks as $blockName) {
                $this->blockDefaults[$blockName] = [];
            }
        }
    }

    /**
     * Validate block name format.
     *
     * @param string $blockName The block name to validate
     *
     * @throws \InvalidArgumentException If block name format is invalid
     */
    private static function validateBlockName(string $blockName): void
    {
        if (!str_contains($blockName, '/')) {
            throw new \InvalidArgumentException(
                sprintf('Invalid block name "%s". Block names must be in "namespace/name" format.', $blockName)
            );
        }

        $parts = explode('/', $blockName);
        if (count($parts) !== 2 || empty($parts[0]) || empty($parts[1])) {
            throw new \InvalidArgumentException(
                sprintf('Invalid block name "%s". Block names must be in "namespace/name" format.', $blockName)
            );
        }
    }

    /**
     * Register the override by hooking into WordPress.
     */
    public function register(): self
    {
        add_filter('block_type_metadata', [$this, 'overrideAttributes'], 10, 1);

        return $this;
    }

    /**
     * Set default attributes for block(s).
     *
     * In focused mode (when using ::for('block/name') or ::for(['block/one', 'block/two'])):
     *   - set('attr', 'value') - Sets attribute for all focused blocks
     *   - set('attr', fn($metadata) => 'value') - Sets attribute with callback for dynamic values
     *   - set(['attr' => 'value']) - Sets multiple attributes for all focused blocks
     *
     * In explicit mode:
     *   - set('block/name', 'attr', 'value') - Sets single attribute for specific block
     *   - set('block/name', ['attr' => 'value']) - Sets multiple attributes for specific block
     *
     * @param string|array               $blockOrAttribute Block name or attribute name/array (in focused mode)
     * @param string|array|callable|null $attributeOrValue Attribute name, attributes array, callback, or value
     * @param mixed                      $value            The default value or callback (only used with block name + attribute name)
     */
    public function set(string|array $blockOrAttribute, string|array|callable|null $attributeOrValue = null, mixed $value = null): self
    {
        // Detect mode by checking if first parameter is a block name (contains '/')
        $isExplicitMode = is_string($blockOrAttribute) && str_contains($blockOrAttribute, '/');

        if ($isExplicitMode) {
            // Explicit mode - apply to specific block
            $blockName = $blockOrAttribute;

            if (!isset($this->blockDefaults[$blockName])) {
                $this->blockDefaults[$blockName] = [];
            }

            if (is_array($attributeOrValue)) {
                // set('block/name', ['attr' => 'value', ...])
                $this->blockDefaults[$blockName] = array_merge(
                    $this->blockDefaults[$blockName],
                    $attributeOrValue
                );
            } elseif (is_string($attributeOrValue)) {
                // set('block/name', 'attr', 'value')
                $this->blockDefaults[$blockName][$attributeOrValue] = $value;
            }
        } else {
            // Focused mode - apply to all focused blocks
            if ($this->focusedBlocks === null) {
                throw new \LogicException('No blocks focused. Use BlockDefaults::for() first or provide a block name.');
            }

            foreach ($this->focusedBlocks as $blockName) {
                if (is_array($blockOrAttribute)) {
                    // set(['attr' => 'value', ...])
                    $this->blockDefaults[$blockName] = array_merge(
                        $this->blockDefaults[$blockName],
                        $blockOrAttribute
                    );
                } elseif (is_string($blockOrAttribute)) {
                    // set('attr', 'value')
                    $this->blockDefaults[$blockName][$blockOrAttribute] = $attributeOrValue;
                }
            }
        }

        return $this;
    }

    /**
     * Remove all defaults for block(s).
     *
     * In focused mode: remove() removes all focused blocks
     * In explicit mode: remove('block/name') removes specified block
     *
     * @param string|null $blockName The block name (optional in focused mode)
     */
    public function remove(?string $blockName = null): self
    {
        if ($blockName === null && $this->focusedBlocks !== null) {
            // Remove all focused blocks
            foreach ($this->focusedBlocks as $block) {
                unset($this->blockDefaults[$block]);
            }
        } elseif ($blockName !== null) {
            // Remove specific block
            unset($this->blockDefaults[$blockName]);
        }

        return $this;
    }

    /**
     * Remove a specific attribute default from block(s).
     *
     * In focused mode: removeAttribute('attr') removes from all focused blocks
     * In explicit mode: removeAttribute('block/name', 'attr') removes from specified block
     *
     * @param string      $blockOrAttribute Block name or attribute name (in focused mode)
     * @param string|null $attributeName    The attribute name (only in explicit mode)
     */
    public function removeAttribute(string $blockOrAttribute, ?string $attributeName = null): self
    {
        // Detect if first param is a block name (contains '/')
        if (str_contains($blockOrAttribute, '/')) {
            // Explicit mode: removeAttribute('block/name', 'attr')
            if (isset($this->blockDefaults[$blockOrAttribute][$attributeName])) {
                unset($this->blockDefaults[$blockOrAttribute][$attributeName]);
            }
        } else {
            // Focused mode: removeAttribute('attr')
            if ($this->focusedBlocks !== null) {
                foreach ($this->focusedBlocks as $blockName) {
                    if (isset($this->blockDefaults[$blockName][$blockOrAttribute])) {
                        unset($this->blockDefaults[$blockName][$blockOrAttribute]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Override block attributes.
     *
     * Executes any callbacks with the full metadata for dynamic values.
     *
     * @param array $metadata The block metadata
     *
     * @return array Modified metadata with overridden attributes
     */
    public function overrideAttributes(array $metadata): array
    {
        // Check if this block is in our defaults list
        if (isset($metadata['name']) && isset($this->blockDefaults[$metadata['name']])) {
            $overrides = $this->blockDefaults[$metadata['name']];

            // Merge default values into attributes
            if (isset($metadata['attributes']) && is_array($metadata['attributes'])) {
                foreach ($overrides as $attributeName => $defaultValue) {
                    if (isset($metadata['attributes'][$attributeName])) {
                        // Execute callback if provided for dynamic values
                        if (is_callable($defaultValue)) {
                            $defaultValue = call_user_func($defaultValue, $metadata);
                        }

                        $metadata['attributes'][$attributeName]['default'] = $defaultValue;
                    }
                }
            }
        }

        return $metadata;
    }

    /**
     * Get all configured block defaults.
     *
     * @return array All block defaults ['block/name' => ['attr' => value, ...], ...]
     */
    public function getDefaults(): array
    {
        return $this->blockDefaults;
    }

    /**
     * Get the currently focused block names.
     *
     * @return array Array of focused block names
     */
    public function getFocusedBlocks(): array
    {
        return $this->focusedBlocks ?? [];
    }
}
