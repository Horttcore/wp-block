<?php

namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

abstract class Block implements ServiceContract
{
    // Block Title
    protected string $title;

    // Block name
    protected string $name;

    // Block attributes
    protected array $attributes = [];

    // Path to block.json
    protected string $blockJson;

    /**
     * Register.
     */
    public function register(): void
    {
        $attrs = [];
        if (!$this->hasBlockJson()) {
            $attrs['attributes'] = $this->getAttributes();
            $attrs['title'] = $this->getTitle();
        }
        $block = \register_block_type(
            $this->hasBlockJson() ? $this->getBlockJson() : $this->getName(),
            array_merge([
                'render_callback' => [$this, 'callback'],
            ], $attrs, $this->args()),
        );
    }

    /**
     * Has block json.
     */
    protected function hasBlockJson(): bool
    {
        return isset($this->blockJson) && $this->blockJson;
    }

    /**
     * Get block json.
     */
    protected function getBlockJson(): string
    {
        if (str_starts_with($this->blockJson, '/') || preg_match('#^[A-Za-z]:[\\\\/]#', $this->blockJson) === 1) {
            return $this->blockJson;
        }

        $fileName = (new \ReflectionClass(static::class))->getFileName();
        if ($fileName === false) {
            throw new \RuntimeException('Could not resolve block class file path.');
        }

        $directory = dirname($fileName);

        return $directory.DIRECTORY_SEPARATOR.$this->blockJson;
    }

    /**
     * Get block name.
     */
    protected function getName(): string
    {
        return $this->name;
    }

    /**
     * Get block title.
     */
    protected function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Register block type args.
     */
    protected function args(): array
    {
        return [];
    }

    /**
     * Get block attributes.
     */
    protected function getAttributes(): array
    {
        return \apply_filters($this->getName().'/attributes', $this->attributes);
    }

    /**
     * Render callback.
     */
    public function callback(array $atts = [], string $content = ''): string
    {
        ob_start();

        \do_action($this->getName().'/before', $atts, $content);
        $this->render($atts, $content);
        $output = ob_get_contents();
        echo \apply_filters($this->getName().'/render', $output, $atts, $content);
        \do_action($this->getName().'/after', $atts, $content);

        return ob_get_clean() ?: '';
    }

    /**
     * Output.
     */
    abstract protected function render(array $atts, string $content): void;
}
