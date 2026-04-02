<?php

use Brain\Monkey\Functions;
use RalfHortt\WPBlock\BlockSupports;

describe('BlockSupports', function () {
    beforeEach(function () {
        // Mock WordPress functions
        Functions\when('add_filter')->justReturn(true);
    });

    it('can be instantiated using constructor', function () {
        $blockSupports = new BlockSupports('core/image');
        expect($blockSupports)->toBeInstanceOf(BlockSupports::class);
    });

    it('validates block name format in constructor', function () {
        expect(function () {
            new BlockSupports('invalid-block');
        })->toThrow(\InvalidArgumentException::class);
    });

    it('validates block name has namespace and name', function () {
        expect(function () {
            new BlockSupports('core/');
        })->toThrow(\InvalidArgumentException::class);

        expect(function () {
            new BlockSupports('/image');
        })->toThrow(\InvalidArgumentException::class);
    });

    it('can add supports with associative array', function () {
        $blockSupports = new BlockSupports('core/image');
        $result = $blockSupports->add(['color' => true, 'alignment' => true]);

        expect($result)->toBe($blockSupports); // Test fluent interface
        expect($blockSupports->getSupportsToAdd())->toHaveKey('color');
        expect($blockSupports->getSupportsToAdd())->toHaveKey('alignment');
    });

    it('can add supports with nested configuration', function () {
        $blockSupports = new BlockSupports('core/image');
        $colorConfig = ['palette' => ['red', 'blue', 'green']];
        $blockSupports->add(['color' => $colorConfig]);

        expect($blockSupports->getSupportsToAdd()['color'])->toBe($colorConfig);
    });

    it('can add supports with string shorthand (boolean)', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add(['color', 'typography', 'spacing']);

        $supports = $blockSupports->getSupportsToAdd();
        expect($supports['color'])->toBe(true);
        expect($supports['typography'])->toBe(true);
        expect($supports['spacing'])->toBe(true);
    });

    it('can add mixed string and associative supports', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add([
            'color' => ['palette' => ['red', 'blue']],
            'typography',
            'spacing' => true,
        ]);

        $supports = $blockSupports->getSupportsToAdd();
        expect($supports)->toHaveKey('color');
        expect($supports)->toHaveKey('typography');
        expect($supports)->toHaveKey('spacing');
    });

    it('can remove supports with string', function () {
        $blockSupports = new BlockSupports('core/image');
        $result = $blockSupports->remove('spacing');

        expect($result)->toBe($blockSupports); // Test fluent interface
        expect($blockSupports->getSupportsToRemove())->toContain('spacing');
    });

    it('can remove supports with array', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->remove(['spacing', 'padding', 'margin']);

        expect($blockSupports->getSupportsToRemove())->toContain('spacing');
        expect($blockSupports->getSupportsToRemove())->toContain('padding');
        expect($blockSupports->getSupportsToRemove())->toContain('margin');
    });

    it('can register without throwing an exception', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->register();

        expect(true)->toBeTrue();
    });

    it('filters block type args for the correct block', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add(['color' => true]);

        $args = [
            'supports' => ['alignment' => true],
        ];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/image');

        expect($filtered['supports'])->toHaveKey('color');
        expect($filtered['supports'])->toHaveKey('alignment');
    });

    it('ignores block type args for other blocks', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add(['color' => true]);

        $args = [
            'supports' => ['alignment' => true],
        ];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/paragraph');

        expect($filtered)->toBe($args); // Unchanged
        expect($filtered['supports'])->not->toHaveKey('color');
    });

    it('removes supports correctly', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->remove('spacing');

        $args = [
            'supports' => [
                'alignment' => true,
                'spacing'   => ['margin' => true],
                'color'     => true,
            ],
        ];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/image');

        expect($filtered['supports'])->not->toHaveKey('spacing');
        expect($filtered['supports'])->toHaveKey('alignment');
        expect($filtered['supports'])->toHaveKey('color');
    });

    it('creates supports array if not present', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add(['color' => true]);

        $args = [];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/image');

        expect($filtered)->toHaveKey('supports');
        expect($filtered['supports'])->toHaveKey('color');
    });

    it('deep merges nested support configuration', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add([
            'color' => [
                'palette'  => ['red', 'blue'],
                'gradient' => true,
            ],
        ]);

        $args = [
            'supports' => [
                'color' => [
                    'background' => true,
                    'palette'    => ['green'], // Will be overwritten
                ],
            ],
        ];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/image');

        expect($filtered['supports']['color'])->toHaveKey('palette');
        expect($filtered['supports']['color'])->toHaveKey('gradient');
        expect($filtered['supports']['color'])->toHaveKey('background');
        expect($filtered['supports']['color']['palette'])->toBe(['red', 'blue']);
    });

    it('supports method chaining', function () {
        $blockSupports = new BlockSupports('core/image');

        $result = $blockSupports
            ->add(['color' => true])
            ->add(['typography' => true])
            ->remove(['spacing', 'padding'])
            ->add(['alignment' => true]);

        expect($result)->toBe($blockSupports);
        expect($blockSupports->getSupportsToAdd())->toHaveKey('color');
        expect($blockSupports->getSupportsToAdd())->toHaveKey('typography');
        expect($blockSupports->getSupportsToAdd())->toHaveKey('alignment');
        expect($blockSupports->getSupportsToRemove())->toHaveCount(2);
    });

    it('returns block name correctly', function () {
        $blockSupports = new BlockSupports('core/image');
        expect($blockSupports->getBlockName())->toBe('core/image');
    });

    it('can add and then remove conflicting supports', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add(['color' => true, 'spacing' => true]);
        $blockSupports->remove('spacing');

        $args = [
            'supports' => ['alignment' => true],
        ];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/image');

        expect($filtered['supports'])->toHaveKey('color');
        expect($filtered['supports'])->not->toHaveKey('spacing');
    });

    it('handles multiple removals of the same support', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->remove('spacing');
        $blockSupports->remove(['spacing']); // Remove same key again

        $args = [
            'supports' => [
                'spacing' => true,
                'color'   => true,
            ],
        ];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/image');

        expect($filtered['supports'])->not->toHaveKey('spacing');
        expect($filtered['supports'])->toHaveKey('color');
    });

    it('preserves existing supports when merging', function () {
        $blockSupports = new BlockSupports('core/image');
        $blockSupports->add(['color' => true]);

        $args = [
            'supports' => [
                'alignment'  => ['type' => 'sticky'],
                'spacing'    => ['margin' => true],
                'typography' => true,
            ],
        ];

        $filtered = $blockSupports->filterBlockTypeArgs($args, 'core/image');

        expect($filtered['supports'])->toHaveKey('alignment');
        expect($filtered['supports']['alignment'])->toBe(['type' => 'sticky']);
        expect($filtered['supports'])->toHaveKey('spacing');
        expect($filtered['supports']['spacing'])->toBe(['margin' => true]);
        expect($filtered['supports'])->toHaveKey('typography');
        expect($filtered['supports'])->toHaveKey('color');
    });

    it('is instance of ServiceContract', function () {
        $blockSupports = new BlockSupports('core/image');
        expect($blockSupports)->toBeInstanceOf(\RalfHortt\ServiceContracts\ServiceContract::class);
    });
});
