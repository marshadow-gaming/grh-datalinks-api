#!/bin/bash

# Génère APP_KEY si absent
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache de configuration
php artisan config:cache
php artisan route:cache

# Migrations automatiques au démarrage
php artisan migrate --force

# Lien storage
php artisan storage:link || true

# Lance PHP-FPM en arrière-plan
php-fpm -D

# Lance Nginx au premier plan
nginx -g "daemon off;"