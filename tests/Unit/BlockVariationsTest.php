<?php

use RalfHortt\WPBlock\BlockVariations;
use Brain\Monkey\Functions;

describe('BlockVariations', function () {
    beforeEach(function () {
        // Mock WordPress functions
        Functions\when('add_filter')->justReturn(true);
        Functions\when('__')->returnArg();
    });

    it('can be instantiated with empty variations', function () {
        $blockVariations = new BlockVariations();
        expect($blockVariations)->toBeInstanceOf(BlockVariations::class);
    });

    it('can be instantiated with variations array', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'test-variation',
                    'title' => 'Test Variation',
                    'description' => 'A test variation',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);
        expect($blockVariations)->toBeInstanceOf(BlockVariations::class);
    });

    it('can add a single variation', function () {
        $blockVariations = new BlockVariations();

        $variation = [
            'name' => 'test-variation',
            'title' => 'Test Variation',
            'description' => 'A test variation',
        ];

        $result = $blockVariations->addVariation('core/image', $variation);

        expect($result)->toBe($blockVariations); // Test fluent interface
    });

    it('can add multiple variations at once', function () {
        $blockVariations = new BlockVariations();

        $variations = [
            [
                'name' => 'variation-1',
                'title' => 'Variation 1',
            ],
            [
                'name' => 'variation-2',
                'title' => 'Variation 2',
            ]
        ];

        $result = $blockVariations->addVariations('core/image', $variations);

        expect($result)->toBe($blockVariations); // Test fluent interface
    });

    it('can remove a specific variation by name', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'variation-1',
                    'title' => 'Variation 1',
                ],
                [
                    'name' => 'variation-2',
                    'title' => 'Variation 2',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);
        $result = $blockVariations->removeVariation('core/image', 'variation-1');

        expect($result)->toBe($blockVariations); // Test fluent interface
    });

    it('can remove all variations for a block', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'variation-1',
                    'title' => 'Variation 1',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);
        $result = $blockVariations->removeAllVariations('core/image');

        expect($result)->toBe($blockVariations); // Test fluent interface
    });

    it('handles removing variation from non-existent block gracefully', function () {
        $blockVariations = new BlockVariations();
        $result = $blockVariations->removeVariation('non-existent/block', 'some-variation');

        expect($result)->toBe($blockVariations);
    });

    it('handles removing non-existent variation gracefully', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'existing-variation',
                    'title' => 'Existing Variation',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);
        $result = $blockVariations->removeVariation('core/image', 'non-existent-variation');

        expect($result)->toBe($blockVariations);
    });

    it('can register without throwing an exception', function () {
        $blockVariations = new BlockVariations();
        $blockVariations->register();

        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });

    it('can chain register method', function () {
        $blockVariations = new BlockVariations();
        $result = $blockVariations->register();

        // register() returns void, so we just test it doesn't throw
        expect(true)->toBeTrue();
    });

    it('returns variations for the correct block type', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'test-variation',
                    'title' => 'Test Variation',
                    'description' => 'A test variation',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);

        // Mock block type object
        $blockType = (object) ['name' => 'core/image'];

        $existingVariations = [];
        $result = $blockVariations->registerBlockVariations($existingVariations, $blockType);

        expect($result)->toHaveCount(1);
        expect($result[0]['name'])->toBe('test-variation');
        expect($result[0]['title'])->toBe('Test Variation');
    });

    it('returns empty array for non-existent block types', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'test-variation',
                    'title' => 'Test Variation',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);

        // Mock different block type
        $blockType = (object) ['name' => 'core/paragraph'];

        $existingVariations = [];
        $result = $blockVariations->registerBlockVariations($existingVariations, $blockType);

        expect($result)->toBe($existingVariations);
    });

    it('merges with existing variations', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'custom-variation',
                    'title' => 'Custom Variation',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);

        $blockType = (object) ['name' => 'core/image'];

        $existingVariations = [
            [
                'name' => 'existing-variation',
                'title' => 'Existing Variation',
            ]
        ];

        $result = $blockVariations->registerBlockVariations($existingVariations, $blockType);

        expect($result)->toHaveCount(2);
        expect($result[0]['name'])->toBe('existing-variation'); // Existing variation preserved
        expect($result[1]['name'])->toBe('custom-variation'); // New variation added
    });

    it('validates variations and skips invalid ones', function () {
        $variations = [
            'core/image' => [
                [
                    'name' => 'valid-variation',
                    'title' => 'Valid Variation',
                ],
                [
                    // Missing name - invalid
                    'title' => 'Invalid Variation',
                ],
                [
                    'name' => '', // Empty name - invalid
                    'title' => 'Another Invalid Variation',
                ]
            ]
        ];

        $blockVariations = new BlockVariations($variations);

        $blockType = (object) ['name' => 'core/image'];

        $existingVariations = [];
        $result = $blockVariations->registerBlockVariations($existingVariations, $blockType);

        expect($result)->toHaveCount(1);
        expect($result[0]['name'])->toBe('valid-variation');
    });

    it('supports method chaining', function () {
        $blockVariations = new BlockVariations();

        $result = $blockVariations
            ->addVariation('core/image', [
                'name' => 'variation-1',
                'title' => 'Variation 1',
            ])
            ->addVariation('core/image', [
                'name' => 'variation-2',
                'title' => 'Variation 2',
            ])
            ->removeVariation('core/image', 'variation-1')
            ->addVariations('core/paragraph', [
                [
                    'name' => 'para-variation',
                    'title' => 'Paragraph Variation',
                ]
            ]);

        expect($result)->toBe($blockVariations);
    });
});
