#!/bin/bash
# Pterodactyl runs the egg's startup command through this, as with its own images.
cd /home/container || exit 1

MODIFIED_STARTUP=$(echo -e "${STARTUP}" | sed -e 's/{{/${/g' -e 's/}}/}/g')
echo ":/home/container$ ${MODIFIED_STARTUP}"

# shellcheck disable=SC2086
eval ${MODIFIED_STARTUP}
