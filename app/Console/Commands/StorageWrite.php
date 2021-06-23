<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Server\Storage;

class StorageWrite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:write {userID} {file} {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write data to a storage';

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
        $data = $this->argument('data');
        
        $storage = new Storage($userID, false);
        $storage->append($file, $data . "\n");
    }
}
