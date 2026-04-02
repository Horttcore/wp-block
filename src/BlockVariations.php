<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockVariations implements ServiceContract
{
    /**
     * Variations to remove by block name.
     *
     * @var array<string, array<string>>
     */
    protected array $variationsToRemove = [];

    /**
     * Variations to add by block name.
     *
     * @var array<string, array<array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool}>>
     */
    protected array $variationsToAdd = [];

    /**
     * Create a new BlockVariations instance.
     *
     * @param array<string, array<array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool}>> $variationsToAdd Optional initial variations by block name
     */
    public function __construct(protected array $variationsToAdd = [])
    {
    }

    /**
     * Add a variation to a block.
     *
     * @param string $blockName The block name (namespace/name format)
     * @param array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool} $variation The variation configuration
     *
     * @return self
     */
    public function add(string $blockName, array $variation): self
    {
        if (!isset($this->variationsToAdd[$blockName])) {
            $this->variationsToAdd[$blockName] = [];
        }

        $this->variationsToAdd[$blockName][] = $variation;

        return $this;
    }

    /**
     * Add multiple variations to a block.
     *
     * @param string $blockName The block name (namespace/name format)
     * @param array<array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool}> $variations Array of variation configurations
     *
     * @return self
     */
    public function addVariations(string $blockName, array $variations): self
    {
        foreach ($variations as $variation) {
            $this->add($blockName, $variation);
        }

        return $this;
    }

    /**
     * Remove a specific variation from a block.
     *
     * @param string $blockName The block name (namespace/name format)
     * @param string $variationName The variation name to remove
     *
     * @return self
     */
    public function remove(string $blockName, string $variationName): self
    {
        if (!isset($this->variationsToRemove[$blockName])) {
            $this->variationsToRemove[$blockName] = [];
        }

        $this->variationsToRemove[$blockName][] = $variationName;

        return $this;
    }

    /**
     * Remove all variations from a block.
     *
     * @param string $blockName The block name (namespace/name format)
     *
     * @return self
     */
    public function removeAll(string $blockName): self
    {
        $this->variationsToRemove[$blockName] = ['*'];

        return $this;
    }

    /**
     * Register the variation filters with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        \add_filter('block_type_metadata_settings', [$this, 'filterBlockMetadata']);
        \add_filter('get_block_type_variations', [$this, 'registerBlockVariations'], 10, 2);
    }

    /**
     * Filter block metadata to remove core registered variations.
     *
     * @param array<string, mixed> $metadata The block type metadata
     *
     * @return array<string, mixed> The filtered metadata
     */
    public function filterBlockMetadata(array $metadata): array
    {
        $blockName = $metadata['name'] ?? '';

        // Filter out variations from metadata
        if (isset($this->variationsToRemove[$blockName])) {
            $toRemove = $this->variationsToRemove[$blockName];

            if (isset($metadata['variations']) && is_array($metadata['variations'])) {
                $metadata['variations'] = array_filter(
                    $metadata['variations'],
                    function ($variation) use ($toRemove) {
                        $variationName = $variation['name'] ?? '';

                        // If removing all
                        if (in_array('*', $toRemove)) {
                            return false;
                        }

                        // Remove if in the removal list
                        return !in_array($variationName, $toRemove);
                    }
                );
            }
        }

        return $metadata;
    }

    /**
     * Register custom variations for a block type.
     *
     * @param array<array<string, mixed>> $variations The existing variations
     * @param object $blockType The block type object
     *
     * @return array<array<string, mixed>> The merged variations array
     */
    public function registerBlockVariations(array $variations, $blockType): array
    {
        // Check if we have variations to add for this specific block type
        if (!isset($this->variationsToAdd[$blockType->name])) {
            return $variations;
        }

        // Get our variations for this block type
        $blockVariations = $this->variationsToAdd[$blockType->name];

        // Filter out invalid variations and merge with existing variations
        foreach ($blockVariations as $variation) {
            if ($this->isValidVariation($variation)) {
                $variations[] = $variation;
            }
        }

        return $variations;
    }

    /**
     * Validate that a variation has the required fields.
     *
     * @param array<string, mixed> $variation The variation to validate
     *
     * @return bool True if valid, false otherwise
     */
    protected function isValidVariation(array $variation): bool
    {
        return !empty($variation['name']) && is_string($variation['name']);
    }
}
