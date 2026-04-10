#!/bin/sh

cd /var/www/html

# Use Render's PORT or default to 8000
PORT=${PORT:-8000}

# Export SSL mode for psql (Neon requires SSL)
export PGSSLMODE="${DB_SSLMODE:-require}"

# Install composer dependencies if vendor is missing
if [ ! -f vendor/autoload.php ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Generate app key only if not already set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force --no-interaction 2>/dev/null || true
fi

# Clear config cache
php artisan config:clear 2>/dev/null || true

# ============================================================
# DATABASE: Run pending migrations (preserves existing data)
# ============================================================
echo "==> [DB] Connection info:"
echo "    DB_HOST=${DB_HOST}"
echo "    DB_PORT=${DB_PORT}"
echo "    DB_DATABASE=${DB_DATABASE}"
echo "    DB_USERNAME=${DB_USERNAME}"
echo "    DB_SSLMODE=${DB_SSLMODE}"
echo "    PGSSLMODE=${PGSSLMODE}"

echo "==> [DB] Running pending migrations..."
php artisan migrate --force --no-interaction 2>&1

MIGRATE_EXIT=$?

if [ $MIGRATE_EXIT -ne 0 ]; then
    echo ""
    echo "==> [DB] !!! Migration failed (exit code: $MIGRATE_EXIT) !!!"
fi

# Cache for production performance
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Create storage link
php artisan storage:link 2>/dev/null || true

# Set permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "==> Starting Laravel on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT}
