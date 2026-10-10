#!/bin/bash
# Starts the portal on Pterodactyl: sets it up from the egg's variables, migrates,
# prints what to put in the plugin's config.yml, then serves on SERVER_PORT and
# answers the commands typed in the panel's console.
set -e

# Stopping before the web server is up just ends the start.
trap 'exit 0' TERM INT

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

print_help() {
    echo
    echo "Commando's voor deze console:"
    echo "  help         Deze lijst"
    echo "  adminlink    Nieuwe link om beheerder te worden"
    echo "  check        Test of het portaal de plugin bereikt"
    echo "  migrate      Werk de database bij"
    echo "  stop         Stop het portaal"
    echo
}

frankenphp php-server --listen "0.0.0.0:${SERVER_PORT}" --root /app/public &
server=$!

stop_portal() {
    echo "Portaal stoppen..."
    kill -TERM "$server" 2>/dev/null || true
    wait "$server" 2>/dev/null || true
    exit 0
}
trap stop_portal TERM INT

echo "Portaal draait op poort ${SERVER_PORT}."
print_help

# The panel's console is stdin. Without it, just keep the web server running.
while kill -0 "$server" 2>/dev/null; do
    status=0
    read -r -t 5 command rest || status=$?
    if [ "$status" -gt 128 ]; then
        continue
    elif [ "$status" -ne 0 ]; then
        wait "$server"
        exit $?
    fi

    case "$command" in
        "") ;;
        help) print_help ;;
        adminlink) php artisan portal:admin-link || true ;;
        check) php artisan portal:check || true ;;
        migrate) php artisan migrate --force || true ;;
        stop) stop_portal ;;
        *) echo "Onbekend commando: ${command}. Typ help voor de lijst met commando's." ;;
    esac
done

# The web server stopped by itself.
wait "$server"
