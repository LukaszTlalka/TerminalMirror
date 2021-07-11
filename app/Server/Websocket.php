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

        if (!preg_match('/^[a-f0-9]{32}$/', $clientID)) {
            $this->logger->error('Client tried to open connection with: ' . $conn->httpRequest->getUri()->getQuery());
            $conn->close();
            return;
        }


        $conn->clientID = $clientID;

        $this->logger->info('Connection opened, clientID: ' . $clientID);

        try {
            $conn->wsStorage = new Storage($clientID, false);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $conn->close();
            return;
        }
        
        $this->startWsLoop($conn);
    }

    public function startWsLoop($conn)
    {
        try {
            $self = $this;

            $lastId = 0;

            $list = $conn->wsStorage->get('outputClient', $lastId);

            if ($content = array_pop($list)) {
                $lastId = $content['id'];
            };

            $this->logger->info('Starting message loop for ws client: ' . $conn->clientID . ' starting from id: '. $lastId);

            $conn->wsLoopTimer = $this->loop->addPeriodicTimer(0.01, function() use ($conn, &$lastId, $self) {

                $self->logger->info('wsLoopTimer ' . $conn->clientID . ' id: '. $lastId);

                $clientID = $conn->clientID;

                foreach ($conn->wsStorage->get('outputClient', $lastId + 1) as $lastId => $content) {
                    $conn->send(json_encode(['k' => $content['data']]));
                    $self->logger->info('Updating lastId for ws client: ' . $conn->clientID . ' id: '. $lastId);
                };

            });
        } catch (\Exception $e) {
            $this->logger->error('Error: ' . $e->getMessage());
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $client = $from;

        if ($client->wsStorage) {

            $keyObj = json_decode($msg);

            $client->wsStorage->append('inputClient', $keyObj->k);

            $this->logger->info('Message received from ws client: ' . $client->clientID . ' message: '. $keyObj->k);
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
