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

            // $list = $conn->wsStorage->get('outputClient', $lastId);

            // if ($content = array_pop($list)) {
            //     $lastId = 0;
            //     $content['id'];
            // };

            $this->logger->info('Starting message loop for ws client: ' . $conn->clientID . ' starting from id: '. $lastId);


            $conn->isActiveUser = true;
            $conn->lastReadStamp = time();
            $conn->wsLoopTimer = null;

            $conn->wsAdjustLoopTimer = $this->loop->addPeriodicTimer(\App\Server\Client::LOOP_ADJUST_INTERVAL/1000, function() use (&$conn, &$lastId, $self) {

                if (isset($conn->wsLoopTimer)) {
                    $self->loop->cancelTimer($conn->wsLoopTimer);
                }

                $baseSpeed = \App\Server\Client::CHECK_FOR_MSGS_ACTIVE_USER;

                $prevUserStatus = $conn->isActiveUser;

                if (time() - $conn->lastReadStamp >= \App\Server\Client::CHECK_FOR_MSGS_ACTIVE_USER) {
                    $conn->isActiveUser = false;
                    $baseSpeed = \App\Server\Client::CHECK_FOR_MSGS_INACTIVE_USER;
                } else {
                    $conn->isActiveUser = true;
                    $baseSpeed = \App\Server\Client::CHECK_FOR_MSGS_ACTIVE_USER;
                }

                if ($prevUserStatus != $conn->isActiveUser) {
                    $self->logger->info("Changing loop speed for ws client: " . $conn->clientID . "  to: " .($conn->isActiveUser ? 'active' : 'inactive'));
                }

                $conn->wsLoopTimer = $self->loop->addPeriodicTimer($baseSpeed/1000, function() use (&$conn, &$lastId, $self) {
                    $clientID = $conn->clientID;

                    foreach ($conn->wsStorage->get('outputClient', $lastId + 1) as $lastId => $content) {
                        $conn->send(json_encode(['k' => $content['data']]));
                        $self->logger->info('Updating lastId for ws client: ' . $conn->clientID . ' id: '. $lastId);

                        $conn->lastReadStamp = time();
                    };
                });
            });

        } catch (\Exception $e) {
            $this->logger->error('Error: ' . $e->getMessage());
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $client = $from;

        if ($client->wsStorage) {

            $inputObj = json_decode($msg);

            $client->wsStorage->append('inputClient', $inputObj->m);

            $this->logger->info('Message received from ws client: ' . $client->clientID . ' message: '. $inputObj->m);
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
