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
     * Private constructor. Use the static for() method instead.
     *
     * @param string $blockName The block name in 'namespace/name' format
     */
    private function __construct(string $blockName)
    {
        $this->blockName = $blockName;
        $this->supportManager = BlockSupports::for($blockName);
        $this->styleManager = new BlockStyles();
        $this->variationManager = new BlockVariations();
    }

    /**
     * Create a new BlockManager instance for a specific block.
     *
     * @param string $blockName The block name in 'namespace/name' format
     *
     * @return self
     *
     * @throws \InvalidArgumentException If block name format is invalid
     */
    public static function for(string $blockName): self
    {
        return new self($blockName);
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
     * @param array $supports Supports to add/merge
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
     * @param string|array $supportKeys Support key(s) to remove
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
     * Add a style to a block.
     *
     * @param string $blockName The block name
     * @param array  $style     The style configuration
     *
     * @return self
     */
    public function addStyle(string $blockName, array $style): self
    {
        $this->styleManager->addStyle($blockName, $style);

        return $this;
    }

    /**
     * Add multiple styles to a block.
     *
     * @param string $blockName The block name
     * @param array  $styles    Array of style configurations
     *
     * @return self
     */
    public function addStyles(string $blockName, array $styles): self
    {
        $this->styleManager->addStyles($blockName, $styles);

        return $this;
    }

    /**
     * Remove a specific style from a block.
     *
     * @param string $blockName  The block name
     * @param string $styleName  The style name to remove
     *
     * @return self
     */
    public function removeStyle(string $blockName, string $styleName): self
    {
        $this->styleManager->removeStyle($blockName, $styleName);

        return $this;
    }

    /**
     * Remove all styles from a block.
     *
     * @param string $blockName The block name
     *
     * @return self
     */
    public function removeAllStyles(string $blockName): self
    {
        $this->styleManager->removeAllStyles($blockName);

        return $this;
    }

    // BlockVariations delegation methods

    /**
     * Add a variation to a block.
     *
     * @param string $blockName The block name
     * @param array  $variation The variation configuration
     *
     * @return self
     */
    public function addVariation(string $blockName, array $variation): self
    {
        $this->variationManager->addVariation($blockName, $variation);

        return $this;
    }

    /**
     * Add multiple variations to a block.
     *
     * @param string $blockName   The block name
     * @param array  $variations  Array of variation configurations
     *
     * @return self
     */
    public function addVariations(string $blockName, array $variations): self
    {
        $this->variationManager->addVariations($blockName, $variations);

        return $this;
    }

    /**
     * Remove a specific variation from a block.
     *
     * @param string $blockName       The block name
     * @param string $variationName   The variation name to remove
     *
     * @return self
     */
    public function removeVariation(string $blockName, string $variationName): self
    {
        $this->variationManager->removeVariation($blockName, $variationName);

        return $this;
    }

    /**
     * Remove all variations from a block.
     *
     * @param string $blockName The block name
     *
     * @return self
     */
    public function removeAllVariations(string $blockName): self
    {
        $this->variationManager->removeAllVariations($blockName);

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
