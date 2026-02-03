<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockVariations implements ServiceContract
{
    protected array $variationsToRemove = [];

    public function __construct(protected array $variationsToAdd = [])
    {
    }

    public function addVariation(string $blockName, array $variation): self
    {
        if (!isset($this->variationsToAdd[$blockName])) {
            $this->variationsToAdd[$blockName] = [];
        }

        $this->variationsToAdd[$blockName][] = $variation;

        return $this;
    }

    public function addVariations(string $blockName, array $variations): self
    {
        foreach ($variations as $variation) {
            $this->addVariation($blockName, $variation);
        }

        return $this;
    }

    public function removeVariation(string $blockName, string $variationName): self
    {
        if (!isset($this->variationsToRemove[$blockName])) {
            $this->variationsToRemove[$blockName] = [];
        }

        $this->variationsToRemove[$blockName][] = $variationName;

        return $this;
    }

    public function removeAllVariations(string $blockName): self
    {
        $this->variationsToRemove[$blockName] = ['*'];

        return $this;
    }

    public function register(): void
    {
        \add_filter('block_type_metadata_settings', [$this, 'filterBlockMetadata']);
        \add_filter('get_block_type_variations', [$this, 'registerBlockVariations'], 10, 2);
    }

    /**
     * Filter block metadata to remove core registered variations.
     *
     * @param array $metadata The block type metadata
     *
     * @return array The filtered metadata
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
     * Register block variations for a specific block type.
     *
     * @param array          $variations Existing variations for the block type
     * @param \WP_Block_Type $blockType  The block type object
     *
     * @return array The merged variations array
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

    protected function isValidVariation(array $variation): bool
    {
        return isset($variation['name']) && !empty($variation['name']);
    }
}
