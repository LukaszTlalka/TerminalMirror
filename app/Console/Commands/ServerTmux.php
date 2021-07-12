<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServerTmux extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:tmux {port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tmux command to run all required servers for development';

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
        $port = $this->argument('port');

        $curlPort = env('APP_SERVER_PORT');

        $this->call('cache:clear');

        $this->info('Commands: ');

        $this->info("echo \"curl-in server: \" && php artisan server:curl-in debug");
        $this->info("echo \"WebSocket server: \" && php artisan server:terminal-websocket debug");
        $this->info("echo \"Watch outputClient: \" && php artisan storage:watcher 2283ca20ac84d62bf52819474a1d5f00 outputClient");
        $this->info("php artisan serve --host=0.0.0.0 --port=".$port."");
        $this->info("echo \"Push to inputClient: \" && php artisan storage:write-constant 2283ca20ac84d62bf52819474a1d5f00 inputClient");
        $this->info("echo \"Terminal share command\" && sleep 5 && curl --http1.1 -s -N -H \"Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00\" http://localhost:{$curlPort}/inputClient | script -q | curl -H \"Transfer-Encoding: chunked\" -H \"Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00\" -X POST -T - http://localhost:{$curlPort}/outputClient");

        $this->info("\n");

        $cmd =  "tmux " .
            "new-session 'echo \"curl-in server: \" && php artisan server:curl-in debug' \; " .
            "split-window 'echo \"WebSocket server: \" && php artisan server:terminal-websocket debug' \; " .
            "split-window -h 'echo \"Watch outputClient: \" && php artisan storage:watcher 2283ca20ac84d62bf52819474a1d5f00 outputClient' \; " .
            "split-window 'php artisan serve --host=0.0.0.0 --port=".$port."' \; " .
            "split-window 'echo \"Push to inputClient: \" && php artisan storage:write-constant 2283ca20ac84d62bf52819474a1d5f00 inputClient' \; " .
            "split-window 'echo \"Terminal share command\" && sleep 5 && curl --http1.1 -s -N -H \"Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00\" http://localhost:{$curlPort}/inputClient | script -q | curl -H \"Transfer-Encoding: chunked\" -H \"Authorization: Bearer 2283ca20ac84d62bf52819474a1d5f00\" -X POST -T - http://localhost:{$curlPort}/outputClient' \; " .
            "detach-client";

        $this->info($cmd);
        
        $this->info("Navigate to: http://localhost:".$port."/terminal/2283ca20ac84d62bf52819474a1d5f00");

        shell_exec($cmd);
        shell_exec("tmux set-option remain-on-exit on");
    }
}
