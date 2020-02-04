<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Server\Storage as ServerStorage;

class SessionController extends Controller
{
    public function create()
    {
        do {
            $ref = md5(__DIR__ . rand(0,PHP_INT_MAX));
            $storage = new ServerStorage($ref);
        } while ( $storage->referenceExists() );

        $storage->inputAppend("initialized\n");

        $command = \App\Server\Command::getCommand($ref);

        return view('session.new', compact('command'));
    }

    public function terminal()
    {
        return view('session.terminal');
    }
}
