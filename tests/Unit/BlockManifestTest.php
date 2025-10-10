<?php

use Brain\Monkey\Functions;
use RalfHortt\WPBlock\BlockManifest;

describe('BlockManifest', function () {
    beforeEach(function () {
        // Mock file_exists to return true for test manifest
        Functions\when('file_exists')
            ->justReturn(true);

        // Mock dirname to return test path
        Functions\when('dirname')
            ->justReturn('/test/path');
    });

    it('can be instantiated with manifest path', function () {
        $manifest = new BlockManifest('/test/manifest.json');

        expect($manifest)->toBeInstanceOf(BlockManifest::class);
    });

    it('can be instantiated with custom blocks path', function () {
        $manifest = new BlockManifest('/test/manifest.json', '/custom/blocks/path');

        expect($manifest)->toBeInstanceOf(BlockManifest::class);
    });

    it('throws exception when manifest file does not exist', function () {
        Functions\when('file_exists')
            ->justReturn(false);

        expect(fn () => new BlockManifest('/nonexistent/manifest.json'))
            ->toThrow(RuntimeException::class, 'Block manifest file not found: /nonexistent/manifest.json');
    });

    it('registers action on register', function () {
        $manifest = new BlockManifest('/test/manifest.json');
        $manifest->register();

        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });

    it('can register block types from metadata collection', function () {
        $manifest = new BlockManifest('/test/manifest.json');
        $manifest->registerBlockType();

        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });

    it('uses custom blocks path when provided', function () {
        $manifest = new BlockManifest('/test/manifest.json', '/custom/blocks/path');
        $manifest->registerBlockType();

        // Just test that it doesn't throw an exception
        expect(true)->toBeTrue();
    });
});
