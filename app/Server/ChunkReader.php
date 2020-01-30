<?php

namespace App\Server;

abstract class ChunkReader
{
    /**
     * @var closure fired when we get the data from socket
     */
    public $readListenerClosure = null;

    public function registerReadListener($closure)
    {
        $this->readListenerClosure = $closure;
    }

    public function pushMessage($chunk)
    {
        $closure = $this->readListenerClosure;
        $closure($chunk);
    }

    public abstract function start();
    public abstract function end();
}
