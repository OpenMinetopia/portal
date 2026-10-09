#!/usr/bin/env bash
# OpenMinetopia Portal installeren met Docker.
#
#   curl -fsSL https://raw.githubusercontent.com/OpenMinetopia/portal/main/install.sh | sudo bash
#
# Zonder vragen (bijvoorbeeld voor automatisering) met omgevingsvariabelen:
#
#   OMT_NONINTERACTIVE=1 OMT_ADDRESS=portaal.jouwserver.nl OMT_EMAIL=jij@example.com \
#   OMT_NAME="Mijn Server" OMT_SERVER_ADDRESS=play.jouwserver.nl \
#   OMT_PLUGIN_HOST=host.docker.internal OMT_PLUGIN_PORT=4567 bash install.sh
#
# Andere variabelen: OMT_DIR (standaard /opt/openminetopia-portal), OMT_HTTP_PORT,
# OMT_HTTPS_PORT, OMT_IMAGE, OMT_REF (git-branch voor docker-compose.yml),
# OMT_BIN_DIR (waar het commando openminetopia-portal komt).
set -euo pipefail

OMT_DIR="${OMT_DIR:-/opt/openminetopia-portal}"
OMT_REF="${OMT_REF:-main}"
OMT_IMAGE="${OMT_IMAGE:-ghcr.io/openminetopia/portal:latest}"
OMT_BIN_DIR="${OMT_BIN_DIR:-/usr/local/bin}"
OMT_NONINTERACTIVE="${OMT_NONINTERACTIVE:-0}"
RAW_URL="https://raw.githubusercontent.com/OpenMinetopia/portal/${OMT_REF}"

bold() { printf '\033[1m%s\033[0m\n' "$*"; }
info() { printf '  %s\n' "$*"; }
fail() { printf '\n\033[31mFout:\033[0m %s\n' "$*" >&2; exit 1; }

# Questions come from the terminal, also when this script is piped into bash.
ask() {
    local question="$1" default="${2:-}" answer
    if [ "$OMT_NONINTERACTIVE" = "1" ]; then
        printf '%s' "$default"
        return
    fi
    if [ -n "$default" ]; then
        printf '%s [%s]: ' "$question" "$default" >/dev/tty
    else
        printf '%s: ' "$question" >/dev/tty
    fi
    IFS= read -r answer </dev/tty || true
    printf '%s' "${answer:-$default}"
}

confirm() {
    local answer
    [ "$OMT_NONINTERACTIVE" = "1" ] && return 0
    answer="$(ask "$1 (j/n)" "${2:-j}")"
    case "$answer" in j|J|ja|Ja|y|Y|yes) return 0 ;; *) return 1 ;; esac
}

random_secret() {
    LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c "${1:-32}" || true
}

is_ip() {
    [[ "$1" =~ ^[0-9]{1,3}(\.[0-9]{1,3}){3}$ ]] || [[ "$1" == *:*:* ]]
}

# Never from stdin: with `curl … | bash` the rest of this script is still waiting there.
compose() {
    docker compose --project-directory "$OMT_DIR" -f "$OMT_DIR/docker-compose.yml" "$@" </dev/null
}

echo
bold "OpenMinetopia Portal installeren"
info "Je portaal komt in $OMT_DIR en draait in Docker."
echo

# 1. Docker.
if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
    [ "$(uname -s)" = "Linux" ] || fail "Docker met Docker Compose is nodig. Installeer Docker Desktop en start dit script opnieuw."
    echo "Docker (met Docker Compose) is niet geïnstalleerd."
    if confirm "Wil je Docker nu installeren via get.docker.com?"; then
        [ "$(id -u)" -eq 0 ] || fail "Start dit script met sudo om Docker te installeren."
        curl -fsSL https://get.docker.com | sh
    else
        fail "Installeer Docker en start dit script opnieuw: https://docs.docker.com/engine/install/"
    fi
fi
docker info >/dev/null 2>&1 || fail "Docker draait niet, of je hebt geen rechten. Start het script met sudo."

if [ -f "$OMT_DIR/.env" ]; then
    echo "Er staat al een portaal in $OMT_DIR."
    confirm "Opnieuw instellen? Je gegevens en sleutels blijven bewaard." || exit 0
fi

