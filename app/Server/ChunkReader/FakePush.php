<?php

namespace App\Server\ChunkReader;

use App\Server\ChunkReader;

class FakePush extends ChunkReader
{
    public function start()
    {
        return $this;
    }

    public function end()
    {
        $body = "Thx I guess...";
        $bodyLength = \strlen($body);
        $this->socket->end("HTTP/1.1 200 OK\r\nConnection: close\r\nContent-Length: {$bodyLength}\r\n\r\n{$body}");
    }
}
