#!/bin/bash

cd /share

composer install
npm run dev

php artisan server:terminal false 3005 &
php artisan server:curl-in false 3006 &

exec apache2-foreground
