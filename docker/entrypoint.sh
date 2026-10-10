#!/bin/sh
# Prepares the portal on every start: waits for the database, migrates and caches,
# then starts the scheduler next to the web server.
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

# The scheduler renews plot rentals. It runs next to the web server only, not for
# a one-off `docker compose run portal php artisan …`; PORTAL_SCHEDULER=0 turns it
# off (for example when a cron job on the host already runs schedule:run).
if [ "${PORTAL_SCHEDULER:-1}" = "1" ] && [ "${1:-}" = "frankenphp" ]; then
    portal-scheduler &
fi

exec "$@"
