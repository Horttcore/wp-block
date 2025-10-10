<?php

namespace RalfHortt\WPBlock\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();

        // Set up WordPress environment
        $this->setUpWordPress();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Set up WordPress environment for testing.
     */
    protected function setUpWordPress(): void
    {
        // Mock common WordPress functions
        Functions\when('add_action')->justReturn(true);
        Functions\when('register_block_type')->justReturn(new \stdClass());
        Functions\when('wp_register_block_types_from_metadata_collection')->justReturn([]);
        Functions\when('apply_filters')->returnArg(2); // Return second argument (the value)
        Functions\when('do_action')->justReturn(null);
        Functions\when('plugin_dir_path')->justReturn('/path/to/plugin/');

        // Mock WordPress constants
        if (!defined('WP_PLUGIN_DIR')) {
            define('WP_PLUGIN_DIR', '/wp-content/plugins');
        }
        if (!defined('DIRECTORY_SEPARATOR')) {
            define('DIRECTORY_SEPARATOR', '/');
        }
    }

    /**
     * Create a test block instance.
     */
    protected function createTestBlock(array $properties = []): TestBlock
    {
        return new TestBlock($properties);
    }
}
