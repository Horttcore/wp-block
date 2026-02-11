<?php

use RalfHortt\WPBlock\BlockDefaults;

describe('BlockDefaults', function () {
    describe('::for() static factory', function () {
        it('creates instance for single block', function () {
            $defaults = BlockDefaults::for('test/block');

            expect($defaults)->toBeInstanceOf(BlockDefaults::class);
        });

        it('creates instance for multiple blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two']);

            expect($defaults)->toBeInstanceOf(BlockDefaults::class);
        });

        it('validates block name format', function () {
            expect(fn () => BlockDefaults::for('invalid-block-name'))
                ->toThrow(\InvalidArgumentException::class, 'Invalid block name');
        });

        it('validates block name has namespace and name', function () {
            expect(fn () => BlockDefaults::for('invalid/'))
                ->toThrow(\InvalidArgumentException::class);
            
            expect(fn () => BlockDefaults::for('/invalid'))
                ->toThrow(\InvalidArgumentException::class);
        });

        it('validates all block names in array', function () {
            expect(fn () => BlockDefaults::for(['core/valid', 'invalid']))
                ->toThrow(\InvalidArgumentException::class);
        });
    });

    describe('single-block mode', function () {
        it('sets single attribute with set()', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', 'red');

            expect($defaults->getDefaults()['test/block'])->toBe(['color' => 'red']);
        });

        it('sets multiple attributes with array', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set(['color' => 'red', 'size' => 'large']);

            expect($defaults->getDefaults()['test/block'])->toBe([
                'color' => 'red',
                'size'  => 'large',
            ]);
        });

        it('merges multiple set() calls', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', 'red')
                ->set('size', 'large')
                ->set(['padding' => '10px']);

            expect($defaults->getDefaults()['test/block'])->toBe([
                'color'   => 'red',
                'size'    => 'large',
                'padding' => '10px',
            ]);
        });

        it('sets value with callback', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', fn($metadata) => 'dynamic-red');

            $callback = $defaults->getDefaults()['test/block']['color'];
            expect($callback)->toBeCallable();
            expect($callback([]))->toBe('dynamic-red');
        });

        it('removes attribute with removeAttribute()', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set(['color' => 'red', 'size' => 'large'])
                ->removeAttribute('color');

            expect($defaults->getDefaults()['test/block'])->toBe(['size' => 'large']);
        });

        it('removes all defaults with remove()', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', 'red')
                ->remove();

            expect($defaults->getDefaults())->not->toHaveKey('test/block');
        });

        it('supports fluent interface with register()', function () {
            $result = BlockDefaults::for('test/block')
                ->set('color', 'red')
                ->register();

            expect($result)->toBeInstanceOf(BlockDefaults::class);
        });

        it('can set null as value', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('test/block', 'color', null);

            expect($defaults->getDefaults()['test/block'])->toBe(['color' => null]);
        });

        it('tracks focused blocks', function () {
            $defaults = BlockDefaults::for('test/block');

            expect($defaults->getFocusedBlocks())->toBe(['test/block']);
        });
    });

    describe('multi-block mode', function () {
        it('sets single attribute for all blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('color', 'red');

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['test/block-one'])->toBe(['color' => 'red']);
            expect($allDefaults['test/block-two'])->toBe(['color' => 'red']);
        });

        it('sets multiple attributes for all blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set(['color' => 'red', 'size' => 'large']);

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['test/block-one'])->toBe([
                'color' => 'red',
                'size'  => 'large',
            ]);
            expect($allDefaults['test/block-two'])->toBe([
                'color' => 'red',
                'size'  => 'large',
            ]);
        });

        it('can chain multiple set() calls', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('color', 'red')
                ->set('size', 'large');

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['test/block-one'])->toBe([
                'color' => 'red',
                'size'  => 'large',
            ]);
            expect($allDefaults['test/block-two'])->toBe([
                'color' => 'red',
                'size'  => 'large',
            ]);
        });

        it('sets callback for all blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('color', fn($metadata) => $metadata['name'] === 'test/block-one' ? 'red' : 'blue');

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['test/block-one']['color'])->toBeCallable();
            expect($allDefaults['test/block-two']['color'])->toBeCallable();
        });

        it('removes attribute from all blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set(['color' => 'red', 'size' => 'large'])
                ->removeAttribute('color');

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['test/block-one'])->toBe(['size' => 'large']);
            expect($allDefaults['test/block-two'])->toBe(['size' => 'large']);
        });

        it('removes all focused blocks with remove()', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('color', 'red')
                ->remove();

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults)->not->toHaveKey('test/block-one');
            expect($allDefaults)->not->toHaveKey('test/block-two');
        });

        it('can set different values for specific blocks using explicit mode', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('color', 'red') // Both blocks get 'red'
                ->set('test/block-one', 'size', 'large'); // Only block-one gets size

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['test/block-one'])->toBe([
                'color' => 'red',
                'size'  => 'large',
            ]);
            expect($allDefaults['test/block-two'])->toBe([
                'color' => 'red',
            ]);
        });

        it('tracks all focused blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two']);

            expect($defaults->getFocusedBlocks())->toBe(['test/block-one', 'test/block-two']);
        });
    });

    describe('explicit block mode', function () {
        it('can set attributes for specific block without using ::for()', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('test/block-three', 'color', 'blue');

            expect($defaults->getDefaults()['test/block-three'])->toBe(['color' => 'blue']);
        });

        it('can set callback for specific block', function () {
            $defaults = BlockDefaults::for(['test/block-one'])
                ->set('test/block-two', 'color', fn($metadata) => 'callback-blue');

            $callback = $defaults->getDefaults()['test/block-two']['color'];
            expect($callback)->toBeCallable();
            expect($callback([]))->toBe('callback-blue');
        });

        it('can remove specific block', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('color', 'red')
                ->remove('test/block-one');

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults)->not->toHaveKey('test/block-one');
            expect($allDefaults)->toHaveKey('test/block-two');
        });

        it('can remove attribute from specific block', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set(['color' => 'red', 'size' => 'large'])
                ->removeAttribute('test/block-one', 'size');

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['test/block-one'])->toBe(['color' => 'red']);
            expect($allDefaults['test/block-two'])->toBe([
                'color' => 'red',
                'size'  => 'large',
            ]);
        });
    });

    describe('overrideAttributes', function () {
        it('overrides block metadata attributes', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', 'red');

            $metadata = [
                'name'       => 'test/block',
                'attributes' => [
                    'color' => [
                        'type'    => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $defaults->overrideAttributes($metadata);

            expect($result['attributes']['color']['default'])->toBe('red');
        });

        it('executes callbacks with metadata', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', fn($metadata) => $metadata['name'] . '-color');

            $metadata = [
                'name'       => 'test/block',
                'attributes' => [
                    'color' => [
                        'type'    => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $defaults->overrideAttributes($metadata);

            expect($result['attributes']['color']['default'])->toBe('test/block-color');
        });

        it('ignores metadata for non-configured blocks', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', 'red');

            $metadata = [
                'name'       => 'test/other',
                'attributes' => [
                    'color' => [
                        'type'    => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $defaults->overrideAttributes($metadata);

            expect($result['attributes']['color']['default'])->toBe('blue');
        });

        it('handles metadata without attributes', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', 'red');

            $metadata = [
                'name' => 'test/block',
            ];

            $result = $defaults->overrideAttributes($metadata);

            expect($result['name'])->toBe('test/block');
        });

        it('only overrides attributes that exist in metadata', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set(['color' => 'red', 'nonexistent' => 'value']);

            $metadata = [
                'name'       => 'test/block',
                'attributes' => [
                    'color' => [
                        'type'    => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $defaults->overrideAttributes($metadata);

            expect($result['attributes']['color']['default'])->toBe('red');
            expect($result['attributes'])->not->toHaveKey('nonexistent');
        });

        it('returns metadata unchanged if name is missing', function () {
            $defaults = BlockDefaults::for('test/block')
                ->set('color', 'red');

            $metadata = [
                'attributes' => [
                    'color' => [
                        'type'    => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $defaults->overrideAttributes($metadata);

            expect($result)->toBe($metadata);
        });
    });

    describe('getDefaults', function () {
        it('returns all configured blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two'])
                ->set('test/block-one', ['color' => 'red'])
                ->set('test/block-two', ['size' => 'large']);

            $allDefaults = $defaults->getDefaults();

            expect($allDefaults)->toHaveKey('test/block-one');
            expect($allDefaults)->toHaveKey('test/block-two');
            expect($allDefaults['test/block-one'])->toBe(['color' => 'red']);
            expect($allDefaults['test/block-two'])->toBe(['size' => 'large']);
        });
    });

    describe('getFocusedBlocks', function () {
        it('returns empty array when no blocks focused', function () {
            $defaults = new class extends BlockDefaults {
                public function __construct() {
                    // Skip parent constructor
                }
            };

            expect($defaults->getFocusedBlocks())->toBe([]);
        });

        it('returns focused blocks', function () {
            $defaults = BlockDefaults::for(['test/block-one', 'test/block-two']);

            expect($defaults->getFocusedBlocks())->toBe(['test/block-one', 'test/block-two']);
        });
    });

    describe('real-world usage examples', function () {
        it('configures core/image with static defaults', function () {
            $defaults = BlockDefaults::for('core/image')
                ->set('sizeSlug', 'large')
                ->set('linkDestination', 'media')
                ->register();

            expect($defaults->getDefaults()['core/image'])->toBe([
                'sizeSlug'        => 'large',
                'linkDestination' => 'media',
            ]);
        });

        it('configures core/media-text with callback for dynamic values', function () {
            $defaults = BlockDefaults::for('core/media-text')
                ->set('verticalAlignment', fn($metadata) => 'top')
                ->register();

            $callback = $defaults->getDefaults()['core/media-text']['verticalAlignment'];
            expect($callback)->toBeCallable();
        });

        it('configures multiple blocks with same attributes', function () {
            $defaults = BlockDefaults::for(['core/media-text', 'core/image'])
                ->set('imageSize', 'full')
                ->register();

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['core/media-text'])->toBe(['imageSize' => 'full']);
            expect($allDefaults['core/image'])->toBe(['imageSize' => 'full']);
        });

        it('configures multiple blocks with same and different attributes', function () {
            $defaults = BlockDefaults::for(['core/image', 'core/paragraph'])
                ->set('className', 'custom-block') // Both get this
                ->set('core/image', 'sizeSlug', 'large') // Only image
                ->set('core/paragraph', 'fontSize', 'large') // Only paragraph
                ->register();

            $allDefaults = $defaults->getDefaults();
            expect($allDefaults['core/image'])->toBe([
                'className' => 'custom-block',
                'sizeSlug'  => 'large',
            ]);
            expect($allDefaults['core/paragraph'])->toBe([
                'className' => 'custom-block',
                'fontSize'  => 'large',
            ]);
        });

        it('uses callback for conditional logic', function () {
            $defaults = BlockDefaults::for('core/image')
                ->set('sizeSlug', function($metadata) {
                    // Example: Different sizes based on context
                    return $metadata['customData'] ?? 'large';
                })
                ->register();

            $metadata = [
                'name' => 'core/image',
                'customData' => 'thumbnail',
                'attributes' => [
                    'sizeSlug' => [
                        'type' => 'string',
                        'default' => 'medium',
                    ],
                ],
            ];

            $result = $defaults->overrideAttributes($metadata);
            expect($result['attributes']['sizeSlug']['default'])->toBe('thumbnail');
        });
    });
});
