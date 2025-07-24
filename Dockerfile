# Stage 1: Build assets and install PHP deps
FROM php:8.3-fpm AS build

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev libsqlite3-dev nodejs npm nginx supervisor \
 && docker-php-ext-install pdo pdo_pgsql pgsql zip pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Add dummy envs to avoid null error in artisan
ENV REVERB_APP_KEY=local
ENV REVERB_APP_SECRET=local
ENV REVERB_APP_ID=local
ENV REVERB_HOST=127.0.0.1
ENV REVERB_PORT=6001
ENV REVERB_SCHEME=http

# Prevent Laravel from hitting missing SQLite files during build
ENV CACHE_STORE=array
ENV SESSION_DRIVER=array

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader && \
    php artisan config:clear && \
    php artisan cache:clear

# Build frontend assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www

# Copy NGINX config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint script and make executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose HTTP port
EXPOSE 80

# Use entrypoint to run migrations before starting services
ENTRYPOINT ["docker-entrypoint.sh"]
