<?php

use RalfHortt\WPBlock\Tests\TestBlock;
use Brain\Monkey\Functions;

describe('Block', function () {
    beforeEach(function () {
        $this->block = new TestBlock([
            'title' => 'My Test Block',
            'name' => 'mytest/block',
            'attributes' => ['color' => 'blue']
        ]);
    });

    it('can get block name', function () {
        expect($this->block->getName())->toBe('mytest/block');
    });

    it('can get block title', function () {
        expect($this->block->getTitle())->toBe('My Test Block');
    });

    it('can get block attributes', function () {
        expect($this->block->getAttributes())->toBe(['color' => 'blue']);
    });

    it('returns false when no block json is set', function () {
        expect($this->block->hasBlockJson())->toBeFalse();
    });

    it('returns true when block json is set', function () {
        $blockWithJson = new TestBlock([
            'blockJson' => 'block.json'
        ]);

        expect($blockWithJson->hasBlockJson())->toBeTrue();
    });

    it('can register block type without block.json', function () {
        $this->block->register();
        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });

    it('can register block type with block.json', function () {
        $blockWithJson = new TestBlock([
            'blockJson' => 'block.json'
        ]);

        $blockWithJson->register();
        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });

    it('can render callback output', function () {
        $output = $this->block->callback();

        expect($output)->toContain('Test block output');
    });
});
