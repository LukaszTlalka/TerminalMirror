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
    {}
}
