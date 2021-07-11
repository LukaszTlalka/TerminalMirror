<?php

namespace App\Server;

class Client
{
    /**
     * @var incomming socket data  buffer
     */
    private $messageBuffer = "";

    /**
     * @var Wether end of header has been reached ("\r\n\r\n")
     */
    private $headerSent = false;

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

            if ( count($chunks = $client->parseBuffer()) > 0 ) {
                if ($this->clientType == 'outputClient') {
                    $this->logBufferChunk($chunks);
                }
            }
        });

        $reader->start();
    }

    public function clientRecognized()
    {
        if (!$this->clientType == 'inputClient') {
            return;
        }

        $this->logger->info('Sending response headers');
        $this->reader->write("HTTP/1.1 200 OK\r\n\r\n");

        $self = $this;

        $self->inputClientLastId = 0;
        \Amp\Loop::repeat(10, function($repeaterID) use ($self) {
            foreach ($self->storage->get('inputClient', $self->inputClientLastId) as $id => $input) {
                $self->inputClientLastId = $id+1;
                $self->reader->write($input['data']);
                $self->logger->info('Writing: ' . substr(str_replace(["\n", "\r"], ["\\n", "\\r"], $input['data']), 0, 50));
            };
        });
    }

    /**
     * store chunk in the input file
     */
    public function logBufferChunk($chunks)
    {
        foreach ($chunks as $chunk) {
            $this->logger->info('Appending storage for outputClient');
            $this->storage->append('outputClient', $chunk);
        }
    }

    /**
     * parse incomming message buffer
     */
    public function parseBuffer()
    {
        if (!$this->userAuth && preg_match("/Authorization: Bearer ([a-f0-9]{32})\r\n/i", $this->messageBuffer, $clientInfo)) {

            $this->userAuth = $clientInfo[1];

            if (!preg_match("#(POST|GET) /(.)#i", $this->messageBuffer, $uriInfo)) {
                $this->abort();
                return [];
            }

            $this->clientType = $uriInfo[2] == 'o' ? 'outputClient' : 'inputClient';

            $this->logger->info('Client connected: ' . $this->userAuth . " type: " . $this->clientType);

            if (!$this->storage) {
                try {
                    $this->storage = new Storage($this->userAuth, false);

                    // check if input has been initialized
                    // so that we don't have two users pushing data to the same input


                    $msgLog = 'curl-started:'.$this->clientType;

                    if (config('app.env') == 'production') {
                        foreach ($this->storage->get('log') as $k => $msg) {
                            if ($k > 3) {
                                break;
                            }

                            if ($msg['data'] == $msgLog) {
                                throw new \Exception("Already taken or input not initialized.");
                            }
                        }
                    }

                    $this->storage->append('log', $msgLog);
                } catch (\Exception $e) {

                    if (config('app.env') != 'production') {
                        throw $e;
                    }

                    $this->logger->error($e->getMessage());

                    $this->end();
                }
            }

            $this->clientRecognized();
        }

        // for input client we don't care about what we get from the
        if ($this->clientType == 'inputClient') {
            $this->messageBuffer = "";
            return [];
        }

        if (!$this->headerSent) {
            $headerEnd = "\r\n\r\n";
            if (($expectPos = strpos($this->messageBuffer, $headerEnd)) !== false) {
                $this->logger->info('Full header found');

                $this->messageBuffer = substr($this->messageBuffer, $expectPos + strlen($headerEnd));
                $this->headerSent = true;

                return $this->parseBuffer();
            } else {
                $this->logger->error('Full header not found');
            }

            return [];
        }


        if (!$this->headerSent && strlen($this->messageBuffer) > 10000 ) {
            $this->abort();
            return [];
        }

        if ($this->headerSent && preg_match("/^([0-9a-f]*)\r\n/i", $this->messageBuffer, $res)) {
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
            }
        }


        return [];
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
}
