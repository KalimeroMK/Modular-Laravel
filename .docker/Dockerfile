# Stage 1: Build dependencies
FROM php:8.4-fpm-alpine AS builder

# Install build dependencies
RUN apk add --no-cache \
    icu-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libxml2-dev oniguruma-dev libzip-dev \
    autoconf g++ make

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring exif pcntl bcmath zip intl \
    && pecl install redis \
    && docker-php-ext-enable redis

# Stage 2: Final image
FROM php:8.4-fpm-alpine

# Install runtime dependencies
RUN apk add --no-cache \
    git curl bash unzip \
    libpng libjpeg freetype libzip icu libxml2 oniguruma

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Set user
USER www-data
