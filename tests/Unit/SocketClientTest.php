<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \Amp\Socket\ServerSocket;
use App\SocketClient;


class SocketClientTest extends TestCase
{
    public function testFileMod()
    {
        $storage = new \App\Server\Storage("test", true);
        /*
            $storage->append('input', 'test');
            $storage->append('input', 'test2');
            $storage->append('input', 'test3');
            print_r($storage->get('input',3));
        */

        $lastModHash = $storage->append('FILE_TYPE_INPUT', "initialized");
        $this->assertTrue($storage->getModHash('FILE_TYPE_INPUT') == $lastModHash);

        $storage->append('FILE_TYPE_INPUT', "modified");

        $this->assertFalse($storage->getModHash('FILE_TYPE_INPUT') == $lastModHash);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testSocketMessage()
    {
        $reference = "01adcd4ea37412bf0af84695611622a7";

        $storage = new \App\Server\Storage($reference, true);
        $storage->append('FILE_TYPE_INPUT', "initialized");

        $reader = new \App\Server\ChunkReader\FakePush();
        $client = new \App\Server\Client($reader);

        $msg = "UE9TVCAvIEhUVFAvMS4xDQpIb3N0OiBsb2NhbGhvc3Q6MzAwNQ0KVXNlci1BZ2VudDogY3VybC83LjU4LjANCkFjY2VwdDogKi8q" .
            "DQpUcmFuc2Zlci1FbmNvZGluZzogY2h1bmtlZA0KQXV0aG9yaXphdGlvbjogQmFzaWMgTURGaFpHTmtOR1ZoTXpjME1USmlaakJo" .
            "WmpnME5qazFOakV4TmpJeVlUYzYNCkV4cGVjdDogMTAwLWNvbnRpbnVlDQoNCmRjDQokClJlYWRtZS5tZAphcHAKYXJ0aXNhbgpi" .
            "b290c3RyYXAKY29tcG9zZXIuanNvbgpjb21wb3Nlci5sb2NrCmNvbmZpZwpkYXRhYmFzZQpub2RlX21vZHVsZXMKcGFja2FnZS1s" .
            "b2NrLmpzb24KcGFja2FnZS5qc29uCnBocHVuaXQueG1sCnB1YmxpYwpyZXNvdXJjZXMKcm91dGVzCnNlcnZlci5waHAKc3RvcmFn" .
            "ZQp0ZXN0LnBocAp0ZXN0cwp0b29scwp2ZW5kb3IKd2VicGFjay5taXguanMKDQo2DQpsdWthcwoNCjJlDQovaG9tZS9sdWthcy9w" .
            "dWJsaWMvTHVrYXN6VGxhbGthL2NvbnNvbGUtc2hhcmUKDQowDQoNCg==";

        foreach (str_split( base64_decode($msg), 100000) as $chunk) {
            $reader->pushMessage($chunk);
        }

        $data = explode("\n", $storage->get('FILE_TYPE_INPUT'));

        $this->assertEquals($data[0], "initialized");
        $this->assertEquals($data[1], "curl-started");
    }

}
