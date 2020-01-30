# Console Share

Laravel based page that allows for easy console sharing over web using nothing but curl.



## Debug Amphp httpd server

php artisan bash:random | bash | curl -H "Transfer-Encoding: chunked"  -H "Content-Type: application/json"  -X POST -T - http://localhost:3005/ -s

