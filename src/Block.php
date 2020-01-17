<?php
namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;

abstract class Block implements ServiceContract
{
    // Block name
    protected $name;

    // Block attributes
    protected $attributes = [];

    /**
     * Register
     */
    public function register(): void
    {
        register_block_type(
            $this->getName(),
            [
                'render_callback' => $this->callback(),
                'attributes'      => $this->getAttributes(),
            ]
        );
    }

    /**
     * Get block name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get block attributes
     */
    public function getAttributes(): array
    {
        return apply_filters($this->getName() . '/attributes', $this->attributes);
    }

    /**
     * Render callback
     */
    public function callback(array $atts = [], string $content = ''): string
    {
        ob_start();

        do_action($this->getName() . '/before', $attributes, $content);
        apply_filters($this->getName() . '/render', $this->render($attributes, $content));
        do_action($this->getName() . '/after', $attributes, $content);

        return ob_get_clean();
    }

    /**
     * Output
     */
    abstract protected function render(array $atts, string $content): void;
}
