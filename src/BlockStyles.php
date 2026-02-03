<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockStyles implements ServiceContract
{
    protected array $stylesToRemove = [];
    protected array $stylesToAdd = [];

    public function __construct(array $styles = [])
    {
        $this->stylesToAdd = $styles;
    }

    public function addStyle(string $blockName, array $style): self
    {
        if (!isset($this->stylesToAdd[$blockName])) {
            $this->stylesToAdd[$blockName] = [];
        }

        $this->stylesToAdd[$blockName][] = $style;

        return $this;
    }

    public function addStyles(string $blockName, array $styles): self
    {
        foreach ($styles as $style) {
            $this->addStyle($blockName, $style);
        }

        return $this;
    }

    public function removeStyle(string $blockName, string $styleName): self
    {
        if (!isset($this->stylesToRemove[$blockName])) {
            $this->stylesToRemove[$blockName] = [];
        }

        $this->stylesToRemove[$blockName][] = $styleName;

        return $this;
    }

    public function removeAllStyles(string $blockName): self
    {
        $this->stylesToRemove[$blockName] = ['*'];

        return $this;
    }

    public function register(): void
    {
        \add_filter('block_type_metadata_settings', [$this, 'filterBlockMetadata']);
    }

    /**
     * Filter block metadata to remove core registered styles.
     *
     * @param array $metadata The block type metadata
     *
     * @return array The filtered metadata
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
