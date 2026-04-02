<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockStyles implements ServiceContract
{
    /**
     * Styles to remove by block name.
     *
     * @var array<string, array<string>>
     */
    protected array $stylesToRemove = [];

    /**
     * Styles to add by block name.
     *
     * @var array<string, array<array{name: string, label?: string}>>
     */
    protected array $stylesToAdd = [];

    /**
     * Create a new BlockStyles instance.
     *
     * @param array<string, array<array{name: string, label?: string}>> $styles Optional initial styles by block name
     */
    public function __construct(array $styles = [])
    {
        $this->stylesToAdd = $styles;
    }

    /**
     * Add a style to a block.
     *
     * @param string                              $blockName The block name (namespace/name format)
     * @param array{name: string, label?: string} $style     The style configuration
     *
     * @return self
     */
    public function add(string $blockName, array $style): self
    {
        if (!isset($this->stylesToAdd[$blockName])) {
            $this->stylesToAdd[$blockName] = [];
        }

        $this->stylesToAdd[$blockName][] = $style;

        return $this;
    }

    /**
     * Add multiple styles to a block.
     *
     * @param string                                     $blockName The block name (namespace/name format)
     * @param array<array{name: string, label?: string}> $styles    Array of style configurations
     *
     * @return self
     */
    public function addStyles(string $blockName, array $styles): self
    {
        foreach ($styles as $style) {
            $this->add($blockName, $style);
        }

        return $this;
    }

    /**
     * Remove a specific style from a block.
     *
     * @param string $blockName The block name (namespace/name format)
     * @param string $styleName The style name to remove
     *
     * @return self
     */
    public function remove(string $blockName, string $styleName): self
    {
        if (!isset($this->stylesToRemove[$blockName])) {
            $this->stylesToRemove[$blockName] = [];
        }

        $this->stylesToRemove[$blockName][] = $styleName;

        return $this;
    }

    /**
     * Remove all styles from a block.
     *
     * @param string $blockName The block name (namespace/name format)
     *
     * @return self
     */
    public function removeAll(string $blockName): self
    {
        $this->stylesToRemove[$blockName] = ['*'];

        return $this;
    }

    /**
     * Register the style removal filter with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        \add_filter('block_type_metadata_settings', [$this, 'filterBlockMetadata']);
    }

    /**
     * Filter block metadata to remove core registered styles.
     *
     * @param array<string, mixed> $metadata The block type metadata
     *
     * @return array<string, mixed> The filtered metadata
     */
    public function filterBlockMetadata(array $metadata): array
    {
        $blockName = $metadata['name'] ?? '';

        // Filter out styles from metadata
        if (isset($this->stylesToRemove[$blockName])) {
            $toRemove = $this->stylesToRemove[$blockName];

            if (isset($metadata['styles']) && is_array($metadata['styles'])) {
                $metadata['styles'] = array_filter(
                    $metadata['styles'],
                    function ($style) use ($toRemove) {
                        $styleName = $style['name'] ?? '';

                        // If removing all
                        if (in_array('*', $toRemove)) {
                            return false;
                        }

                        // Remove if in the removal list
                        return !in_array($styleName, $toRemove);
                    }
                );
            }
        }

        return $metadata;
    }
}
