#!/bin/sh
set -e

cd /var/www/html

# Use Render's PORT or default to 8000
PORT=${PORT:-8000}

# Install composer dependencies if vendor is missing
if [ ! -f vendor/autoload.php ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Generate app key only if not already set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi

# Clear config cache (file-based, safe before migration)
php artisan config:clear 2>/dev/null || true

# Run migrations first (cache table must exist before cache:clear)
php artisan migrate --force --no-interaction 2>/dev/null || true

# Clear cache (now safe since cache table exists)
php artisan cache:clear 2>/dev/null || true

# Create storage link if not exists
php artisan storage:link 2>/dev/null || true

# Set permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "Starting Laravel server on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT}
