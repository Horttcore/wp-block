<?php

namespace RalfHortt\WPBlock;

/**
 * Validates WordPress block names (namespace/name).
 */
final class BlockName
{
    /**
     * @throws \InvalidArgumentException If block name format is invalid
     */
    public static function validate(string $blockName): void
    {
        if (!str_contains($blockName, '/')) {
            throw new \InvalidArgumentException(
                sprintf('Invalid block name "%s". Block names must be in "namespace/name" format.', $blockName)
            );
        }

        $parts = explode('/', $blockName);
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            throw new \InvalidArgumentException(
                sprintf('Invalid block name "%s". Block names must be in "namespace/name" format.', $blockName)
            );
        }
    }
}
