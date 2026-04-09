FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git curl zip unzip libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev icu-dev postgresql-dev oniguruma-dev \
    nodejs npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy application
COPY . .

# Install dependencies at build time (will be overridden by bind mount in dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Expose port
EXPOSE 8000

# Use entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]
