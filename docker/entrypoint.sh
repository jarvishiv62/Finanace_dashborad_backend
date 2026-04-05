#!/bin/sh

# Create logs directory and set permissions
echo "Setting up storage directories..."
mkdir -p /var/www/html/storage/logs
chmod -R 777 /var/www/html/storage/logs

# Publish Sanctum migrations and run them
echo "Publishing Sanctum migrations..."
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force

# Run Laravel migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed the database
echo "Seeding database..."
php artisan db:seed --force

# Cache configuration for production
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisor
echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
