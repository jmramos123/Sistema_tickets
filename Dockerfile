# Stage 1: Build assets and install PHP deps
FROM php:8.3-fpm AS build

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev libsqlite3-dev nodejs npm nginx supervisor

RUN docker-php-ext-install pdo pdo_pgsql pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader && \
    php artisan config:clear && \
    php artisan cache:clear

RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www

# Copy NGINX config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose HTTP port
EXPOSE 80

CMD ["/usr/bin/supervisord"]
