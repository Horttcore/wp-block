#!/bin/bash

# GitHub Actions CI Status Check
# Run this script to verify your changes are ready for CI

echo "🔍 Running pre-CI checks..."
echo ""

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo "❌ Not in a git repository"
    exit 1
fi

# Check if composer.json exists
if [ ! -f "composer.json" ]; then
    echo "❌ composer.json not found"
    exit 1
fi

echo "📦 Installing dependencies..."
composer install --quiet

echo ""
echo "✅ Running Composer validation..."
composer validate --strict

echo ""
echo "🔍 Running PHPStan analysis..."
composer phpstan

echo ""
echo "🧪 Running tests..."
composer test

echo ""
echo "✅ All checks passed! Ready for CI 🚀"
echo ""
echo "Next steps:"
echo "  1. Commit your changes: git add . && git commit -m 'Your message'"
echo "  2. Push to GitHub: git push"
echo "  3. GitHub Actions will automatically run the full test suite"
