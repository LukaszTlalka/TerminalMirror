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


/**
 * NOT IN USE
 */
class CurlInHttpServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:curl-in {debug?}';

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
        $command = $this;
        $debug = $this->argument('debug') ? true : false;

        $logger = new class($command, $debug) {
            public function __construct($command, $debug) {
                    $this->command = $command;
                    $this->debug = $debug;
            }

            public function __call($method, $params) {
                if ($this->debug) {
                    $this->command->$method(...$params);
                }
            }
        };


        \Amp\Loop::run(function () use ($logger) {
            $server = \Amp\Socket\listen("0.0.0.0:".env("APP_SERVER_PORT"));

            while ($socket = yield $server->accept()) {
                $reader = new \App\Server\ChunkReader\Socket($socket);
                $client = new \App\Server\Client($reader, $logger);
                //new SocketClient($socket);

                \Amp\Loop::delay(3000, function() use ($client) {
                    //$client->end();
                });
            }
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
    }
}
