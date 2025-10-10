<?php

use RalfHortt\WPBlock\Tests\TestBlock;
use RalfHortt\WPBlock\BlockManifest;
use Brain\Monkey\Functions;

describe('WordPress Block Integration', function () {
    it('can register a block and render output', function () {
        // Create and register block
        $block = new TestBlock([
            'title' => 'Integration Test Block',
            'name' => 'integration/test-block',
            'attributes' => ['testAttr' => 'value']
        ]);

        $block->register();

        // Test callback functionality
        $output = $block->callback(['color' => 'red'], 'test content');

        expect($output)->toContain('Test block output');
    });

    it('can handle block manifest registration', function () {
        Functions\when('file_exists')->justReturn(true);
        Functions\when('dirname')->justReturn('/blocks');

        $manifest = new BlockManifest('/path/to/manifest.json');
        $manifest->register();
        $manifest->registerBlockType();

        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });

    it('handles block with block.json configuration', function () {
        $block = new TestBlock([
            'title' => 'JSON Block',
            'name' => 'json/block',
            'blockJson' => 'block.json'
        ]);

        $block->register();

        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });
});
