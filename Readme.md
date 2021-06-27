# Console Share

Laravel based system that allows for easy console sharing over web using nothing but curl.

### Operation Principle

System requires 3 http servers running to operate:

1) `php artisan serve`

Served by laravel framework

2) `php artisan server:terminal-websocket debug`

[Client: Web Browser] -> [Server: Websocket Server]


3) `php artisan serve:curl-in debug`

For curl communication:

[inputClient curl ] | script -q | outputClient curl ...


#### curl command 

The basic operation principle is quite simple:

1) Read input from custom http socket amp server (php artisan serve:curl-in)
2) Pipe the result into script -q  command
3) Write output to custom http socket amp server (php artisan serve:curl-in)

[inputClient curl ] | script -q | outputClient curl ...

Command example:

```bash
curl --http1.1 -s -N -H "Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00" http://localhost:3005/inputClient | script -q | curl -H "Transfer-Encoding: chunked" -H "Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00" -X POST -T - http://localhost:3005/outputClient
```

#### Web browser operation

## Testing  

- configure .env WEBSOCKET_SERVER_PORT and run the server `php artisan server:terminal-websocket debug`

- Create session by navigating to:  

http://host:port/new-session

- Open input client connection: 

ws://localhost:[WEBSOCKET_SERVER_PORT]/console-share?inputClient=[md5 from the step 2]



## Debug Amphp httpd server  

php artisan bash:random | bash | curl -H "Transfer-Encoding: chunked"  -H "Content-Type: application/json"  -X POST -T - http://localhost:3005/ -s

curl -H "Transfer-Encoding: chunked" -X POST -T - http://localhost:3005/console-share?inputClient=1675d32308cc13ddb8a14c6b5df0f59c -s


## IDEAS

socat - EXEC:'bash',pty,setsid,ctty | cat
