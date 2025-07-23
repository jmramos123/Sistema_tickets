# Use official PHP image with required extensions
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Node.js + npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader && \
    php artisan config:clear && \
    php artisan cache:clear

# Build frontend assets
RUN npm install && npm run build

# Set proper permissions
RUN chown -R www-data:www-data /var/www

# Expose port for Laravel serve
EXPOSE 8000

# Start the Laravel app
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
