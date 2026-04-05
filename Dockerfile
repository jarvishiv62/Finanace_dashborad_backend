FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (layer cache optimization)
COPY composer.json composer.lock ./

# Install PHP dependencies (no dev dependencies in production)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy rest of application
COPY . .

# Ensure .env file exists and has correct permissions
RUN if [ ! -f .env ]; then \
        echo "Creating .env from environment variables..."; \
        echo "APP_NAME=Finance_backend" > .env; \
        echo "APP_ENV=production" >> .env; \
        echo "APP_URL=https://finanace-dashborad-backend.onrender.com" >> .env; \
        echo "APP_DEBUG=false" >> .env; \
        echo "DB_CONNECTION=sqlite" >> .env; \
        echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> .env; \
    fi

# Create SQLite database if it doesn't exist
RUN mkdir -p database && touch database/database.sqlite

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Create supervisor log directory
RUN mkdir -p /var/log/supervisor

# Make entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]