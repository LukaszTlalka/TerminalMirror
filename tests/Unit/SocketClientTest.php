<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \Amp\Socket\ServerSocket;
use App\SocketClient;


class SocketClientTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);


        $reader = new \App\Server\ChunkReader\FakePush();
        $client = new \App\Server\Client($reader);

        $msg = "UE9TVCAvIEhUVFAvMS4xDQpIb3N0OiBsb2NhbGhvc3Q6MzAwNQ0KVXNlci1BZ2VudDogY3VybC83LjU4LjANCkFjY2VwdDogK" .
            "i8qDQpUcmFuc2Zlci1FbmNvZGluZzogY2h1bmtlZA0KQ29udGVudC1UeXBlOiBhcHBsaWNhdGlvbi9qc29uDQpFeHBlY3Q6IDEwM" .
            "C1jb250aW51ZQ0KDQpkMQ0KUmVhZG1lLm1kCmFwcAphcnRpc2FuCmJvb3RzdHJhcApjb21wb3Nlci5qc29uCmNvbXBvc2VyLmxvY" .
            "2sKY29uZmlnCmRhdGFiYXNlCm5vZGVfbW9kdWxlcwpwYWNrYWdlLWxvY2suanNvbgpwYWNrYWdlLmpzb24KcGhwdW5pdC54bWwKc" .
            "HVibGljCnJlc291cmNlcwpyb3V0ZXMKc2VydmVyLnBocApzdG9yYWdlCnRlc3RzCnRvb2xzCnZlbmRvcgp3ZWJwYWNrLm1peC5qc" .
            "woNCjYNCmx1a2FzCg0KMmUNCi9ob21lL2x1a2FzL3B1YmxpYy9MdWthc3pUbGFsa2EvY29uc29sZS1zaGFyZQoNCjANCg0K";

        foreach (str_split( base64_decode($msg), 100) as $chunk) {
            $reader->pushMessage($chunk);
        }

        return;




        exit;



        \Amp\Loop::run(function(){

            // create temp file handle
            $tempHandle = tmpfile();

            $uri = stream_get_meta_data($tempHandle)['uri'];
            $socketHandle = fopen($uri, "r+");

            $serverSocket = new ServerSocket($socketHandle);
            $socketClient = new SocketClient($serverSocket);

            \Amp\Loop::delay(10, function() use ($tempHandle) {
                fwrite($tempHandle, 'Writing to socket');
            });


            // \Amp\Loop::delay(2000, function() use($serverSocket) {
            //     while (null !== $chunk = yield $serverSocket->read()) {
            //         echo ">>>>>>>>>{$chunk}<<<<<<<<<<<<<";
            //     }
            // });

            \Amp\Loop::repeat(100, function() use ($socketClient) {
                echo "test";
            });

            \Amp\Loop::delay(2000, function() {
                echo "!!!!!!! finished !!!!!!!";
            });

            /*
                \Amp\Loop::delay(2000, function() {
                    echo "!!!!!!! finished !!!!!!!";
                });
             */
        });





        return;

        die('in');




        exit;


        \Amp\Loop::run(function () {
            echo "Inside loop";
            // Impose a 5-second timeout if nothing is input
            \Amp\Loop::delay(5000, function() {
                echo "Delay";
            });
        });

        exit;
        $handle = fopen('/tmp/test-fasdfa', 'r+');
        \Amp\Loop::run(function () {

            $serverSocket = new ServerSocket($handle);

            while($data = $serverSocket->read()) {
                echo $data;
            }
        });

        exit;

        \Amp\Loop::run(function () use ($ServerSocket) {
            while($data = $serverSocket->read()) {
                echo $data;
            }
        });
        exit;

        exit;
        \Amp\Loop::run(function () {
            $handle = fopen('/tmp/test-fasdfa', 'w+');
            $serverSocket = new ServerSocket($handle);

            $socketClient = new SocketClient($serverSocket);

            while(!sleep(1)) {
                fwrite($handle, "test");
                $result = \Amp\Promise\wait($socketClient->read());

                echo "result" . $result;
            
            }
            exit;
        });


        $this->assertTrue(true);
    }
}
