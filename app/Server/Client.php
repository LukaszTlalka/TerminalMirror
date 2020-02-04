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
     * @var extracted from the header of the message
     */
    private $userAuth = null;

    /**
     * @var do we need to initialize storage
     */
     private $storage = null;

    /**
     * @var \App\Server\ChunkReader - data that delivers chunks / usually socket
     */
    private $reader = null;

    /**
     * @param $reader - reader provides the data
     */
    public function __construct(\App\Server\ChunkReader $reader)
    {
        $this->reader = $reader;

        $client = $this;

        $reader->registerReadListener(function($chunk) use ($client) {
            $client->messageBuffer .= $chunk;

            //file_put_contents("/tmp/chunks-dd", $chunk, FILE_APPEND);

            if ( count($chunks = $client->parseBuffer()) > 0 ) {
                $this->logBufferChunk($chunks);
            }
        });

        $reader->start();
    }

    /**
     * store chunk in the input file
     */
    public function logBufferChunk($chunks)
    {
        list($userID, $pass) = explode(":", $this->userAuth);

        if (!preg_match('/^[a-f0-9]{32}$/', $userID)) {
            $this->end();
        }

        if (!$this->storage) {
            try {
                $this->storage = new Storage($userID, false);
                
                // check if input has been initialized
                // so that we don't have two users pushing data to the same input
                if (trim($this->storage->inputGet()) != "initialized") {
                    throw new \Exception("Already taken or input not initialized.");
                }

                $this->storage->inputAppend("curl-started");
            } catch (\Exception $e) {

                if (config('app.env') != 'production') {
                    throw $e;
                }

                $this->end();
            }
        }

        foreach ($chunks as $chunk) {
            $chunkString = json_encode([
                'v' => 1,
                'chunk' => base64_encode($chunk),
                'microtime' => microtime(true)
            ]);

            $this->storage->inputAppend($chunkString);
        }
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
            $expect100Continue = "Expect: 100-continue\r\n";
            if (($expectPos = strpos($this->messageBuffer, $expect100Continue)) !== false) {

                $header = substr($this->messageBuffer, 0, $expectPos + strlen($expect100Continue) + 2);

                if (!preg_match("/Authorization: Basic (.*?)\r\n/", $header, $clientInfo)) {
                    $this->abort();
                    return [];
                }

                $this->userAuth = base64_decode($clientInfo[1]);

                $this->messageBuffer = substr($this->messageBuffer, $expectPos + strlen($expect100Continue) + 2);
                $this->expect100Reached = true;

                return $this->parseBuffer();
            }

            return [];
        }

        if (!$this->expect100Reached && strlen($this->messageBuffer) > 1000 ) {
            $this->abort();
            return [];
        }

        if (preg_match("/^([0-9a-f]*)\r\n/i", $this->messageBuffer, $res)) {
            $chunkSizeHex = $res[1];
            $chunkSize = hexdec($chunkSizeHex);
            $chunk = substr($this->messageBuffer, strlen($chunkSizeHex) + 2, $chunkSize);

            if (strlen($chunk) == $chunkSize) {
                $this->messageBuffer = substr($this->messageBuffer, $chunkSize + strlen($chunkSizeHex) + 4);

                $out = [ $chunk ];

                foreach ((array)$this->parseBuffer() as $c) {
                    $out[] = $c;
                }

                return $out;

                //dd(array_merge([$chunk], (array)$this->parseBuffer() ));
            }
        }


        return [];
    }

    public static function getRandomID()
    {
    
    }
}
