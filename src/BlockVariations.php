<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockVariations implements ServiceContract
{
    public function __construct(protected array $variations = [])
    {
    }

    public function addVariation(string $blockName, array $variation): self
    {
        if (!isset($this->variations[$blockName])) {
            $this->variations[$blockName] = [];
        }

        $this->variations[$blockName][] = $variation;

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
        if (!isset($this->variations[$blockName])) {
            return $this;
        }

        $this->variations[$blockName] = array_filter(
            $this->variations[$blockName],
            fn ($variation) => ($variation['name'] ?? '') !== $variationName
        );

        // Remove the block entry if no variations remain
        if (empty($this->variations[$blockName])) {
            unset($this->variations[$blockName]);
        }

        return $this;
    }

    public function removeAllVariations(string $blockName): self
    {
        unset($this->variations[$blockName]);

        return $this;
    }

    public function register(): void
    {
        \add_filter('get_block_type_variations', [$this, 'registerBlockVariations'], 10, 2);
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
        // Check if we have variations for this specific block type
        if (!isset($this->variations[$blockType->name])) {
            return $variations;
        }

        // Get our variations for this block type
        $blockVariations = $this->variations[$blockType->name];

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
