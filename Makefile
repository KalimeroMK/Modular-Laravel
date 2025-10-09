# Makefile for Modular Laravel

.PHONY: help docker-setup docker-test docker-stop docker-restart docker-logs test phpstan pint migrate seed

# Default target
help: ## Show this help message
	@echo "Modular Laravel - Available Commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Docker commands
docker-setup: ## Setup Docker environment and run migrations/seeders
	@echo "🐳 Setting up Docker environment..."
	@./docker-setup.sh

docker-test: ## Run all tests in Docker environment
	@echo "🧪 Running tests in Docker..."
	@./docker-test.sh

docker-stop: ## Stop Docker containers
	@echo "🛑 Stopping Docker containers..."
	@docker-compose down

docker-restart: ## Restart Docker containers
	@echo "🔄 Restarting Docker containers..."
	@docker-compose restart

docker-logs: ## Show Docker logs
	@echo "📋 Showing Docker logs..."
	@docker-compose logs -f

# Development commands
test: ## Run PHPUnit tests
	@echo "🧪 Running tests..."
	@php artisan test

phpstan: ## Run PHPStan static analysis
	@echo "🔍 Running PHPStan..."
	@vendor/bin/phpstan analyse

pint: ## Run Laravel Pint code formatting
	@echo "🎨 Running Pint..."
	@vendor/bin/pint

migrate: ## Run database migrations
	@echo "🗄️ Running migrations..."
	@php artisan migrate:fresh --seed

seed: ## Run database seeders
	@echo "🌱 Running seeders..."
	@php artisan db:seed

# Quick setup for local development
setup: ## Quick setup for local development (without Docker)
	@echo "⚡ Quick local setup..."
	@composer install
	@cp .env.example .env
	@php artisan key:generate
	@php artisan migrate:fresh --seed
	@php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
	@php artisan migrate
	@php artisan l5-swagger:generate
	@echo "✅ Local setup completed!"

# Clean commands
clean: ## Clean cache and temporary files
	@echo "🧹 Cleaning cache..."
	@php artisan optimize:clear
	@php artisan config:clear
	@php artisan route:clear
	@php artisan view:clear

# Production commands
build: ## Build for production
	@echo "🏗️ Building for production..."
	@composer install --no-dev --optimize-autoloader
	@php artisan config:cache
	@php artisan route:cache
	@php artisan view:cache
	@npm run build
