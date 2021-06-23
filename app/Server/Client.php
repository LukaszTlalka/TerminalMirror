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
     * @var string  inputClient | outputClient
     */
    private $clientType = '';

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
    public function __construct(\App\Server\ChunkReader $reader, $logger)
    {
        $this->reader = $reader;
        $this->logger = $logger;

        $client = $this;

        $reader->registerReadListener(function($chunk) use ($client, $logger) {
            $logger->info('Buffer message received: ' . substr(str_replace(["\n", "\r"], ["\\n", "\\r"], $chunk), 0, 50));
            $client->messageBuffer .= $chunk;

            //file_put_contents("/tmp/chunks-dd", $chunk, FILE_APPEND);

            if ( count($chunks = $client->parseBuffer()) > 0 ) {
                if ($this->clientType == 'outputClient') {
                    $this->logBufferChunk($chunks);
                }
            }
        });

        $reader->start();
    }

    /**
     * store chunk in the input file
     */
    public function logBufferChunk($chunks)
    {
        $userID = $this->userAuth;

        if (!preg_match('/^[a-f0-9]{32}$/', $userID)) {
            $this->end();
        }

        if (!$this->storage) {
            try {
                $this->storage = new Storage($userID, false);

                // check if input has been initialized
                // so that we don't have two users pushing data to the same input
                
                $log = $this->storage->get('log');
                if (count($log) != 1) {
                    // TODO: uncomment
                    //throw new \Exception("Already taken or input not initialized.");
                }

                $this->storage->append('log', "curl-started");
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

            $this->logger->info('Appending storage for FILE_TYPE_OUTPUT');
            $this->storage->append('FILE_TYPE_OUTPUT', $chunkString);
        }

    }

    public function abort()
    {
        return $this->end();
    }
    public function end()
    {
        $this->logger->info('Client disconnected: ' . ($this->userAuth ?? ''));
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

                if (!preg_match("/Authorization: Bearer (.*?)\r\n/", $header, $clientInfo)) {
                    $this->abort();
                    return [];
                }

                $this->userAuth = $clientInfo[1];

                if (!preg_match("#POST /(.)#i", $header, $uriInfo)) {
                    $this->abort();
                    return [];
                }

                $this->clientType = $uriInfo[1] == 'o' ? 'outputClient' : 'inputClient';

                $this->logger->info('Client connected: ' . $this->userAuth . " type: " . $this->clientType);

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
