<?php

namespace RalfHortt\WPBlock\Tests;

use RalfHortt\WPBlock\Block;

class TestBlock extends Block
{
    public function __construct(array $properties = [])
    {
        $this->title = $properties['title'] ?? 'Test Block';
        $this->name = $properties['name'] ?? 'test/block';
        $this->attributes = $properties['attributes'] ?? [];

        if (isset($properties['blockJson'])) {
            $this->blockJson = $properties['blockJson'];
        }
    }

    // Make protected methods public for testing
    public function hasBlockJson(): bool
    {
        return parent::hasBlockJson();
    }

    public function getName(): string
    {
        return parent::getName();
    }

    public function getTitle(): string
    {
        return parent::getTitle();
    }

    public function getAttributes(): array
    {
        return parent::getAttributes();
    }

    protected function render(array $atts, string $content): void
    {
        echo 'Test block output';
    }
}
