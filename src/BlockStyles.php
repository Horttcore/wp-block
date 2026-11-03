<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockStyles implements ServiceContract
{
    /**
     * @var array<string, array<string>>
     */
    protected array $stylesToRemove = [];

    /**
     * @var array<string, array<array{name: string, label?: string}>>
     */
    protected array $stylesToAdd = [];

    /**
     * @param array<string, array<array{name: string, label?: string}>> $styles
     */
    public function __construct(array $styles = [])
    {
        $this->stylesToAdd = $styles;
    }

    /**
     * @param array{name: string, label?: string} $style
     */
    public function add(string $blockName, array $style): self
    {
        BlockName::validate($blockName);

        if (!isset($this->stylesToAdd[$blockName])) {
            $this->stylesToAdd[$blockName] = [];
        }

        $this->stylesToAdd[$blockName][] = $style;

        return $this;
    }

    /**
     * @param array{name: string, label?: string} $style
     */
    public function addStyle(string $blockName, array $style): self
    {
        return $this->add($blockName, $style);
    }

    /**
     * @param array<array{name: string, label?: string}> $styles
     */
    public function addStyles(string $blockName, array $styles): self
    {
        foreach ($styles as $style) {
            $this->add($blockName, $style);
        }

        return $this;
    }

    public function remove(string $blockName, string $styleName): self
    {
        BlockName::validate($blockName);

        if (!isset($this->stylesToRemove[$blockName])) {
            $this->stylesToRemove[$blockName] = [];
        }

        $this->stylesToRemove[$blockName][] = $styleName;

        return $this;
    }

    public function removeStyle(string $blockName, string $styleName): self
    {
        return $this->remove($blockName, $styleName);
    }

    public function removeAll(string $blockName): self
    {
        BlockName::validate($blockName);
        $this->stylesToRemove[$blockName] = ['*'];

        return $this;
    }

    public function removeAllStyles(string $blockName): self
    {
        return $this->removeAll($blockName);
    }

    public function register(): void
    {
        \add_filter('block_type_metadata_settings', [$this, 'filterBlockMetadata']);
    }

    /**
     * @param array<string, mixed> $metadata
     *
     * @return array<string, mixed>
     */
    public function filterBlockMetadata(array $metadata): array
    {
        $blockName = $metadata['name'] ?? '';

        if (isset($this->stylesToAdd[$blockName]) && $this->stylesToAdd[$blockName] !== []) {
            if (!isset($metadata['styles']) || !is_array($metadata['styles'])) {
                $metadata['styles'] = [];
            }

            foreach ($this->stylesToAdd[$blockName] as $style) {
                $metadata['styles'][] = $style;
            }
        }

        if (isset($this->stylesToRemove[$blockName])) {
            $toRemove = $this->stylesToRemove[$blockName];

            if (isset($metadata['styles']) && is_array($metadata['styles'])) {
                $metadata['styles'] = array_filter(
                    $metadata['styles'],
                    function ($style) use ($toRemove) {
                        $styleName = $style['name'] ?? '';

                        if (in_array('*', $toRemove, true)) {
                            return false;
                        }

                        return !in_array($styleName, $toRemove, true);
                    }
                );
            }
        }

        return $metadata;
    }
}
