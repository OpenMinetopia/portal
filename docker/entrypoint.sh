#!/bin/sh
# Prepares the portal on every start: waits for the database, migrates and caches.
# Before `php artisan portal:install` has run there is no APP_KEY yet; then only
# the web server starts.
set -e

cd /app

if [ "${PORTAL_SKIP_BOOT:-0}" != "1" ]; then
    php artisan config:clear >/dev/null

    if php artisan portal:wait-for-database --timeout="${PORTAL_DB_TIMEOUT:-60}"; then
        if grep -Eq '^APP_KEY=.+' .env 2>/dev/null || [ -n "${APP_KEY:-}" ]; then
            php artisan migrate --force
            php artisan storage:link >/dev/null 2>&1 || true
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
        else
            echo "Het portaal is nog niet ingericht. Draai: php artisan portal:install"
        fi
    fi
fi

exec "$@"
