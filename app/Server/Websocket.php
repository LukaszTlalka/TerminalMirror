<?php
namespace App\Server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class Websocket  implements MessageComponentInterface
{
    const checkChangeTime = 0.05;

    protected $clients;

    private $loop;

    public function __construct(\React\EventLoop\StreamSelectLoop $loop, $logger) {
        $logger->info('Websocket initialized');

        $this->logger = $logger;
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
    }

    public function onOpen(ConnectionInterface $conn) {

        $this->clients->attach($conn);

        list($clientParam, $clientID) = explode("=", $conn->httpRequest->getUri()->getQuery());

        if (!in_array($clientParam, ["inputClient", "outputClient"]) || !preg_match('/^[a-f0-9]{32}$/', $clientID)) {
            $this->logger->error('Client tried to open connection with: ' . $conn->httpRequest->getUri()->getQuery());
            $conn->close();
            return;
        }


        $conn->clientParam = $clientParam;
        $conn->clientID = $clientID;

        $this->logger->info('Connection opened, type: ' . $clientParam . " clientID: " . $clientID);

        try {
            $conn->wsStorage = new Storage($clientID, false);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $conn->close();
            return;
        }

        if ($clientParam == 'inputClient') {
            $this->inputClientOnOpen($conn);
        }
    }

    public function inputClientOnOpen($conn)
    {
        $conn->wsStorageHash = null;

        $conn->wsLoopTimer = $this->loop->addPeriodicTimer(1, function() use ($conn) {
            $clientID = $conn->clientID;

            if ($conn->wsStorageHash != $newHash = $conn->wsStorage->getModHash(Storage::FILE_TYPE_INPUT)) {
                $contents = $conn->wsStorage->get(Storage::FILE_TYPE_INPUT);

                $conn->wsStorageHash = $newHash;

                $conn->send($contents);
            }
        });
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $client = $from;

        if ($client->wsStorage) {

            if ($client->clientParam != 'outputClient') {
                return $this->logger->error("We've received a message from the inputClient! Input client should only receive messages that are then forwarded to the bash command.");
            }

            $chunkString = json_encode([
                'v' => 1,
                'chunk' => base64_encode($msg),
                'microtime' => microtime(true)
            ]);

            $client->wsStorage->append(Storage::FILE_TYPE_OUTPUT, $chunkString);

            $this->logger->info('Message received from outputClient: ' . $client->clientID . ' message: '. $chunkString);
        }

        //echo "Sending back: ".$msg."\n";
        //$client->send($msg);
        //    }
        //}
    }

    public function onClose(ConnectionInterface $conn) {

        if (isset($conn->wsLoopTimer)) {
            $this->loop->cancelTimer($conn->wsLoopTimer);
        }

        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

}
