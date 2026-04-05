#!/bin/sh

echo "==> Setting up .env..."
if [ ! -f .env ]; then
    echo "APP_NAME=Finance_backend" > .env
    echo "APP_ENV=production" >> .env
    echo "APP_URL=https://finanace-dashborad-backend.onrender.com" >> .env
    echo "APP_DEBUG=true" >> .env
    echo "DB_CONNECTION=sqlite" >> .env
    echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env
fi

if ! grep -q "APP_KEY=" .env; then
    echo "APP_KEY=" >> .env
fi
if grep -q "APP_KEY=$" .env; then
    php artisan key:generate --force
fi

echo "==> Setting up storage..."
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/database
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/database
chmod -R 775 /var/www/html/bootstrap/cache

echo "==> Clearing stale caches..."
php artisan config:clear
php artisan view:clear

echo "==> Discovering packages..."
php artisan package:discover --ansi

echo "==> Caching config..."
php artisan config:cache

echo "==> Running migrations..."
php artisan migrate --force || true

echo "==> Clearing cache (after migrations)..."
php artisan cache:clear || true

echo "==> Seeding database..."
php artisan db:seed --force || true

echo "==> Caching routes and views..."
php artisan route:cache
php artisan view:cache

echo "==> Recent Laravel logs..."
tail -50 /var/www/html/storage/logs/laravel.log 2>/dev/null || echo "No log file yet"

echo "==> Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf