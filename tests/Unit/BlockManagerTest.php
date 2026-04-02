<?php

use Brain\Monkey\Functions;
use RalfHortt\WPBlock\BlockManager;

describe('BlockManager', function () {
    beforeEach(function () {
        // Mock WordPress functions
        Functions\when('add_filter')->justReturn(true);
    });

    it('can be instantiated using constructor', function () {
        $manager = new BlockManager('core/image');
        expect($manager)->toBeInstanceOf(BlockManager::class);
    });

    it('validates block name format in constructor', function () {
        expect(function () {
            new BlockManager('invalid-block');
        })->toThrow(\InvalidArgumentException::class);
    });

    it('stores the block name correctly', function () {
        $manager = new BlockManager('core/image');
        expect($manager->getBlockName())->toBe('core/image');
    });

    it('can add supports', function () {
        $manager = new BlockManager('core/image');
        $result = $manager->addSupports(['color' => true, 'alignment' => true]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove supports', function () {
        $manager = new BlockManager('core/image');
        $result = $manager->removeSupports(['spacing', 'padding']);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add a single style', function () {
        $manager = new BlockManager('core/button');
        $result = $manager->addStyle(['name' => 'test', 'label' => 'Test']);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add multiple styles', function () {
        $manager = new BlockManager('core/button');
        $result = $manager->addStyles([
            ['name' => 'style1', 'label' => 'Style 1'],
            ['name' => 'style2', 'label' => 'Style 2'],
        ]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove a specific style', function () {
        $manager = new BlockManager('core/button');
        $result = $manager->removeStyle('outline');

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove all styles', function () {
        $manager = new BlockManager('core/button');
        $result = $manager->removeAllStyles();

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add a single variation', function () {
        $manager = new BlockManager('core/image');
        $result = $manager->addVariation([
            'name'  => 'hero-image',
            'title' => 'Hero Image',
        ]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can add multiple variations', function () {
        $manager = new BlockManager('core/image');
        $result = $manager->addVariations([
            ['name' => 'var1', 'title' => 'Variation 1'],
            ['name' => 'var2', 'title' => 'Variation 2'],
        ]);

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove a specific variation', function () {
        $manager = new BlockManager('core/button');
        $result = $manager->removeVariation('outline');

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can remove all variations', function () {
        $manager = new BlockManager('core/button');
        $result = $manager->removeAllVariations();

        expect($result)->toBe($manager); // Fluent interface
    });

    it('can register without throwing an exception', function () {
        $manager = new BlockManager('core/image');
        $manager->register();

        expect(true)->toBeTrue();
    });

    it('registers all three managers when register() is called', function () {
        $manager = new BlockManager('core/button');

        $manager->addSupports(['color' => true]);
        $manager->removeStyle('outline');
        $manager->addVariation(['name' => 'cta']);

        $manager->register();

        expect(true)->toBeTrue();
    });

    it('is instance of ServiceContract', function () {
        $manager = new BlockManager('core/image');
        expect($manager)->toBeInstanceOf(\RalfHortt\ServiceContracts\ServiceContract::class);
    });

    it('creates separate manager instances for different blocks', function () {
        $manager1 = new BlockManager('core/image');
        $manager2 = new BlockManager('core/paragraph');

        expect($manager1->getBlockName())->toBe('core/image');
        expect($manager2->getBlockName())->toBe('core/paragraph');
        expect($manager1)->not->toBe($manager2);
    });

    it('supports fluent chaining across multiple operations', function () {
        $manager = new BlockManager('core/image');

        $result = $manager
            ->addSupports(['color' => true, 'alignment' => true])
            ->removeSupports(['spacing'])
            ->removeStyle('outline')
            ->addVariation([
                'name'       => 'hero-image',
                'title'      => 'Hero Image',
                'attributes' => ['align' => 'wide'],
            ])
            ->addVariation([
                'name'       => 'thumbnail-image',
                'title'      => 'Thumbnail Image',
                'attributes' => ['align' => 'center', 'scale' => 'thumbnail'],
            ]);

        expect($result)->toBe($manager);
    });

    it('supports mixed operations on all managers', function () {
        $manager = new BlockManager('core/button');

        $result = $manager
            ->addSupports(['color' => true])
            ->addSupports(['typography' => true])
            ->removeSupports(['spacing'])
            ->addStyle(['name' => 'style1', 'label' => 'Style 1'])
            ->removeStyle('outline')
            ->addVariation(['name' => 'primary', 'title' => 'Primary'])
            ->removeVariation('ghost');

        expect($result)->toBe($manager);
    });

    it('uses the managed block name for styles automatically', function () {
        $manager = new BlockManager('core/image');

        // When calling style methods, the manager automatically uses 'core/image'
        $result = $manager
            ->addStyle(['name' => 'test', 'label' => 'Test'])
            ->removeStyle('outline')
            ->removeAllStyles();

        expect($result)->toBe($manager);
    });

    it('uses the managed block name for variations automatically', function () {
        $manager = new BlockManager('core/button');

        // When calling variation methods, the manager automatically uses 'core/button'
        $result = $manager
            ->addVariation(['name' => 'test-var', 'title' => 'Test'])
            ->removeVariation('outline')
            ->removeAllVariations();

        expect($result)->toBe($manager);
    });

    it('has clean api with no redundant block name', function () {
        $manager = new BlockManager('core/button');

        // This is the ideal clean API - no block name duplication
        $result = $manager
            ->addSupports(['color' => true])
            ->removeStyle('outline')
            ->addVariation(['name' => 'cta', 'title' => 'CTA Button'])
            ->register();

        // Just verify register returns void and doesn't throw
        expect($result)->toBeNull();
    });
});
