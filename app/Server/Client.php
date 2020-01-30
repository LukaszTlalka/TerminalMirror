<?php

namespace App\Server;

class Client
{
    /**
     * @var incomming socket data  buffer
     */
    private $messageBuffer = "";

    /**
     * @var Wether Expect: 100-continue has been reached
     */
    private $expect100Reached = false;

    /**
     * @var \App\Server\ChunkReader - data that delivers chunks / usually socket
     */
    private $reader = null;

    public function __construct(\App\Server\ChunkReader $reader)
    {
        $this->reader = $reader;

        $client = $this;

        $reader->registerReadListener(function($chunk) use ($client) {
            $client->messageBuffer .= $chunk;
            $client->parseBuffer();
        });

        $reader->start();
    }

    public function end()
    {
        return $this->reader->end();
    }

    /**
     * parse incomming message buffer
     */
    public function parseBuffer()
    {
        if (!$this->expect100Reached) {
            if (($expectPos = strpos($this->messageBuffer, "Expect: 100-continue\r\n")) !== false) {
                $this->messageBuffer = substr($expectPos + 22, $expectPos);
                $this->expect100Reached = true;
            }
        }

        if (!$this->expect100Reached && strlen($this->messageBuffer) > 1000 ) {
            return $this->abort();
        }

        //preg_match("/[0-9"]", 3ls

        echo $this->messageBuffer;
    }

    public function read()
    {
        return $this->reader->read();
    }

    public function captureBufferRead()
    {
       while (null !== $chunk = yield $this->read()) {
           echo ">>>>>>>>>{$chunk}<<<<<<<<<<<<<";
       }

       return $chunk;

        \Amp\asyncCall(function (\Amp\Socket\ServerSocket $socket) {
            echo "Async fired";
            while (null !== $chunk = yield $socket->read()) {
                echo ">>>>>>>>>{$chunk}<<<<<<<<<<<<<";
                //$this->messageBuffer .= $chunk;
                //$this->parseBuffer();
            }

            return $chunk;
        }, $this->reader);



       return;

        return;
/*
        \Amp\asyncCall(function (\Amp\Socket\ServerSocket $socket) {
            while (null !== $chunk = yield $socket->read()) {
                echo ">>>>>>>>>{$chunk}<<<<<<<<<<<<<";
                //$this->messageBuffer .= $chunk;
                //$this->parseBuffer();
            }

            echo "ttttttt{$chunk}ttttttt";
        }, $this->reader);
 */
        //echo "tsetiiiiiiiiiiiiiiiii";

        /*
        echo "test";
        while (null !== $chunk = yield $this->reader->read()) {
            echo ">>>>>>>>>{$chunk}<<<<<<<<<<<<<";
        }

        echo "test";
        /*

        return;
        while (null !== $chunk = yield $socket->read()) {
            echo "############{$chunk}############";
            return;
            $this->messageBuffer .= $chunk;
            $this->parseBuffer();
        }

        return;
        // first asyncCall
        \Amp\asyncCall(function (\Amp\Socket\ServerSocket $socket) {

            while (null !== $chunk = yield $socket->read()) {
                echo $chunk;
                exit;
                $this->messageBuffer .= $chunk;
                $this->parseBuffer();
            }

        }, $this->reader);
        */
    }

    public function abort()
    {
        $body = "Thank you for using ".config('app.name');
        $bodyLength = \strlen($body);
        $this->reader->end("HTTP/1.1 200 OK\r\nConnection: close\r\nContent-Length: {$bodyLength}\r\n\r\n{$body}");
    }

}
