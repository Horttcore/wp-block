<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockDefaults implements ServiceContract
{
    /**
     * Array of block configurations.
     * Format: ['block-name' => ['attributeName' => value, ...], ...].
     */
    protected array $blockDefaults = [];

    /**
     * Constructor.
     *
     * @param string|array $blockNames The block name(s) or array of block names with their defaults
     * @param array        $overrides  Array of attributes to override with their new default values (only if blockNames is string)
     */
    public function __construct(string|array $blockNames, array $overrides = [])
    {
        if (is_string($blockNames)) {
            $this->blockDefaults[$blockNames] = $overrides;
        } elseif (is_array($blockNames)) {
            $this->blockDefaults = $blockNames;
        }
    }

    /**
     * Register the override by hooking into WordPress.
     */
    public function register(): void
    {
        add_filter('block_type_metadata', [$this, 'overrideAttributes'], 10, 1);
    }

    /**
     * Override block attributes.
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
                        $metadata['attributes'][$attributeName]['default'] = $defaultValue;
                    }
                }
            }
        }

        return $metadata;
    }

    /**
     * Add a block with default attributes.
     *
     * @param string $blockName The block name (e.g., 'namespace/block-name')
     * @param array  $overrides Array of attributes to override with their new default values
     */
    public function addBlock(string $blockName, array $overrides = []): self
    {
        $this->blockDefaults[$blockName] = $overrides;

        return $this;
    }

    /**
     * Set an attribute override for a specific block.
     *
     * @param string $blockName     The block name
     * @param string $attributeName The attribute name to override
     * @param mixed  $defaultValue  The new default value
     */
    public function setAttribute(string $blockName, string $attributeName, $defaultValue): self
    {
        if (!isset($this->blockDefaults[$blockName])) {
            $this->blockDefaults[$blockName] = [];
        }

        $this->blockDefaults[$blockName][$attributeName] = $defaultValue;

        return $this;
    }

    /**
     * Set multiple attribute overrides for a specific block.
     *
     * @param string $blockName The block name
     * @param array  $overrides Array of attribute names and their new default values
     */
    public function setAttributes(string $blockName, array $overrides): self
    {
        if (!isset($this->blockDefaults[$blockName])) {
            $this->blockDefaults[$blockName] = [];
        }

        $this->blockDefaults[$blockName] = array_merge(
            $this->blockDefaults[$blockName],
            $overrides
        );

        return $this;
    }

    /**
     * Get all block defaults.
     */
    public function getDefaults(): array
    {
        return $this->blockDefaults;
    }

    /**
     * Get defaults for a specific block.
     *
     * @param string $blockName The block name
     */
    public function getBlockDefaults(string $blockName): array
    {
        return $this->blockDefaults[$blockName] ?? [];
    }
}
