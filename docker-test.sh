#!/bin/bash

# Docker Test Script for Modular Laravel
# This script runs tests inside Docker containers

echo "🧪 Running tests in Docker environment..."

# Check if Docker containers are running
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Docker containers are not running. Please run ./docker-setup.sh first."
    exit 1
fi

# Run tests
echo "🔬 Running PHPUnit tests..."
docker-compose exec app php artisan test

# Run PHPStan
echo "🔍 Running PHPStan static analysis..."
docker-compose exec app vendor/bin/phpstan analyse

# Run Pint
echo "🎨 Running Laravel Pint code formatting..."
docker-compose exec app vendor/bin/pint --test

echo ""
echo "✅ All tests and checks completed!"
