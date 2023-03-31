<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class WebsocketHttpServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:terminal-websocket {debug?} {port?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create http server for handling terminal commands';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Handle debug log
        $debug = $this->argument('debug') == 'true' ? true : false;
        $port = $this->argument('port') ? $this->argument('port') : env("WEBSOCKET_SERVER_PORT");

        $command = $this;

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

        // ------------------------------------


        $loop = \React\EventLoop\Factory::create();
        $host = parse_url(config('app.url'))['host'];

        // Run the server application through the WebSocket protocol on port env('WEBSOCKET_SERVER_PORT')
        $app = new \Ratchet\App($host, $port, '0.0.0.0', $loop);
        $app->route('/console-share', new \App\Server\Websocket($loop, $logger), array('*'));
        $app->route('/echo', new \Ratchet\Server\EchoServer, array('*'));
        $app->run();
    }
}
