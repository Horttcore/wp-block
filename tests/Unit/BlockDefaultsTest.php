<?php

use RalfHortt\WPBlock\BlockDefaults;

describe('BlockDefaults', function () {
    beforeEach(function () {
        $this->blockDefaults = new BlockDefaults('test/block');
    });

    describe('constructor', function () {
        it('accepts a single block name with overrides', function () {
            $defaults = new BlockDefaults('test/block', ['color' => 'red']);

            expect($defaults->getBlockDefaults('test/block'))->toBe(['color' => 'red']);
        });

        it('accepts multiple blocks as array', function () {
            $blocks = [
                'test/block-one' => ['color' => 'red'],
                'test/block-two' => ['size' => 'large'],
            ];
            $defaults = new BlockDefaults($blocks);

            expect($defaults->getBlockDefaults('test/block-one'))->toBe(['color' => 'red']);
            expect($defaults->getBlockDefaults('test/block-two'))->toBe(['size' => 'large']);
        });

        it('handles empty overrides', function () {
            $defaults = new BlockDefaults('test/block');

            expect($defaults->getBlockDefaults('test/block'))->toBe([]);
        });
    });

    describe('addBlock', function () {
        it('adds a new block with defaults', function () {
            $this->blockDefaults->addBlock('test/block-two', ['padding' => '10px']);

            expect($this->blockDefaults->getBlockDefaults('test/block-two'))->toBe(['padding' => '10px']);
        });

        it('returns self for method chaining', function () {
            $result = $this->blockDefaults->addBlock('test/block-two');

            expect($result)->toBe($this->blockDefaults);
        });

        it('overwrites existing block defaults', function () {
            $this->blockDefaults->addBlock('test/block', ['color' => 'blue']);

            expect($this->blockDefaults->getBlockDefaults('test/block'))->toBe(['color' => 'blue']);
        });
    });

    describe('setAttribute', function () {
        it('sets an attribute for a block', function () {
            $this->blockDefaults->setAttribute('test/block', 'color', 'red');

            expect($this->blockDefaults->getBlockDefaults('test/block'))->toHaveKey('color');
            expect($this->blockDefaults->getBlockDefaults('test/block')['color'])->toBe('red');
        });

        it('creates block entry if it does not exist', function () {
            $this->blockDefaults->setAttribute('test/new-block', 'color', 'green');

            expect($this->blockDefaults->getBlockDefaults('test/new-block'))->toHaveKey('color');
        });

        it('returns self for method chaining', function () {
            $result = $this->blockDefaults->setAttribute('test/block', 'color', 'red');

            expect($result)->toBe($this->blockDefaults);
        });

        it('does not overwrite existing attributes', function () {
            $this->blockDefaults->setAttribute('test/block', 'color', 'red');
            $this->blockDefaults->setAttribute('test/block', 'size', 'large');

            $defaults = $this->blockDefaults->getBlockDefaults('test/block');
            expect($defaults)->toHaveKey('color');
            expect($defaults)->toHaveKey('size');
            expect($defaults['color'])->toBe('red');
            expect($defaults['size'])->toBe('large');
        });
    });

    describe('setAttributes', function () {
        it('sets multiple attributes for a block', function () {
            $this->blockDefaults->setAttributes('test/block', [
                'color' => 'red',
                'size' => 'large',
            ]);

            $defaults = $this->blockDefaults->getBlockDefaults('test/block');
            expect($defaults['color'])->toBe('red');
            expect($defaults['size'])->toBe('large');
        });

        it('creates block entry if it does not exist', function () {
            $this->blockDefaults->setAttributes('test/new-block', ['padding' => '10px']);

            expect($this->blockDefaults->getBlockDefaults('test/new-block'))->toHaveKey('padding');
        });

        it('returns self for method chaining', function () {
            $result = $this->blockDefaults->setAttributes('test/block', ['color' => 'red']);

            expect($result)->toBe($this->blockDefaults);
        });

        it('merges with existing attributes', function () {
            $this->blockDefaults->setAttributes('test/block', ['color' => 'red']);
            $this->blockDefaults->setAttributes('test/block', ['size' => 'large']);

            $defaults = $this->blockDefaults->getBlockDefaults('test/block');
            expect($defaults['color'])->toBe('red');
            expect($defaults['size'])->toBe('large');
        });
    });

    describe('getDefaults', function () {
        it('returns all block defaults', function () {
            $this->blockDefaults->addBlock('test/block-one', ['color' => 'red']);
            $this->blockDefaults->addBlock('test/block-two', ['size' => 'large']);

            $allDefaults = $this->blockDefaults->getDefaults();

            expect($allDefaults)->toHaveKey('test/block-one');
            expect($allDefaults)->toHaveKey('test/block-two');
            expect($allDefaults['test/block-one'])->toBe(['color' => 'red']);
            expect($allDefaults['test/block-two'])->toBe(['size' => 'large']);
        });
    });

    describe('getBlockDefaults', function () {
        it('returns defaults for a specific block', function () {
            $this->blockDefaults->setAttribute('test/block', 'color', 'red');

            expect($this->blockDefaults->getBlockDefaults('test/block'))->toHaveKey('color');
        });

        it('returns empty array for non-existent block', function () {
            expect($this->blockDefaults->getBlockDefaults('test/nonexistent'))->toBe([]);
        });
    });

    describe('overrideAttributes', function () {
        it('overrides block metadata attributes', function () {
            $this->blockDefaults->setAttribute('test/block', 'color', 'red');

            $metadata = [
                'name' => 'test/block',
                'attributes' => [
                    'color' => [
                        'type' => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $this->blockDefaults->overrideAttributes($metadata);

            expect($result['attributes']['color']['default'])->toBe('red');
        });

        it('ignores metadata for non-configured blocks', function () {
            $metadata = [
                'name' => 'test/other',
                'attributes' => [
                    'color' => [
                        'type' => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $this->blockDefaults->overrideAttributes($metadata);

            expect($result['attributes']['color']['default'])->toBe('blue');
        });

        it('handles metadata without attributes', function () {
            $this->blockDefaults->setAttribute('test/block', 'color', 'red');

            $metadata = [
                'name' => 'test/block',
            ];

            $result = $this->blockDefaults->overrideAttributes($metadata);

            expect($result['name'])->toBe('test/block');
        });

        it('only overrides attributes that exist in metadata', function () {
            $this->blockDefaults->setAttributes('test/block', [
                'color' => 'red',
                'nonexistent' => 'value',
            ]);

            $metadata = [
                'name' => 'test/block',
                'attributes' => [
                    'color' => [
                        'type' => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $this->blockDefaults->overrideAttributes($metadata);

            expect($result['attributes']['color']['default'])->toBe('red');
            expect($result['attributes'])->not->toHaveKey('nonexistent');
        });

        it('returns metadata unchanged if name is missing', function () {
            $metadata = [
                'attributes' => [
                    'color' => [
                        'type' => 'string',
                        'default' => 'blue',
                    ],
                ],
            ];

            $result = $this->blockDefaults->overrideAttributes($metadata);

            expect($result)->toBe($metadata);
        });
    });

    describe('method chaining', function () {
        it('chains multiple operations', function () {
            $result = $this->blockDefaults
                ->addBlock('test/block-one', ['color' => 'red'])
                ->setAttribute('test/block-two', 'size', 'large')
                ->setAttributes('test/block-three', ['padding' => '10px']);

            expect($result)->toBe($this->blockDefaults);
            expect($this->blockDefaults->getBlockDefaults('test/block-one'))->toBe(['color' => 'red']);
            expect($this->blockDefaults->getBlockDefaults('test/block-two'))->toHaveKey('size');
            expect($this->blockDefaults->getBlockDefaults('test/block-three'))->toHaveKey('padding');
        });
    });
});
