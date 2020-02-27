<?php
namespace App\Server;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class Websocket  implements MessageComponentInterface
{
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

        $conn->wsLoopTimer = $this->loop->addPeriodicTimer(1, function() use ($conn, $clientID) {
            if ($clientID == "testing") {
                //$conn->send('test');
            }
        });
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($from == $client) {
                echo "Sending back: ".$msg."\n";
                $client->send($msg);
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
