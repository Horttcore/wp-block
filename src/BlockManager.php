<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockManager implements ServiceContract
{
    /**
     * The block name being managed.
     *
     * @var string
     */
    protected string $blockName;

    /**
     * The BlockSupports manager for this block.
     *
     * @var BlockSupports
     */
    protected BlockSupports $supportManager;

    /**
     * The BlockStyles manager for this block.
     *
     * @var BlockStyles
     */
    protected BlockStyles $styleManager;

    /**
     * The BlockVariations manager for this block.
     *
     * @var BlockVariations
     */
    protected BlockVariations $variationManager;

    /**
     * Create a new BlockManager instance for a specific block.
     *
     * @param string $blockName The block name in 'namespace/name' format (e.g., 'core/button')
     *
     * @throws \InvalidArgumentException If block name format is invalid
     */
    public function __construct(string $blockName)
    {
        $this->blockName = $blockName;
        $this->supportManager = new BlockSupports($blockName);
        $this->styleManager = new BlockStyles();
        $this->variationManager = new BlockVariations();
    }

    /**
     * Get the block name being managed.
     *
     * @return string
     */
    public function getBlockName(): string
    {
        return $this->blockName;
    }

    // BlockSupports delegation methods

    /**
     * Add or merge supports into the block.
     *
     * @param array<string|int, string|bool|array> $supports Supports to add/merge
     *
     * @return self
     */
    public function addSupports(array $supports): self
    {
        $this->supportManager->add($supports);

        return $this;
    }

    /**
     * Remove supports from the block.
     *
     * @param string|array<string> $supportKeys Support key(s) to remove
     *
     * @return self
     */
    public function removeSupports(string|array $supportKeys): self
    {
        $this->supportManager->remove($supportKeys);

        return $this;
    }

    // BlockStyles delegation methods

    /**
     * Add a style to the managed block.
     *
     * @param array{name: string, label?: string} $style The style configuration
     *
     * @return self
     */
    public function addStyle(array $style): self
    {
        $this->styleManager->add($this->blockName, $style);

        return $this;
    }

    /**
     * Add multiple styles to the managed block.
     *
     * @param array<array{name: string, label?: string}> $styles Array of style configurations
     *
     * @return self
     */
    public function addStyles(array $styles): self
    {
        $this->styleManager->addStyles($this->blockName, $styles);

        return $this;
    }

    /**
     * Remove a specific style from the managed block.
     *
     * @param string $styleName The style name to remove
     *
     * @return self
     */
    public function removeStyle(string $styleName): self
    {
        $this->styleManager->remove($this->blockName, $styleName);

        return $this;
    }

    /**
     * Remove all styles from the managed block.
     *
     * @return self
     */
    public function removeAllStyles(): self
    {
        $this->styleManager->removeAll($this->blockName);

        return $this;
    }

    // BlockVariations delegation methods

    /**
     * Add a variation to the managed block.
     *
     * @param array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool} $variation The variation configuration
     *
     * @return self
     */
    public function addVariation(array $variation): self
    {
        $this->variationManager->add($this->blockName, $variation);

        return $this;
    }

    /**
     * Add multiple variations to the managed block.
     *
     * @param array<array{name: string, title: string, description?: string, attributes?: array, innerBlocks?: array, scope?: array, isDefault?: bool}> $variations Array of variation configurations
     *
     * @return self
     */
    public function addVariations(array $variations): self
    {
        $this->variationManager->addVariations($this->blockName, $variations);

        return $this;
    }

    /**
     * Remove a specific variation from the managed block.
     *
     * @param string $variationName The variation name to remove
     *
     * @return self
     */
    public function removeVariation(string $variationName): self
    {
        $this->variationManager->remove($this->blockName, $variationName);

        return $this;
    }

    /**
     * Remove all variations from the managed block.
     *
     * @return self
     */
    public function removeAllVariations(): self
    {
        $this->variationManager->removeAll($this->blockName);

        return $this;
    }

    /**
     * Register all internal managers.
     *
     * Calls register() on the supports, styles, and variations managers.
     *
     * @return void
     */
    public function register(): void
    {
        $this->supportManager->register();
        $this->styleManager->register();
        $this->variationManager->register();
    }
}
