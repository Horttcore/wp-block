# Testing Guide

This project uses **PestPHP** for testing, which provides an elegant and expressive way to write tests.

## Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage (requires Xdebug or PCOV)
composer test:coverage

# Watch tests for changes (requires inotify on Linux)
composer test:watch

# Run specific test file
./vendor/bin/pest tests/Unit/BlockTest.php

# Run specific test
./vendor/bin/pest --filter="can get block name"
```

## Test Structure

- `tests/Unit/` - Unit tests for individual classes
- `tests/Feature/` - Integration tests that test multiple components together
- `tests/TestCase.php` - Base test case with WordPress mocking setup
- `tests/TestBlock.php` - Test implementation of the Block class

## Writing Tests

Tests use Pest's descriptive syntax:

```php
describe('Block', function () {
    beforeEach(function () {
        $this->block = new TestBlock([
            'title' => 'My Test Block',
            'name' => 'mytest/block'
        ]);
    });

    it('can get block name', function () {
        expect($this->block->getName())->toBe('mytest/block');
    });
});
```

## WordPress Mocking

The `TestCase` class automatically sets up WordPress function mocking using Brain\Monkey:

- `add_action()` - Returns true
- `register_block_type()` - Returns stdClass
- `apply_filters()` - Returns the second argument (the value)
- WordPress constants are defined for testing

## Static Analysis

PHPStan is configured to work alongside Pest:

```bash
composer phpstan
```

Both tools are set up to work together for comprehensive code quality assurance.
