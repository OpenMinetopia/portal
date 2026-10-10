#!/bin/bash
# Starts the portal on Pterodactyl: sets it up from the egg's variables, migrates,
# prints what to put in the plugin's config.yml, then serves on SERVER_PORT.
set -e

cd /app

mkdir -p /home/container/storage/app/public /home/container/storage/logs \
    /home/container/storage/framework/cache/data /home/container/storage/framework/sessions \
    /home/container/storage/framework/views /home/container/bootstrap/cache
touch /home/container/.env

# Wings can mount the image read-only, so Laravel's caches go to /home/container too.
export APP_CONFIG_CACHE=/home/container/bootstrap/cache/config.php \
    APP_ROUTES_CACHE=/home/container/bootstrap/cache/routes-v7.php \
    APP_EVENTS_CACHE=/home/container/bootstrap/cache/events.php \
    APP_SERVICES_CACHE=/home/container/bootstrap/cache/services.php \
    APP_PACKAGES_CACHE=/home/container/bootstrap/cache/packages.php

php artisan config:clear >/dev/null
php artisan portal:wait-for-database --timeout=60

# Every start applies the egg's variables; keys and data are kept.
php artisan portal:install --no-interaction --no-admin-link \
    --name="${PORTAL_NAME}" --url="${PORTAL_URL}" --server-address="${SERVER_ADDRESS:-}" \
    --plugin-host="${PLUGIN_HOST}" --plugin-port="${PLUGIN_PORT:-4567}" \
    --db-connection=mysql --db-host="${DB_HOST}" --db-port="${DB_PORT:-3306}" \
    --db-database="${DB_DATABASE}" --db-username="${DB_USERNAME}" --db-password="${DB_PASSWORD}"

# Until someone is admin, every start prints a fresh one-time admin link.
php artisan portal:admin-link --if-no-admin

php artisan config:cache >/dev/null
php artisan route:cache >/dev/null
php artisan view:cache >/dev/null

echo "Portaal draait op poort ${SERVER_PORT}."
exec frankenphp php-server --listen "0.0.0.0:${SERVER_PORT}" --root /app/public
