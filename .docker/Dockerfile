# Stage 1: Build dependencies
FROM php:8.3-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git curl nano unzip bash icu-dev zip nodejs npm \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libxml2-dev oniguruma-dev libzip-dev shadow \
    autoconf g++ make

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath zip intl \
    && pecl install redis \
    && docker-php-ext-enable redis  # Enable Redis extension

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Set user permissions (проверка дали `www-data` групата постои)
RUN id -u www-data &>/dev/null || adduser -D -u 1000 -G www-data www-data

# Stage 2: Final image (Minimal)
FROM php:8.3-fpm-alpine

# Install runtime dependencies (не бара php8-pecl-redis повеќе)
RUN apk add --no-cache libpng libjpeg freetype libzip icu

# Copy built dependencies from builder
COPY --from=builder /usr/local /usr/local
COPY --from=builder /usr/bin/composer /usr/bin/composer
COPY --from=builder /usr/bin/node /usr/bin/node
COPY --from=builder /usr/bin/npm /usr/bin/npm

# Enable Redis extension
RUN docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Set user
USER www-data
