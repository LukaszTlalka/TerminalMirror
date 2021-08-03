<?php

namespace App\Server;

use Illuminate\Support\HtmlString;

class Command
{
    public static function getCommand($ref)
    {
        // We manually apply classes from the highlighting library to improve the looks a little.
        return new HtmlString(
            '<span class="hljs-built_in">curl</span> --version > /dev/null && ' . "\\\n" .
            '<span class="hljs-built_in">script</span> --version > /dev/null && ' . "\\\n"  .
            '<span class="hljs-built_in">curl</span> --http1.1 -s -N ' .
            '-H <span class="hljs-string">"Authorization: Bearer '.$ref.'"</span> ' .
            env('APP_URL').(env("APP_SERVER_PORT") ? (':'.env("APP_SERVER_PORT")) : '').'/inputClient | ' . "\\\n" .
            '<span class="hljs-built_in">script</span> -q /dev/null | ' . "\\\n" .
            '<span class="hljs-built_in">curl</span> -H <span class="hljs-string">"Transfer-Encoding: chunked"</span> ' .
            '-H <span class="hljs-string">"Authorization: Bearer '.$ref.'"</span>' .
            ' -X POST -T - '.env('APP_URL').(env("APP_SERVER_PORT") ? (':'.env("APP_SERVER_PORT")) : '').
            '/outputClient'
        );
    }
}
