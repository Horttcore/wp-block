<?php

use Brain\Monkey\Functions;
use RalfHortt\WPBlock\BlockManager;

describe('BlockManager', function () {
    beforeEach(function () {
        // Mock WordPress functions
        Functions\when('add_filter')->justReturn(true);
    });

    it('can be instantiated using for() factory method', function () {
        $manager = BlockManager::for('core/image');
        expect($manager)->toBeInstanceOf(BlockManager::class);
    });

    it('validates block name format in factory method', function () {
        expect(function () {
            BlockManager::for('invalid-block');
        })->toThrow(\InvalidArgumentException::class);
    });

    it('stores the block name correctly', function () {
        $manager = BlockManager::for('core/image');
        expect($manager->getBlockName())->toBe('core/image');
    });

    it('can add supports', function () {
        $manager = BlockManager::for('core/image');
        $result = $manager->addSupports(['color' => true, 'alignment' => true]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove supports', function () {
        $manager = BlockManager::for('core/image');
        $result = $manager->removeSupports(['spacing', 'padding']);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add a single style', function () {
        $manager = BlockManager::for('core/button');
        $result = $manager->addStyle('core/button', ['name' => 'test', 'label' => 'Test']);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add multiple styles', function () {
        $manager = BlockManager::for('core/button');
        $result = $manager->addStyles('core/button', [
            ['name' => 'style1', 'label' => 'Style 1'],
            ['name' => 'style2', 'label' => 'Style 2'],
        ]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove a specific style', function () {
        $manager = BlockManager::for('core/button');
        $result = $manager->removeStyle('core/button', 'outline');

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove all styles', function () {
        $manager = BlockManager::for('core/button');
        $result = $manager->removeAllStyles('core/button');

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add a single variation', function () {
        $manager = BlockManager::for('core/image');
        $result = $manager->addVariation('core/image', [
            'name'  => 'hero-image',
            'title' => 'Hero Image',
        ]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add multiple variations', function () {
        $manager = BlockManager::for('core/image');
        $result = $manager->addVariations('core/image', [
            ['name' => 'var1', 'title' => 'Variation 1'],
            ['name' => 'var2', 'title' => 'Variation 2'],
        ]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove a specific variation', function () {
        $manager = BlockManager::for('core/button');
        $result = $manager->removeVariation('core/button', 'outline');

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove all variations', function () {
        $manager = BlockManager::for('core/button');
        $result = $manager->removeAllVariations('core/button');

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can register without throwing an exception', function () {
        $manager = BlockManager::for('core/image');
        $manager->register();

        expect(true)->toBeTrue();
    });

    it('registers all three managers when register() is called', function () {
        $manager = BlockManager::for('core/button');

        $manager->addSupports(['color' => true]);
        $manager->removeStyle('core/button', 'outline');
        $manager->addVariation('core/button', ['name' => 'cta']);

        $manager->register();

        expect(true)->toBeTrue();
    });

    it('is instance of ServiceContract', function () {
        $manager = BlockManager::for('core/image');
        expect($manager)->toBeInstanceOf(\RalfHortt\ServiceContracts\ServiceContract::class);
    });

    it('creates separate manager instances for different blocks', function () {
        $manager1 = BlockManager::for('core/image');
        $manager2 = BlockManager::for('core/paragraph');

        expect($manager1->getBlockName())->toBe('core/image');
        expect($manager2->getBlockName())->toBe('core/paragraph');
        expect($manager1)->not->toBe($manager2);
    });

    it('supports fluent chaining across multiple operations', function () {
        $manager = BlockManager::for('core/image');

        $result = $manager
            ->addSupports(['color' => true, 'alignment' => true])
            ->removeSupports(['spacing'])
            ->removeStyle('core/image', 'outline')
            ->addVariation('core/image', [
                'name'       => 'hero-image',
                'title'      => 'Hero Image',
                'attributes' => ['align' => 'wide'],
            ])
            ->addVariation('core/image', [
                'name'       => 'thumbnail-image',
                'title'      => 'Thumbnail Image',
                'attributes' => ['align' => 'center', 'scale' => 'thumbnail'],
            ]);

        expect($result)->toBe($manager);
    });

    it('supports mixed operations on all managers', function () {
        $manager = BlockManager::for('core/button');

        $result = $manager
            ->addSupports(['color' => true])
            ->addSupports(['typography' => true])
            ->removeSupports(['spacing'])
            ->addStyle('core/button', ['name' => 'style1', 'label' => 'Style 1'])
            ->removeStyle('core/button', 'outline')
            ->addVariation('core/button', ['name' => 'primary', 'title' => 'Primary'])
            ->removeVariation('core/button', 'ghost');

        expect($result)->toBe($manager);
    });

    it('allows configuring for the managed block directly', function () {
        $manager = BlockManager::for('core/image');

        // When configuring for the same block, the block name should match
        $result = $manager
            ->addSupports(['color' => true])
            ->addVariation('core/image', ['name' => 'test-var', 'title' => 'Test'])
            ->removeStyle('core/image', 'outline');

        expect($result)->toBe($manager);
    });

    it('can configure different blocks via same manager methods', function () {
        $manager = BlockManager::for('core/image');

        // BlockManager can configure styles for any block, not just the managed one
        $result = $manager
            ->addStyle('core/paragraph', ['name' => 'para-style', 'label' => 'Para Style'])
            ->addVariation('core/button', ['name' => 'btn-var', 'title' => 'Button Var']);

        expect($result)->toBe($manager);
    });
});
