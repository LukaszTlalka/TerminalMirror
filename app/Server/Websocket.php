<?php
namespace App\Server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class Websocket  implements MessageComponentInterface
{
    const checkChangeTime = 0.05;

    protected $clients;

    private $loop;

    public function __construct(\React\EventLoop\StreamSelectLoop $loop) {
        $this->clients = new \SplObjectStorage;

        $this->loop = $loop;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);

        list($clientParam, $clientID) = explode("=", $conn->httpRequest->getUri()->getQuery());

        if ($clientParam != "client") {
            return;
        }

        $conn->wsStorage = new Storage($clientID, false);
        $conn->wsStorageHash = [
            Storage::FILE_TYPE_INPUT => null,
            Storage::FILE_TYPE_OUTPUT => null,
        ];

        $conn->wsLoopTimer = $this->loop->addPeriodicTimer(1, function() use ($conn, $clientID) {
            foreach ($conn->wsStorageHash as $inputType => $hash) {
                if ($conn->wsStorage->getModHash($inputType) != $hash) {

                    $contents = $conn->wsStorage->get($inputType);

                    $conn->wsStorageHash[ $inputType ] = $hash;

                    $conn->send($contents);
                }
            }

        });
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($from == $client) {

                if ($client->wsStorage) {
                    $chunkString = json_encode([
                        'v' => 1,
                        'chunk' => base64_encode($msg),
                        'microtime' => microtime(true)
                    ]);

                    $client->wsStorage->append(Storage::FILE_TYPE_INPUT, $chunkString);
                }

                //echo "Sending back: ".$msg."\n";
                //$client->send($msg);
            }
        }
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
