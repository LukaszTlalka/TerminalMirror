#!/bin/bash

cd /share

php artisan server:terminal false 3005 &
php artisan server:curl-in false 3006 &

exec apache2-foreground
