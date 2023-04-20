#!/bin/sh

su - $(id -un 1000)

cd /var/www

chmod -R 777 bootstrap/cache
chmod -R 777 storage

sh -c "composer install; php artisan migrate; if [ -z \$(grep '^APP_KEY=.\{10,\}' .env) ]; then php artisan key:generate; fi"
sh -c "php artisan storage:link"

if [ $DISABLE_NPM_AUTORUN != true ]; then
    npm config set strict-ssl=false && npm install && npm run production &
fi

php artisan system:set-uptime

service cron start

php artisan server:terminal-websocket debug &
php artisan serve:curl-in debug &

exec php artisan serve --host 0.0.0.0 --port 80
