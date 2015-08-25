<?php

namespace zibo\library\network;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ConnectionTest extends BaseTestCase {

    private $host;

    public function setUp() {
        $this->host = 'localhost';
    }

    public function testConnect() {
        $connection = new Connection($this->host);

        $this->assertNull(Reflection::getProperty($connection, 'socket'));

        $connection->connect();

        $this->assertNotNull(Reflection::getProperty($connection, 'socket'));

        $connection->disconnect();

        $this->assertNull(Reflection::getProperty($connection, 'socket'));
    }

    public function testSendAndReceive() {
        $requestString = "HEAD / HTTP/1.0\r\n\r\n";

        $connection = new Connection($this->host);
        $connection->connect();
        $connection->sendRequest($requestString);
        $responseString = $connection->receiveResponse();

        $this->assertNotNull($responseString);

        $responseLines = explode("\r\n", $responseString);
        $responseStatus = array_shift($responseLines);

        $this->assertEquals('HTTP/1.1 200 OK', $responseStatus);
    }

}