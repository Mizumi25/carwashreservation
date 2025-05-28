# Use official PHP image with FPM
FROM php:8.2-fpm

# Install system dependencies and PHP extensions needed for Laravel
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip curl libicu-dev \
    && docker-php-ext-install pdo_mysql zip exif pcntl bcmath gd intl

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all files into the container
COPY . .

# Install PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader

# Prepare Laravel app (storage link and cache config/routes/views)
RUN php artisan storage:link && php artisan config:cache && php artisan route:cache && php artisan view:cache

# Expose port 9000 to communicate with the web server (like Nginx)
EXPOSE 9000

# Start PHP-FPM server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
