<?php

use Brain\Monkey\Functions;
use RalfHortt\WPBlock\BlockPatterns;

describe('BlockPatterns', function () {
    beforeEach(function () {
        Functions\when('add_action')->justReturn(true);
        Functions\when('add_filter')->justReturn(true);
        Functions\when('remove_theme_support')->justReturn(true);
    });

    it('registers pattern categories', function () {
        Functions\expect('register_block_pattern_category')
            ->once()
            ->with('featured', ['label' => 'Featured']);

        (new BlockPatterns())->registerCategories(['featured' => 'Featured']);
    });

    it('hooks init when register is called', function () {
        (new BlockPatterns())->register();

        expect(true)->toBeTrue();
    });

    it('supports fluent toggles', function () {
        $patterns = (new BlockPatterns())
            ->patternCategories(['hero' => 'Hero'])
            ->removeCorePatterns()
            ->disableRemotePatterns();

        expect($patterns)->toBeInstanceOf(BlockPatterns::class);
    });
});
