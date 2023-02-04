#!/bin/sh
set -e

if [ $APP_ENV == 'local' ]; then
    if [ ! -d "/app/vendor" ]; then
        echo "No vendor directory found, installing dependencies"
        XDEBUG_MODE=off composer install
    fi
    XDEBUG_MODE=off php artisan migrate --force
    XDEBUG_MODE=off php artisan db:seed --force
fi
