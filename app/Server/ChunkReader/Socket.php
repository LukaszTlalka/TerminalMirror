<?php

namespace App\Server\ChunkReader;

use App\Server\ChunkReader;

class Socket extends ChunkReader
{
    public function __construct(\Amp\Socket\ServerSocket $socket)
    {
        $this->socket = $socket;
    }

    public function start()
    {
        $readListenerClosure = $this->readListenerClosure;
        \Amp\asyncCall(function (\Amp\Socket\ServerSocket $socket) use ($readListenerClosure) {
            while (null !== $chunk = yield $socket->read()) {
                $readListenerClosure($chunk);
            }

            return $chunk;
        }, $this->socket);

        return $this;
    }

    public function end()
    {
        $this->socket->end("");
        //$body = "Thx I guess...";
        //$bodyLength = \strlen($body);
        //$this->socket->end("HTTP/1.1 200 OK\r\nConnection: close\r\nContent-Length: {$bodyLength}\r\n\r\n{$body}");
    }
}
