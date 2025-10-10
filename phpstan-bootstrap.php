<?php
/**
 * PHPStan bootstrap file for WordPress functions
 */

// Define WordPress functions that PHPStan needs to know about
if (!function_exists('add_action')) {
    function add_action(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1): bool {
        return true;
    }
}

if (!function_exists('register_block_type')) {
    function register_block_type(string $block_type, array $args = []): \WP_Block_Type {
        return new \WP_Block_Type($block_type, $args);
    }
}

if (!function_exists('wp_register_block_types_from_metadata_collection')) {
    function wp_register_block_types_from_metadata_collection(string $path, string $manifest = ''): array {
        return [];
    }
}

if (!function_exists('file_exists')) {
    function file_exists(string $filename): bool {
        return true;
    }
}

if (!function_exists('dirname')) {
    function dirname(string $path, int $levels = 1): string {
        return '';
    }
}

// Define WordPress classes that PHPStan needs to know about
if (!class_exists('WP_Block_Type')) {
    class WP_Block_Type {
        public function __construct(string $block_type, array $args = []) {}
    }
}

if (!class_exists('RuntimeException')) {
    class RuntimeException extends Exception {}
}
