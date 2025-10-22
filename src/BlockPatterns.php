<?php
namespace RalfHortt\WPBlock;

use RalfHortt\ServiceContracts\ServiceContract;


/**
 * Pattern Categories Manager
 */
class BlockPatterns implements ServiceContract
{
    protected array $patternCategories = [];

    public function patternCategories(array $patternCategories = []): self
    {
        $this->patternCategories = $patternCategories;

        return $this;
    }

    /**
     * Register hooks
     */
    public function register(): void
    {
        add_action('after_setup_theme', function() {
            add_action('init', [$this, 'registerCategories']);
        });
    }

    /**
     * Remove all core block patterns
     */
    public function removeCorePatterns(): self
    {
        remove_theme_support('core-block-patterns');

        return $this;
    }

    /**
     * Disable remote block patterns from WordPress.org
     */
    public function disableRemotePatterns(): self
    {
        add_filter('should_load_remote_block_patterns', '__return_false');

        return $this;
    }

    /**
     * Register categories callback
     */
    protected function registerCategories(array $categories = []): void
    {
        foreach ($categories as $slug => $label) {
            register_block_pattern_category($slug, ['label' => $label]);
        }
    }
}
