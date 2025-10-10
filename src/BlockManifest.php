<?php
namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockManifest implements ServiceContract
{

    public function __construct(protected string $manifestPath, protected ?string $blocksPath = null)
    {
        if (!file_exists($this->manifestPath)) {
            throw new \RuntimeException('Block manifest file not found: ' . $this->manifestPath);
        }

        if ($this->blocksPath === null) {
            $this->blocksPath = dirname($this->manifestPath);
        }
    }

    public function register(): void
    {
        add_action('init', [$this, 'registerBlockType']);
    }

    public function registerBlockType(): void
    {
        wp_register_block_types_from_metadata_collection($this->blocksPath ?? '', $this->manifestPath);
    }
}