# 2. Questions.
echo
bold "Het adres van je portaal"
info "Een domeinnaam (bijvoorbeeld portaal.jouwserver.nl) krijgt automatisch HTTPS."
info "Die domeinnaam moet al naar het IP-adres van deze machine wijzen."
info "Heb je geen domein? Vul dan het IP-adres van deze machine in."
ADDRESS="$(ask "Domeinnaam of IP-adres" "${OMT_ADDRESS:-}")"
ADDRESS="${ADDRESS#http://}"; ADDRESS="${ADDRESS#https://}"; ADDRESS="${ADDRESS%%/*}"
[ -n "$ADDRESS" ] || fail "Vul een domeinnaam of IP-adres in."

HTTP_PORT="${OMT_HTTP_PORT:-80}"
HTTPS_PORT="${OMT_HTTPS_PORT:-443}"
EMAIL=""

if is_ip "$ADDRESS"; then
    SERVER_NAME=":80"
    APP_URL="http://${ADDRESS}"
    [ "$HTTP_PORT" = "80" ] || APP_URL="${APP_URL}:${HTTP_PORT}"
    CADDY_OPTIONS=""
else
    echo
    info "Voor het HTTPS-certificaat (Let's Encrypt) is een e-mailadres nodig."
    EMAIL="$(ask "E-mailadres" "${OMT_EMAIL:-}")"
    [ -n "$EMAIL" ] || fail "Vul een e-mailadres in voor het certificaat."
    SERVER_NAME="$ADDRESS"
    APP_URL="https://${ADDRESS}"
    [ "$HTTPS_PORT" = "443" ] || APP_URL="${APP_URL}:${HTTPS_PORT}"
    CADDY_OPTIONS="email ${EMAIL}"
fi

echo
bold "Je server"
NAME="$(ask "Naam van je server (zo heet het portaal)" "${OMT_NAME:-}")"
[ -n "$NAME" ] || fail "Vul een naam in."
SERVER_ADDRESS="$(ask "Adres waarmee spelers verbinden in Minecraft, bijvoorbeeld play.jouwserver.nl (mag leeg)" "${OMT_SERVER_ADDRESS:-}")"

echo
bold "De OpenMinetopia-plugin"
info "Het portaal praat met de plugin via de REST-API van de plugin."
info "1) De Minecraft-server draait op deze machine"
info "2) De Minecraft-server draait ergens anders"
if [ -n "${OMT_PLUGIN_HOST:-}" ]; then
    PLUGIN_HOST="$OMT_PLUGIN_HOST"
else
    WHERE="$(ask "Kies 1 of 2" "1")"
    if [ "$WHERE" = "2" ]; then
        PLUGIN_HOST="$(ask "IP-adres of hostnaam van de Minecraft-server")"
        [ -n "$PLUGIN_HOST" ] || fail "Vul het adres van de Minecraft-server in."
    else
        PLUGIN_HOST="host.docker.internal"
    fi
fi
PLUGIN_PORT="$(ask "Poort van de plugin-API (rest-api → port)" "${OMT_PLUGIN_PORT:-4567}")"
if ! [[ "$PLUGIN_PORT" =~ ^[0-9]+$ ]] || [ "$PLUGIN_PORT" -lt 1 ] || [ "$PLUGIN_PORT" -gt 65535 ]; then
    fail "De poort moet een getal tussen 1 en 65535 zijn."
fi

# 3. Files.
mkdir -p "$OMT_DIR"
cd "$OMT_DIR"

if [ -n "${OMT_SOURCE:-}" ]; then
    cp "$OMT_SOURCE/docker-compose.yml" docker-compose.yml
else
    curl -fsSL "$RAW_URL/docker-compose.yml" -o docker-compose.yml || fail "Kon docker-compose.yml niet downloaden van $RAW_URL."
fi

DB_PASSWORD="$(grep -E '^DB_PASSWORD=' .env 2>/dev/null | cut -d= -f2- || true)"
DB_ROOT_PASSWORD="$(grep -E '^DB_ROOT_PASSWORD=' .env 2>/dev/null | cut -d= -f2- || true)"
DB_PASSWORD="${DB_PASSWORD:-$(random_secret 32)}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-$(random_secret 32)}"

cat > .env <<ENV
# Gemaakt door install.sh. De instellingen van het portaal zelf staan in portal.env.
PORTAL_IMAGE=${OMT_IMAGE}
PORTAL_SERVER_NAME=${SERVER_NAME}
PORTAL_CADDY_OPTIONS=${CADDY_OPTIONS}
PORTAL_HTTP_PORT=${HTTP_PORT}
PORTAL_HTTPS_PORT=${HTTPS_PORT}
DB_PASSWORD=${DB_PASSWORD}
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
ENV
chmod 600 .env

