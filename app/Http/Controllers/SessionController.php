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

        $storage->append('log', "initialized");

        $command = \App\Server\Command::getCommand($ref);

        return view('session.new', compact('command', 'ref'))
            ->withHeaders([
                "Cache-Control" => "no-cache, no-store, must-revalidate",
                "Pragma" => "no-cache",
                "Expires" => "0"
            ]);
    }

    public function terminal(string $hash)
    {
        $websocket = [
            'host' => strtr(config('app.url'),[
                'https://' => 'wss://',
                'http://' => 'ws://',
            ]),
            'port' => env('WEBSOCKET_SERVER_PORT'),
            'client' => $hash,
        ];

        return view('session.terminal', compact('websocket'));
    }

    public function checkCurlSession($ref)
    {
        $storage = new ServerStorage($ref);

        $log = $storage->get('log');
        if (count($log) >= 2) {
            return response()->json(['success' => true, 'redirect' => route('terminal-session', [$ref])]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
