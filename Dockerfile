# syntax=docker/dockerfile:1

# Front-end assets.
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# PHP dependencies.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# What both images share: PHP with the extensions, the app and its assets.
FROM dunglas/frankenphp:1-php8.4-bookworm AS base

RUN install-php-extensions pdo_mysql intl zip bcmath pcntl opcache \
    && apt-get update && apt-get install -y --no-install-recommends libcap2-bin && rm -rf /var/lib/apt/lists/* \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ENV SERVER_NAME=:80 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PORTAL_MODE=single

WORKDIR /app

COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build

RUN php artisan package:discover --ansi \
    && ln -s /app/storage/app/public /app/public/storage

# Pterodactyl: runs as the panel's container user in /home/container, which holds
# .env and storage; the app itself stays in the image, so a restart with a newer
# image is an update.
FROM base AS pterodactyl

ENV HOME=/home/container \
    XDG_DATA_HOME=/home/container/.caddy/data \
    XDG_CONFIG_HOME=/home/container/.caddy/config

# Wings drops every capability, and exec refuses a binary that still asks for one.
RUN useradd --create-home --home-dir /home/container --shell /bin/bash container \
    && rm -rf /app/storage /app/.env \
    && ln -s /home/container/storage /app/storage \
    && ln -s /home/container/.env /app/.env \
    && chmod -R a+rwX /app/bootstrap/cache \
    && setcap -r /usr/local/bin/frankenphp

COPY docker/pterodactyl-entrypoint.sh /entrypoint.sh
COPY docker/pterodactyl-start.sh /usr/local/bin/portal-start

USER container
WORKDIR /home/container

ENTRYPOINT ["/bin/bash", "/entrypoint.sh"]

# The portal for Docker Compose: FrankenPHP (Caddy + PHP) serving /app/public,
# with automatic HTTPS for a domain.
FROM base AS app

COPY docker/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/portal-entrypoint

RUN touch .env \
    && setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/.env /data/caddy /config/caddy

USER www-data

EXPOSE 80 443 443/udp

ENTRYPOINT ["portal-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