# The portal writes its own settings here (owned by www-data in the container).
touch portal.env
chown 33:33 portal.env 2>/dev/null || chmod 666 portal.env
chmod 600 portal.env 2>/dev/null || true

# 4. Start.
echo
bold "Portaal starten…"
if [ "${OMT_SKIP_PULL:-0}" != "1" ]; then
    compose pull --quiet
fi
compose up -d

info "Wachten op de database…"
for _ in $(seq 1 60); do
    compose exec -T portal php artisan portal:wait-for-database --timeout=2 >/dev/null 2>&1 && break
    sleep 2
done
compose exec -T portal php artisan portal:wait-for-database --timeout=10 || fail "De database start niet. Bekijk de logs: docker compose -f $OMT_DIR/docker-compose.yml logs db"

OUTPUT="$(compose exec -T portal php artisan portal:install --no-interaction \
    --name="$NAME" --url="$APP_URL" --server-address="$SERVER_ADDRESS" \
    --plugin-host="$PLUGIN_HOST" --plugin-port="$PLUGIN_PORT" \
    --db-connection=mariadb --db-host=db --db-port=3306 --db-database=portal \
    --db-username=portal --db-password="$DB_PASSWORD")" || { echo "$OUTPUT"; fail "Inrichten van het portaal is mislukt."; }

info "Wachten tot het portaal antwoordt…"
RESPONDING=0
for _ in $(seq 1 30); do
    if curl -s -o /dev/null -H "Host: ${ADDRESS}" "http://127.0.0.1:${HTTP_PORT}/up"; then
        RESPONDING=1
        break
    fi
    sleep 2
done

# 5. The command for later.
cat > "$OMT_BIN_DIR/openminetopia-portal" <<WRAPPER || true
#!/bin/sh
# Beheer van het OpenMinetopia Portal in $OMT_DIR.
set -e
cd "$OMT_DIR"
case "\${1:-}" in
    update)
        docker compose pull
        docker compose up -d
        echo "Bijgewerkt. Het portaal migreert de database zelf bij het opstarten."
        ;;
    backup)
        stamp=\$(date +%Y%m%d-%H%M%S)
        docker compose exec -T db sh -c 'exec mariadb-dump -uroot -p"\$MARIADB_ROOT_PASSWORD" --single-transaction portal' | gzip > "backup-\$stamp.sql.gz"
        docker compose exec -T portal tar -C /app/storage/app -czf - public > "bestanden-\$stamp.tar.gz"
        echo "Back-up gemaakt: $OMT_DIR/backup-\$stamp.sql.gz en $OMT_DIR/bestanden-\$stamp.tar.gz"
        ;;
    admin-link) docker compose exec portal php artisan portal:admin-link ;;
    check) docker compose exec portal php artisan portal:check ;;
    instellen) docker compose exec portal php artisan portal:install ;;
    logs) docker compose logs -f portal ;;
    stop) docker compose stop ;;
    start) docker compose up -d ;;
    artisan) shift; docker compose exec portal php artisan "\$@" ;;
    *)
        echo "Gebruik: openminetopia-portal <commando>"
        echo "  update      nieuwste versie ophalen en herstarten"
        echo "  backup      database en geüploade bestanden opslaan in $OMT_DIR"
        echo "  admin-link  nieuwe eenmalige link om beheerder te worden"
        echo "  check       test de verbinding met de plugin"
        echo "  instellen   naam, adres of plugin opnieuw instellen"
        echo "  logs        logs van het portaal volgen"
        echo "  start, stop"
        echo "  artisan     een artisan-commando uitvoeren"
        ;;
esac
WRAPPER
chmod +x "$OMT_BIN_DIR/openminetopia-portal" 2>/dev/null || true

# 6. Done.
line="────────────────────────────────────────────────────────────────────────"
echo
echo "$line"
echo "$OUTPUT" | sed -n '/Zet dit in/,$p' | grep -v 'portal:check'
echo "$line"
echo
bold "Klaar!"
info "Portaal: $APP_URL"
[ "$RESPONDING" = "1" ] || info "Het portaal antwoordt nog niet; bekijk de logs met: openminetopia-portal logs"
if ! is_ip "$ADDRESS"; then
    info "Het HTTPS-certificaat wordt bij het eerste bezoek aangevraagd; dat kan een minuut duren."
fi
echo
info "Bijwerken:           openminetopia-portal update"
info "Back-up maken:       openminetopia-portal backup"
info "Plugin testen:       openminetopia-portal check"
info "Nieuwe beheerderslink: openminetopia-portal admin-link"
echo
