#!/bin/sh

# Generate APP_KEY (ensure .env exists first)
echo "Setting up .env file..."
if [ ! -f .env ]; then
    echo "APP_NAME=Finance_backend" > .env
    echo "APP_ENV=production" >> .env
    echo "APP_URL=https://finanace-dashborad-backend.onrender.com" >> .env
    echo "APP_DEBUG=false" >> .env
    echo "DB_CONNECTION=sqlite" >> .env
    echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env
fi

echo "Generating APP_KEY..."
php artisan key:generate --force

# Create logs directory and set permissions
echo "Setting up storage directories..."
mkdir -p /var/www/html/storage/logs
chmod -R 777 /var/www/html/storage/logs

# Set correct APP_URL for production
echo "Setting APP_URL..."
export APP_URL="https://finanace-dashborad-backend.onrender.com"

# Clear caches to ensure fresh environment
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Regenerate config cache with new APP_KEY
echo "Recaching configuration..."
php artisan config:cache

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
