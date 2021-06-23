<?php

namespace App\Server;

class Command
{
    public static function getCommand($ref)
    {
        return 'php artisan bash:random | ' .
            'bash | ' .
            'curl -H "Transfer-Encoding: chunked" ' .
            '-H "Authorization: Bearer '.$ref.'"' .
            //' -X POST -T - '.env('APP_URL').':'.env("APP_SERVER_PORT").'/console-share?inputClient='.$ref.' -s';
            ' -X POST -T - '.env('APP_URL').':'.env("APP_SERVER_PORT").'/outputClient';
    }
}
