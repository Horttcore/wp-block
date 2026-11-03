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
     * @param array<string, array<array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool}>> $variationsToAdd
     */
    public function __construct(array $variationsToAdd = [])
    {
        $this->variationsToAdd = $variationsToAdd;
    }

    /**
     * @param array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool} $variation
     */
    public function add(string $blockName, array $variation): self
    {
        BlockName::validate($blockName);

        if (!isset($this->variationsToAdd[$blockName])) {
            $this->variationsToAdd[$blockName] = [];
        }

        $this->variationsToAdd[$blockName][] = $variation;

        return $this;
    }

    /**
     * @param array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool} $variation
     */
    public function addVariation(string $blockName, array $variation): self
    {
        return $this->add($blockName, $variation);
    }

    /**
     * @param array<array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool}> $variations
     */
    public function addVariations(string $blockName, array $variations): self
    {
        foreach ($variations as $variation) {
            $this->add($blockName, $variation);
        }

        return $this;
    }

    public function remove(string $blockName, string $variationName): self
    {
        BlockName::validate($blockName);

        if (!isset($this->variationsToRemove[$blockName])) {
            $this->variationsToRemove[$blockName] = [];
        }

        $this->variationsToRemove[$blockName][] = $variationName;

        return $this;
    }

    public function removeVariation(string $blockName, string $variationName): self
    {
        return $this->remove($blockName, $variationName);
    }

    public function removeAll(string $blockName): self
    {
        BlockName::validate($blockName);
        $this->variationsToRemove[$blockName] = ['*'];

        return $this;
    }

    public function removeAllVariations(string $blockName): self
    {
        return $this->removeAll($blockName);
    }

    public function register(): void
    {
        \add_filter('block_type_metadata_settings', [$this, 'filterBlockMetadata']);
        \add_filter('get_block_type_variations', [$this, 'registerBlockVariations'], 10, 2);
    }

    /**
     * @param array<string, mixed> $metadata
     *
     * @return array<string, mixed>
     */
    public function filterBlockMetadata(array $metadata): array
    {
        $blockName = $metadata['name'] ?? '';

        if (isset($this->variationsToRemove[$blockName])) {
            $toRemove = $this->variationsToRemove[$blockName];

            if (isset($metadata['variations']) && is_array($metadata['variations'])) {
                $metadata['variations'] = array_filter(
                    $metadata['variations'],
                    function ($variation) use ($toRemove) {
                        $variationName = $variation['name'] ?? '';

                        if (in_array('*', $toRemove, true)) {
                            return false;
                        }

                        return !in_array($variationName, $toRemove, true);
                    }
                );
            }
        }

        return $metadata;
    }

    /**
     * @param array<array<string, mixed>> $variations
     *
     * @return array<array<string, mixed>>
     */
    public function registerBlockVariations(array $variations, object $blockType): array
    {
        if (!property_exists($blockType, 'name') || !is_string($blockType->name)) {
            return $variations;
        }

        $blockName = $blockType->name;

        if (!isset($this->variationsToAdd[$blockName])) {
            return $variations;
        }

        foreach ($this->variationsToAdd[$blockName] as $variation) {
            if ($this->isValidVariation($variation)) {
                $variations[] = $variation;
            }
        }

        return $variations;
    }

    /**
     * @param array<string, mixed> $variation
     */
    protected function isValidVariation(array $variation): bool
    {
        return !empty($variation['name']) && is_string($variation['name']);
    }
}
