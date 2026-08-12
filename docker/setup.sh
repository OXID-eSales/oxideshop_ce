#!/usr/bin/env bash
# Bootstraps the shop inside the php container. Run via `make setup` or
# `docker compose exec php bash docker/setup.sh`.
set -euo pipefail
cd /var/www

if [ ! -f .env ]; then
    cp .env.dist .env
fi

composer install

# CE core ships no frontend or admin-backend theme; without them the shop
# prints the twig template name instead of rendering it, and /admin 500s
# on TemplateNotInChainException. Pull the dev-branch build of each so it
# matches this checkout's dev-b-8.0.x core packages (the tagged releases
# pull twig-component/apex-theme versions with mismatched service wiring).
composer require --no-interaction \
    oxid-esales/apex-theme:dev-b-8.0.x \
    oxid-esales/twig-component:dev-b-8.0.x \
    oxid-esales/twig-admin-theme:dev-b-8.0.x

CONSOLE="bin/oe-console"
AVAILABLE_COMMANDS=$("$CONSOLE" list --raw 2>/dev/null | awk '{print $1}')

run_if_available() {
    local command="$1"
    shift
    if echo "$AVAILABLE_COMMANDS" | grep -qx "$command"; then
        "$CONSOLE" "$command" "$@"
    else
        echo "Skipping '$command' (not registered by installed packages) - see 'bin/oe-console list'"
    fi
}

run_if_available oe:setup:shop --language=en
run_if_available oe:database:migrate
run_if_available oe:theme:activate apex
run_if_available oe:setup:demodata
run_if_available oe:admin:create-user admin@example.com admin123
run_if_available oe:module:install-assets
run_if_available oe:cache:clear

echo "Setup complete. Shop should be reachable at http://localhost/"
