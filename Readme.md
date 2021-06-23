# Console Share

Laravel based system that allows for easy console sharing over web using nothing but curl.

### Operation Principle

The basic operation principle is quite simple:

1) Read input from websocket
2) Pipe the result into bash command
3) Write the output to websocket

[input websocket curl ... ] | bash | output websocket curl ...



Command example:

ws://localhost:3005/console-share?inputClient=5b24132605be249f2a50286e24ab0764

ws://localhost:3005/console-share?inputClient=5b24132605be249f2a50286e24ab0764


```bash
curl -H "Transfer-Encoding: chunked"  -H "Content-Type: application/json"  -X POST -T - http://localhost:3005/console-share?inputClient=5b24132605be249f2a50286e24ab0764 -s | \
bash | \
curl -H "Transfer-Encoding: chunked"  -H "Content-Type: application/json"  -X POST -T - http://localhost:3005/console-share?outputClient=5b24132605be249f2a50286e24ab0764 -s
```

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
