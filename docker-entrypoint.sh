#!/usr/bin/env sh
set -e

# Force Laravel to forget any cached config and pick up the Render env vars
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Run migrations and seeders against the DB_CONNECTION provided by Render (pgsql)
php artisan migrate:refresh --force --seed

# Finally, start Supervisor
exec /usr/bin/supervisord
