#!/bin/bash

# Docker Setup Script for Modular Laravel
# This script sets up the development environment with Docker

echo "🐳 Setting up Modular Laravel with Docker..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop and try again."
    exit 1
fi

# Create .env.docker file for Docker environment
echo "📝 Creating .env.docker file..."
cat > .env.docker << 'EOF'
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:benKImyGs3Vco9uGRjBZO4KjcLoEKAWG6egc2Zck7NE=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret

BROADCAST_DRIVER=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=8bc5c18edaec34
MAIL_PASSWORD=4d9d8674c2a4df

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF

echo "✅ .env.docker file created"

# Start Docker containers
echo "🚀 Starting Docker containers..."
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 10

# Copy .env.docker to .env for Docker environment
echo "📋 Copying Docker environment configuration..."
cp .env.docker .env

# Install dependencies inside container
echo "📦 Installing dependencies..."
docker-compose exec app composer install

# Generate application key if needed
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run migrations
echo "🗄️ Running migrations..."
docker-compose exec app php artisan migrate:fresh

# Publish Spatie Permission migrations
echo "🔐 Publishing Spatie Permission migrations..."
docker-compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"

# Run migrations again to include permission tables
echo "🗄️ Running migrations with permission tables..."
docker-compose exec app php artisan migrate

# Run seeders
echo "🌱 Running seeders..."
docker-compose exec app php artisan db:seed

# Generate Swagger documentation
echo "📚 Generating Swagger documentation..."
docker-compose exec app php artisan l5-swagger:generate

# Set proper permissions
echo "🔧 Setting proper permissions..."
docker-compose exec app chmod -R 775 storage bootstrap/cache

echo ""
echo "🎉 Docker setup completed successfully!"
echo ""
echo "📋 Available services:"
echo "   🌐 Web: http://localhost"
echo "   🗄️ Database: localhost:3301 (homestead/secret)"
echo "   📚 API Docs: http://localhost/api/documentation"
echo ""
echo "🔧 Useful commands:"
echo "   docker-compose exec app php artisan migrate:fresh --seed"
echo "   docker-compose exec app php artisan test"
echo "   docker-compose exec app composer install"
echo "   docker-compose logs -f"
echo ""
echo "🛑 To stop: docker-compose down"
echo "🔄 To restart: docker-compose restart"
