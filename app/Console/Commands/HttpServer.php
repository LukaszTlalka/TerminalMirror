<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\HttpServer as AmpHttpServer;
use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Http\Status;
use Amp\Socket\Server;
use Psr\Log\NullLogger;

use App\SocketClient;

use Amp\Socket\ServerSocket;


class HttpServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'console-share:server-start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start custom HTTP server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    private function startServer()
    {
        \Amp\Loop::run(function () {
            $server = \Amp\Socket\listen("127.0.0.1:3005");

            echo "Listening for new connections on " . $server->getAddress() . " ..." . PHP_EOL;
            echo "Open your browser and visit http://" . $server->getAddress() . "/" . PHP_EOL;

            while ($socket = yield $server->accept()) {

                $reader = new \App\Server\ChunkReader\Socket($socket);
                $client = new \App\Server\Client($reader);
                //new SocketClient($socket);

                \Amp\Loop::delay(3000, function() use ($client) {
                    $client->end();
                });
                continue;

                // first asyncCall
                \Amp\asyncCall(function (\Amp\Socket\ServerSocket $socket) {
                    $buffer = "";
                    while (null !== $chunk = yield $socket->read()) {
                        $buffer .= "";
                    }
                }, $socket);

                \Amp\Loop::delay(5000, function() use ($socket) {
                    list($ip, $port) = explode(":", $socket->getRemoteAddress());

                    echo "Accepted connection from {$ip}:{$port}." . PHP_EOL;

                    $body = "Hey, your IP is {$ip} and your local port used is {$port}.";
                    $bodyLength = \strlen($body);

                    yield $socket->end("HTTP/1.1 200 OK\r\nConnection: close\r\nContent-Length: {$bodyLength}\r\n\r\n{$body}");
                });
            }
        });
    
    }

    public function debug()
    {
        \Amp\Loop::run(function(){

            // create temp file handle
            $tempHandle = tmpfile();

            $uri = stream_get_meta_data($tempHandle)['uri'];
            $socketHandle = fopen($uri, "r+");

            $serverSocket = new ServerSocket($socketHandle);
            $socketClient = new SocketClient($serverSocket);

            \Amp\Loop::repeat(1000, function() use ($tempHandle, $socketClient, $serverSocket, $socketHandle) {
                echo "New message\n";
                fwrite($tempHandle, 'Message we wrote to socket');
                fwrite($tempHandle, 'Message we wrote to socket');

                //echo fread($socketHandle, 9102)."\n";

                //return;

               while (null !== $chunk = yield $serverSocket->read()) {
                   echo ">>>>>>>>>{$chunk}<<<<<<<<<<<<<";
               }

                return $chunk;
                //return $socketClient->captureBufferRead();
            });


/*
            \Amp\Loop::delay(2000, function() use($serverSocket) {
                while (null !== $chunk = yield $serverSocket->read()) {
                    echo ">>>>>>>>>{$chunk}<<<<<<<<<<<<<";
                }
            });
*/
           \Amp\Loop::repeat(600, function() use ($socketClient) {
               echo "ticker\n";
//               $socketClient->captureBufferRead();
           });

//           echo "Loop finished\n";


 //              \Amp\Loop::delay(2000, function() {
 //                  echo "!!!!!!! finished !!!!!!!";
 //              });
        });

    
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->startServer();
        //$this->debug();
    }
}
