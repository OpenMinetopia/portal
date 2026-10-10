#!/bin/sh
# Runs Laravel's scheduler (plot rental renewals) on every minute for as long as
# the portal runs; both images start it in the background. Its normal output is
# dropped; errors and the app's log (stderr in the images) still show.
cd /app || exit 1

while :; do
    php -r 'sleep(60 - (int) date("s"));'

    # Until portal:install has run there is nothing to schedule.
    if grep -Eq '^APP_KEY=.+' .env 2>/dev/null || [ -n "${APP_KEY:-}" ]; then
        php artisan schedule:run --no-interaction >/dev/null </dev/null || true
    fi
done
