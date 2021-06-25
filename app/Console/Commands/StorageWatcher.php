<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Server\Storage;

class StorageWatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:watcher {userID} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $userID = $this->argument('userID');
        $file = $this->argument('file');

        $storage = new Storage($userID, false);

        $id = 0;
        while(!sleep(1)) {

            foreach ($storage->get($file, $id+1) as $id => $data) {
                print_r($data['data']);
            };
        }
    }
}
