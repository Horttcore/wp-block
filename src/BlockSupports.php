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
     * @var array<string, bool|array>
     */
    protected array $supportsToAdd = [];

    /**
     * Support keys to remove from the block.
     *
     * @var array<string>
     */
    protected array $supportsToRemove = [];

    /**
     * @throws \InvalidArgumentException If block name format is invalid
     */
    public static function for(string $blockName): self
    {
        return new self($blockName);
    }

    /**
     * @throws \InvalidArgumentException If block name format is invalid
     */
    public function __construct(string $blockName)
    {
        BlockName::validate($blockName);
        $this->blockName = $blockName;
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
     * @param array<string|int, string|bool|array> $supports Supports to add/merge
     *
     * @return self
     */
    public function add(array $supports): self
    {
        foreach ($supports as $key => $value) {
            if (is_string($key) && (is_bool($value) || is_array($value))) {
                $this->supportsToAdd[$key] = $value;
            } elseif (is_int($key) && is_string($value)) {
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
     * @param string|array<string> $supportKeys Support key(s) to remove
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
     * @param array<string, mixed> $args      The arguments for register_block_type
     * @param string               $blockName The block name being registered
     *
     * @return array<string, mixed> Modified arguments
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
     * @param array<string, bool|array> $existing The existing array
     * @param array<string, bool|array> $new      The new array to merge in
     *
     * @return array<string, bool|array> The merged array
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
     * @return array<string, bool|array>
     */
    public function getSupportsToAdd(): array
    {
        return $this->supportsToAdd;
    }

    /**
     * Get the configured supports to remove.
     *
     * @return array<string>
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
