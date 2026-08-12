#!/usr/bin/env bash
# The repo is bind-mounted from the host, so it's owned by the host user's
# uid/gid rather than www-data. Make the directories OXID writes to at
# runtime writable by anyone so Apache (running as www-data) can use them.
set -e

for dir in var/cache var/generated var/configuration var/configuration.dev source/log source/tmp source/export source/Application/views; do
    if [ -d "/var/www/$dir" ]; then
        chmod -R go+rwX "/var/www/$dir" || true
    fi
done

exec apache2-foreground
