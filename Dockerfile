# Use official PHP image with FPM
FROM php:8.2-fpm

# Install system dependencies and PHP extensions needed for Laravel
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip curl libicu-dev \
    && docker-php-ext-install pdo_mysql zip exif pcntl bcmath gd intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock ./

# Install PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy all application files
COPY . .

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Run post-install scripts
RUN composer run-script post-install-cmd --no-interaction || true

# Generate application key if not set and run Laravel optimizations
RUN php artisan key:generate --no-interaction || true \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose the port that Render assigns
EXPOSE 8080

# Start Laravel's built-in server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]

