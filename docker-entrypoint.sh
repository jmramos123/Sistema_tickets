#!/usr/bin/env sh
set -e

# Run database migrations
php artisan migrate --force

# Then start all services
exec /usr/bin/supervisord
