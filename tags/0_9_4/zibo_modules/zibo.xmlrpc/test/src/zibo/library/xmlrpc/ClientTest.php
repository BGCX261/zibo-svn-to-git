<?php

namespace zibo\library\xmlrpc;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\config\io\ini\IniConfigIO;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ClientTest extends BaseTestCase {

    const PHPXMLRPC_TEST_SERVER = 'http://phpxmlrpc.sourceforge.net/server.php';
    const PHPXMLRPC_TEST_METHOD = 'examples.stringecho';

    protected function setUp() {
        $path = new File(__DIR__ . '/../../../../');

        $this->setUpApplication($path->getPath());

        try {
            $browser = new GenericBrowser(new File(getcwd()));
            $configIO = new IniConfigIO(Environment::getInstance(), $browser);

            Zibo::getInstance($browser, $configIO);
        } catch (ZiboException $e) {
        }
    }

    public function testConstruct() {
        $client = new Client('http://time.xmlrpc.com:80/RPC2');

        $host = Reflection::getProperty($client, 'host');
        $this->assertEquals('time.xmlrpc.com', $host, 'host is not the expected host');

        $port = Reflection::getProperty($client, 'port');
        $this->assertEquals(80, $port, 'port is not the expected port');

        $path = Reflection::getProperty($client, 'path');
        $this->assertEquals('/RPC2', $path, 'path is not the expected path');
    }

    /**
     * @expectedException zibo\library\xmlrpc\exception\XmlRpcException
     */
    public function testConstructThrowsExceptionWhenUrlIsEmpty() {
        $client = new Client('');
    }

    /**
     * @expectedException zibo\library\xmlrpc\exception\XmlRpcException
     */
    public function testConstructThrowsExceptionWhenInvalidUrlProvided() {
        $client = new Client('invalid url');
    }

    /**
     * @expectedException zibo\library\xmlrpc\exception\ConnectionException
     **/
    public function testConstructThrowsConnectionExceptionWhenUnableToConnect() {
        $client = new Client('http://unexisting.example.com');
        $request = new Request('system.listMethods');

        $response = $client->invoke($request);
    }

    /**
     * @expectedException zibo\library\xmlrpc\exception\XmlRpcException
     */
    public function testConstructThrowsExceptionOnUnsupportedHttpsUrl() {
        $client = new Client('https://example.com');
    }

    public function testInvokePhpxmlrpcTestServerExampleEchoesString() {
        $client = new Client(self::PHPXMLRPC_TEST_SERVER);
        $request = new Request(self::PHPXMLRPC_TEST_METHOD);
        $string = 'testing 1 2 3';
        $request->addParameter($string);

        $response = $client->invoke($request);
        $this->assertSame($string, $response->getValue());
    }

}