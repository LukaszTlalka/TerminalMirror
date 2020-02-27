# Console Share

Laravel based system that allows for easy console sharing over web using nothing but curl.

Servers list:
- laravel main - port 80 -> serve main page
- app server - env('APP_SERVER_PORT') -> curl command line handling. Used for storring output of the commands.
- terminal websocket - used on the page to exchange information with the curl proxy


## Debug Amphp httpd server

php artisan bash:random | bash | curl -H "Transfer-Encoding: chunked"  -H "Content-Type: application/json"  -X POST -T - http://localhost:3005/ -s
