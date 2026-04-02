<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

class BlockSupports implements ServiceContract
{
    /**
     * The block name to configure supports for.
     *
     * @var string
     */
    protected string $blockName;

    /**
     * Supports to add/merge into the block.
     *
     * @var array
     */
    protected array $supportsToAdd = [];

    /**
     * Support keys to remove from the block.
     *
     * @var array<string>
     */
    protected array $supportsToRemove = [];

    /**
     * Private constructor. Use the static for() method instead.
     *
     * @param string $blockName The block name in 'namespace/name' format
     */
    private function __construct(string $blockName)
    {
        $this->blockName = $blockName;
    }

    /**
     * Create a new BlockSupports instance for a specific block.
     *
     * @param string $blockName The block name in 'namespace/name' format
     *
     * @throws \InvalidArgumentException If block name format is invalid
     *
     * @return self
     */
    public static function for(string $blockName): self
    {
        self::validateBlockName($blockName);

        return new self($blockName);
    }

    /**
     * Validate block name format.
     *
     * @param string $blockName The block name to validate
     *
     * @throws \InvalidArgumentException If block name format is invalid
     */
    private static function validateBlockName(string $blockName): void
    {
        if (!str_contains($blockName, '/')) {
            throw new \InvalidArgumentException(
                sprintf('Invalid block name "%s". Block names must be in "namespace/name" format.', $blockName)
            );
        }

        $parts = explode('/', $blockName);
        if (count($parts) !== 2 || empty($parts[0]) || empty($parts[1])) {
            throw new \InvalidArgumentException(
                sprintf('Invalid block name "%s". Block names must be in "namespace/name" format.', $blockName)
            );
        }
    }

    /**
     * Add or merge supports into the block.
     *
     * Supports both array config (with nested values) and string shorthand for boolean features.
     *
     * Examples:
     *   ->add(['color' => true])
     *   ->add(['color' => ['palette' => [...]]])
     *   ->add(['color', 'typography'])  // shorthand for boolean features
     *
     * @param array $supports Supports to add/merge
     *
     * @return self
     */
    public function add(array $supports): self
    {
        foreach ($supports as $key => $value) {
            if (is_string($key)) {
                // Associative: 'color' => true or 'color' => [...]
                $this->supportsToAdd[$key] = $value;
            } else {
                // Indexed: just the string name, treated as boolean true
                $this->supportsToAdd[$value] = true;
            }
        }

        return $this;
    }

    /**
     * Remove supports from the block.
     *
     * Supports both string and array formats.
     *
     * Examples:
     *   ->remove('spacing')
     *   ->remove(['spacing', 'padding'])
     *
     * @param string|array $supportKeys Support key(s) to remove
     *
     * @return self
     */
    public function remove(string|array $supportKeys): self
    {
        if (is_string($supportKeys)) {
            $this->supportsToRemove[] = $supportKeys;
        } else {
            foreach ($supportKeys as $key) {
                if (is_string($key)) {
                    $this->supportsToRemove[] = $key;
                }
            }
        }

        return $this;
    }

    /**
     * Register the supports configuration by hooking into WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        \add_filter('register_block_type_args', [$this, 'filterBlockTypeArgs'], 10, 2);
    }

    /**
     * Filter block type args to apply supports configuration.
     *
     * @param array  $args      The arguments for register_block_type
     * @param string $blockName The block name being registered
     *
     * @return array Modified arguments
     */
    public function filterBlockTypeArgs(array $args, string $blockName): array
    {
        // Only apply to the target block
        if ($blockName !== $this->blockName) {
            return $args;
        }

        // Ensure supports array exists
        if (!isset($args['supports'])) {
            $args['supports'] = [];
        }

        // Add/merge new supports using deep merge
        if (!empty($this->supportsToAdd)) {
            $args['supports'] = $this->deepMerge($args['supports'], $this->supportsToAdd);
        }

        // Remove specified supports (removals take precedence over additions)
        foreach ($this->supportsToRemove as $key) {
            unset($args['supports'][$key]);
        }

        return $args;
    }

    /**
     * Deep merge two arrays, with $new taking precedence.
     *
     * Used to merge supports configuration while preserving nested array structures.
     *
     * @param array $existing The existing array
     * @param array $new      The new array to merge in
     *
     * @return array The merged array
     */
    protected function deepMerge(array $existing, array $new): array
    {
        foreach ($new as $key => $value) {
            if (isset($existing[$key]) && is_array($existing[$key]) && is_array($value)) {
                $existing[$key] = $this->deepMerge($existing[$key], $value);
            } else {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }

    /**
     * Get the configured supports to add.
     *
     * @return array
     */
    public function getSupportsToAdd(): array
    {
        return $this->supportsToAdd;
    }

    /**
     * Get the configured supports to remove.
     *
     * @return array
     */
    public function getSupportsToRemove(): array
    {
        return $this->supportsToRemove;
    }

    /**
     * Get the target block name.
     *
     * @return string
     */
    public function getBlockName(): string
    {
        return $this->blockName;
    }
}
