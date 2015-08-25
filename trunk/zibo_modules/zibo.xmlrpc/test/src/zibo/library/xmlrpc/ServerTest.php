<?php

namespace zibo\library\xmlrpc;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\config\io\ini\IniConfigIO;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;
use zibo\library\xmlrpc\exception\XmlRpcException;
use zibo\library\Callback;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ServerTest extends BaseTestCase {

    private $server;
    private $service;
    private $serviceName;
    private $callback;
    private $resultType;
    private $parameterTypes;

    protected function setUp() {
        $path = new File(__DIR__ . '/../../../../');

        $this->setUpApplication($path->getPath());

        try {
            $browser = new GenericBrowser(new File(getcwd()));
            $configIO = new IniConfigIO(Environment::getInstance(), $browser);

            Zibo::getInstance($browser, $configIO);
        } catch (ZiboException $e) {
        }

        $this->server = new Server();

        $this->service = new SumService();
        $this->serviceName = 'test.sum';
        $this->callback = array($this->service, 'sum');
        $this->resultType = 'int';
        $this->parameterTypes = array('int', 'int');
    }

    protected function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testRegisterService() {
        $expected = array(
            'callback' => new Callback($this->callback),
            'return' => $this->resultType,
            'parameters' => $this->parameterTypes,
            'description' => null,
        );

        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);

        $services = Reflection::getProperty($this->server, 'services');
        $this->assertNotNull($services, 'services is null');
        $this->assertTrue(is_array($services), 'services is no array');
        $this->assertEquals(1, count($services), 'services has unexpected count');
        $this->assertTrue(isset($services[$this->serviceName]), 'registered service is not set');
        $this->assertEquals($expected, $services[$this->serviceName]);
    }

    public function testRegisterServiceWithOneParameter() {
        $expected = array(
            'callback' => new Callback($this->callback),
            'return' => $this->resultType,
            'parameters' => array('int'),
            'description' => null,
        );

        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, 'int');

        $services = Reflection::getProperty($this->server, 'services');
        $this->assertNotNull($services, 'services is null');
        $this->assertTrue(is_array($services), 'services is no array');
        $this->assertEquals(1, count($services), 'services has unexpected count');
        $this->assertTrue(isset($services[$this->serviceName]), 'registered service is not set');
        $this->assertEquals($expected, $services[$this->serviceName]);
    }

    public function testRegisterServiceWithoutParameters() {
        $expected = array(
            'callback' => new Callback($this->callback),
            'return' => $this->resultType,
            'parameters' => array(),
            'description' => null,
        );

        $this->server->registerService($this->serviceName, $this->callback, $this->resultType);

        $services = Reflection::getProperty($this->server, 'services');
        $this->assertNotNull($services, 'services is null');
        $this->assertTrue(is_array($services), 'services is no array');
        $this->assertEquals(1, count($services), 'services has unexpected count');
        $this->assertTrue(isset($services[$this->serviceName]), 'registered service is not set');
        $this->assertEquals($expected, $services[$this->serviceName]);
    }

    public function testRegisterServiceThrowsExceptionWhenNameIsEmpty() {
        try {
            $this->server->registerService('', $this->callback, $this->resultType, $this->parameterTypes);
        } catch (XmlRpcException $e) {
            return;
        }
        $this->fail();
    }

    public function testRegisterServiceThrowsExceptionWhenNameAlreadyExists() {
        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);

        try {
            $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);
        } catch (XmlRpcException $e) {
            return;
        }
        $this->fail();
    }

    public function testRegisterServiceThrowsExceptionWhenEmptyResultTypeProvided() {
        try {
            $this->server->registerService($this->serviceName, $this->callback, '', $this->parameterTypes);
        } catch (XmlRpcException $e) {
            return;
        }
        $this->fail();
    }

    public function testRegisterServiceThrowsExceptionWhenInvalidResultTypeProvided() {
        try {
            $this->server->registerService($this->serviceName, $this->callback, 'invalid', $this->parameterTypes);
        } catch (XmlRpcException $e) {
            return;
        }
        $this->fail();
    }

    public function testRegisterServiceThrowsExceptionWhenInvalidParameterTypeProvided() {
        try {
            $this->server->registerService($this->serviceName, $this->callback, $this->resultType, array('int', 'invalid'));
        } catch (XmlRpcException $e) {
            return;
        }
        $this->fail();
    }

    public function testService() {
        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);

        $headers =
            '<?xml version="1.0"?>' . "\n" .
            '<methodCall>' .
            '    <methodName>' . $this->serviceName . '</methodName>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '            <value><int>31</int></value>' . "\n" .
            '        </param>' . "\n" .
            '        <param>' . "\n" .
            '            <value><int>27</int></value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodCall>';

        $response = $this->server->service($headers);
        $this->assertNotNull($response, 'response is null');
        $this->assertTrue($response instanceof Response, 'response is not an instance of Response');
        $this->assertEquals(58, $response->getValue());
    }

    public function testServiceGivesErrorResponseWhenServiceNameIsUnknown() {
        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);

        $headers =
            '<?xml version="1.0"?>' . "\n" .
            '<methodCall>' .
            '    <methodName>service.unknown</methodName>' . "\n" .
            '</methodCall>';

        $response = $this->server->service($headers);
        $this->assertNotNull($response, 'response is null');
        $this->assertTrue($response instanceof Response, 'response is not an instance of Response');
        $this->assertEquals(1, $response->getErrorCode());
    }

    public function testServiceGivesErrorResponseWhenParametersAreIncorrect() {
        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);

        $headers =
            '<?xml version="1.0"?>' . "\n" .
            '<methodCall>' .
            '    <methodName>' . $this->serviceName . '</methodName>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '            <value><int>31</int></value>' . "\n" .
            '        </param>' . "\n" .
            '        <param>' . "\n" .
            '            <value><string>test</string></value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodCall>';

        $response = $this->server->service($headers);
        $this->assertNotNull($response, 'response is null');
        $this->assertTrue($response instanceof Response, 'response is not an instance of XmlRpcResponse');

        $this->assertEquals(3, $response->getErrorCode());
    }

    public function testServiceGivesErrorResponseWhenCountParametersAreIncorrect() {
        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);

        $headers =
            '<?xml version="1.0"?>' . "\n" .
            '<methodCall>' .
            '    <methodName>' . $this->serviceName . '</methodName>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '            <value><int>31</int></value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodCall>';

        $response = $this->server->service($headers);
        $this->assertNotNull($response, 'response is null');
        $this->assertTrue($response instanceof Response, 'response is not an instance of Response');
        $this->assertEquals(3, $response->getErrorCode());
    }

    public function testServiceGivesErrorResponseWhenRequestXmlIsInvalid() {
        $this->server->registerService($this->serviceName, $this->callback, $this->resultType, $this->parameterTypes);

        $headers =
            '<?xml version="1.0"?>' . "\n" .
            '<methodCall>' .
            '    <methodName>' . $this->serviceName . '</methodName>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '            <value><int>31</int></value>' . "\n" .
            '        </pm>' . "\n" .
            '        <param>' . "\n" .
            '            <value><int>27</int></value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodCall>';

        $response = $this->server->service($headers);
        $this->assertNotNull($response, 'response is null');
        $this->assertTrue($response instanceof Response, 'response is not an instance of Response');
        $this->assertTrue($response->getErrorCode() >= 100);
    }

}

class SumService {

    public function sum($a, $b) {
        return $a + $b;
    }

}