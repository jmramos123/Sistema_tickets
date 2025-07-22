# Use official PHP image with necessary extensions
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev libpq-dev libssl-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set proper permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose the port Laravel will serve on
EXPOSE 10000

# Start Laravel's built-in server
CMD php artisan serve --host=0.0.0.0 --port=10000
