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

ARG USER_ID=1000
ARG GROUP_ID=1000

# Install runtime dependencies
RUN apk add --no-cache \
    git curl bash unzip \
    libpng libjpeg freetype libzip icu libxml2 oniguruma

# Create matching host user for permissions (handle UID/GID conflicts)
RUN set -eux; \
    existing_user=$(awk -F: '$3 == '${USER_ID}' {print $1}' /etc/passwd); \
    if [ -n "$existing_user" ] && [ "$existing_user" != "www-data" ]; then \
        deluser "$existing_user"; \
    fi; \
    existing_group=$(awk -F: '$3 == '${GROUP_ID}' {print $1}' /etc/group); \
    if [ -n "$existing_group" ] && [ "$existing_group" != "www-data" ]; then \
        delgroup "$existing_group"; \
    fi; \
    deluser www-data 2>/dev/null || true; \
    delgroup www-data 2>/dev/null || true; \
    addgroup -g ${GROUP_ID} -S www-data; \
    adduser -u ${USER_ID} -S www-data -G www-data

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Set user
USER www-data
