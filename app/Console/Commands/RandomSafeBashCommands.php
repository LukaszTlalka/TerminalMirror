<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RandomSafeBashCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bash:random';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate safe bash random commands';

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
        echo "ls\n";
        sleep(1);
        echo "whoami\n";
        sleep(1);
        echo "pwd\n";
    }
}
