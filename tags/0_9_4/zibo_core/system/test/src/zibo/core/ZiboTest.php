<?php

namespace zibo\core;

use zibo\library\config\Config;

use zibo\ZiboException;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ZiboTest extends BaseTestCase {

    protected function tearDown() {
        try {
            Reflection::setProperty(Zibo::getInstance(), 'instance', null);
        } catch (ZiboException $e) {

        }
    }

    private function getBrowserMock() {
        return $this->getMock('zibo\\library\\filesystem\\browser\\Browser');
    }

    private function getConfigIOMock() {
        return $this->getMock('zibo\\library\\config\\io\\ConfigIO');
    }

    public function testGetInstanceWithConfigIO() {
        $this->assertTrue(Zibo::getInstance($this->getBrowserMock(), $this->getConfigIOMock()) instanceof Zibo);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGetInstanceWithParametersThrowsExceptionWhenInstanceHasBeenCreated() {
        $browserMock = $this->getBrowserMock();
        $ioMock = $this->getConfigIOMock();
        Zibo::getInstance($browserMock, $ioMock);
        Zibo::getInstance($browserMock, $ioMock);
    }
    /**
     * @expectedException zibo\ZiboException
     */
    public function testGetInstanceWithoutBrowserAndConfigIOThrowsExceptionWhenInstanceHasNotBeenCreated() {
        Zibo::getInstance();
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRunEventThrowsExceptionWhenSystemEventNameIsPassed() {
        $zibo = Zibo::getInstance($this->getBrowserMock(), $this->getConfigIOMock());
        $zibo->runEvent(Zibo::EVENT_PRE_ROUTE);
    }

    public function testRunWithChainedRequests() {
        $routerMock = $this->getMock('zibo\core\Router', array('getRequest', 'getRoutes'));
        $routerMockCall = $routerMock->expects($this->once());
        $routerMockCall->method('getRequest');
        $routerMockCall->will($this->returnValue(new Request('', '', 'zibo\core\TestController', 'chainAction')));

        $zibo = Zibo::getInstance($this->getBrowserMock(), $this->getConfigIOMock());
        $zibo->setRouter($routerMock);
        $zibo->run(false);

        $this->assertEquals(array('chain', 'index'), TestController::$actions);
    }

}

class TestController implements Controller {

    public static $actions = array();

    public function setRequest(Request $request) {}
    public function setResponse(Response $response) {}
    public function preAction() {}
    public function postAction() {}

    public function indexAction() {
        self::$actions[] = 'index';
    }

    public function chainAction() {
        self::$actions[] = 'chain';
        return new Request('', '', 'zibo\core\TestController', 'indexAction');
    }

}