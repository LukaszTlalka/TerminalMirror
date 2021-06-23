<?php

namespace App\Server;

class Command
{
    public static function getCommand($ref)
    {
        return 'curl --http1.1 -s -N ' .
            '-H "Authorization: Bearer '.$ref.'" ' .
            env('APP_URL').':'.env("APP_SERVER_PORT").'/outputClient | ' .
            'bash | ' .
            'curl -H "Transfer-Encoding: chunked" ' .
            '-H "Authorization: Bearer '.$ref.'"' .
            //' -X POST -T - '.env('APP_URL').':'.env("APP_SERVER_PORT").'/console-share?inputClient='.$ref.' -s';
            ' -X POST -T - '.env('APP_URL').':'.env("APP_SERVER_PORT").'/outputClient';
    }
}
