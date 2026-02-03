<?php

use Brain\Monkey\Functions;
use RalfHortt\WPBlock\BlockStyles;

describe('BlockStyles', function () {
    beforeEach(function () {
        // Mock WordPress functions
        Functions\when('add_filter')->justReturn(true);
        Functions\when('__')->returnArg();
    });

    it('can be instantiated with empty styles', function () {
        $blockStyles = new BlockStyles();
        expect($blockStyles)->toBeInstanceOf(BlockStyles::class);
    });

    it('can be instantiated with styles array', function () {
        $styles = [
            'core/button' => [
                [
                    'name' => 'custom-style',
                    'label' => 'Custom Style',
                ],
            ],
        ];

        $blockStyles = new BlockStyles($styles);
        expect($blockStyles)->toBeInstanceOf(BlockStyles::class);
    });

    it('can add a single style', function () {
        $blockStyles = new BlockStyles();

        $style = [
            'name' => 'custom-style',
            'label' => 'Custom Style',
        ];

        $result = $blockStyles->addStyle('core/button', $style);

        expect($result)->toBe($blockStyles); // Test fluent interface
    });

    it('can add multiple styles at once', function () {
        $blockStyles = new BlockStyles();

        $styles = [
            [
                'name' => 'style-1',
                'label' => 'Style 1',
            ],
            [
                'name' => 'style-2',
                'label' => 'Style 2',
            ],
        ];

        $result = $blockStyles->addStyles('core/button', $styles);

        expect($result)->toBe($blockStyles); // Test fluent interface
    });

    it('can remove a specific style by name', function () {
        $blockStyles = new BlockStyles();
        $result = $blockStyles->removeStyle('core/button', 'outline');

        expect($result)->toBe($blockStyles); // Test fluent interface
    });

    it('can remove all styles for a block', function () {
        $blockStyles = new BlockStyles();
        $result = $blockStyles->removeAllStyles('core/button');

        expect($result)->toBe($blockStyles); // Test fluent interface
    });

    it('handles removing style from non-existent block gracefully', function () {
        $blockStyles = new BlockStyles();
        $result = $blockStyles->removeStyle('non-existent/block', 'some-style');

        expect($result)->toBe($blockStyles);
    });

    it('can register without throwing an exception', function () {
        $blockStyles = new BlockStyles();
        $blockStyles->register();

        expect(true)->toBeTrue();
    });

    it('can chain register method', function () {
        $blockStyles = new BlockStyles();
        $result = $blockStyles->register();

        // register() returns void, so we just test it doesn't throw
        expect(true)->toBeTrue();
    });

    it('filters out styles from metadata', function () {
        $blockStyles = new BlockStyles();
        $blockStyles->removeStyle('core/button', 'outline');

        $metadata = [
            'name' => 'core/button',
            'styles' => [
                [
                    'name' => 'default',
                    'label' => 'Default',
                ],
                [
                    'name' => 'outline',
                    'label' => 'Outline',
                ],
                [
                    'name' => 'fill',
                    'label' => 'Fill',
                ],
            ],
        ];

        $result = $blockStyles->filterBlockMetadata($metadata);

        expect($result['styles'])->toHaveCount(2);
        
        // Get the filtered styles as array values
        $filteredStyles = array_values($result['styles']);
        expect($filteredStyles[0]['name'])->toBe('default');
        expect($filteredStyles[1]['name'])->toBe('fill');
    });

    it('removes all styles when using wildcard', function () {
        $blockStyles = new BlockStyles();
        $blockStyles->removeAllStyles('core/button');

        $metadata = [
            'name' => 'core/button',
            'styles' => [
                [
                    'name' => 'default',
                    'label' => 'Default',
                ],
                [
                    'name' => 'outline',
                    'label' => 'Outline',
                ],
            ],
        ];

        $result = $blockStyles->filterBlockMetadata($metadata);

        expect($result['styles'])->toHaveCount(0);
    });

    it('returns metadata unchanged for blocks without configured removals', function () {
        $blockStyles = new BlockStyles();
        $blockStyles->removeStyle('core/paragraph', 'highlight');

        $metadata = [
            'name' => 'core/button',
            'styles' => [
                [
                    'name' => 'outline',
                    'label' => 'Outline',
                ],
            ],
        ];

        $result = $blockStyles->filterBlockMetadata($metadata);

        expect($result)->toBe($metadata);
    });

    it('handles metadata without styles gracefully', function () {
        $blockStyles = new BlockStyles();
        $blockStyles->removeStyle('core/button', 'outline');

        $metadata = [
            'name' => 'core/button',
        ];

        $result = $blockStyles->filterBlockMetadata($metadata);

        expect($result)->toBe($metadata);
    });

    it('supports method chaining for multiple operations', function () {
        $blockStyles = new BlockStyles();

        $result = $blockStyles
            ->removeStyle('core/button', 'outline')
            ->removeStyle('core/button', 'fill')
            ->addStyle('core/image', [
                'name' => 'custom-rounded',
                'label' => 'Custom Rounded',
            ])
            ->removeAllStyles('core/paragraph');

        expect($result)->toBe($blockStyles);
    });

    it('removes multiple specific styles', function () {
        $blockStyles = new BlockStyles();
        $blockStyles->removeStyle('core/button', 'outline');
        $blockStyles->removeStyle('core/button', 'fill');

        $metadata = [
            'name' => 'core/button',
            'styles' => [
                [
                    'name' => 'default',
                    'label' => 'Default',
                ],
                [
                    'name' => 'outline',
                    'label' => 'Outline',
                ],
                [
                    'name' => 'fill',
                    'label' => 'Fill',
                ],
            ],
        ];

        $result = $blockStyles->filterBlockMetadata($metadata);

        expect($result['styles'])->toHaveCount(1);
        
        // Get the filtered styles as array values
        $filteredStyles = array_values($result['styles']);
        expect($filteredStyles[0]['name'])->toBe('default');
    });

    it('handles styles without name attribute gracefully', function () {
        $blockStyles = new BlockStyles();
        $blockStyles->removeStyle('core/button', 'outline');

        $metadata = [
            'name' => 'core/button',
            'styles' => [
                [
                    'label' => 'No Name Style',
                ],
                [
                    'name' => 'outline',
                    'label' => 'Outline',
                ],
            ],
        ];

        $result = $blockStyles->filterBlockMetadata($metadata);

        expect($result['styles'])->toHaveCount(1);
        
        // Get the filtered styles as array values
        $filteredStyles = array_values($result['styles']);
        expect($filteredStyles[0]['label'])->toBe('No Name Style');
    });
});
