<?php

namespace zibo\core;

use zibo\core\dispatcher\GenericDispatcher;

use zibo\core\controller\Controller;

use zibo\library\ObjectFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ZiboTest extends BaseTestCase {

    private function getBrowserMock() {
        return $this->getMock('zibo\\core\\filesystem\\FileBrowser');
    }

    private function getConfigIOMock() {
        return $this->getMock('zibo\\core\\config\\io\\ConfigIO');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testTriggerEventThrowsExceptionWhenSystemEventNameIsPassed() {
        $zibo = new Zibo($this->getBrowserMock(), $this->getConfigIOMock());
        $zibo->triggerEvent(Zibo::EVENT_PRE_ROUTE);
    }

    public function testMainWithChainedRequests() {
        $routerMock = $this->getMock('zibo\\core\\router\\Router', array('getRequest', 'getRoutes', 'getAliases'));
        $routerMockCall = $routerMock->expects($this->once());
        $routerMockCall->method('getRequest');
        $routerMockCall->will($this->returnValue(new Request('', '', 'zibo\core\TestController', 'chainAction')));

        $zibo = new Zibo($this->getBrowserMock(), $this->getConfigIOMock());
        $zibo->setRouter($routerMock);
        $zibo->setDispatcher(new GenericDispatcher($zibo, new ObjectFactory()));
        $zibo->main();

        $this->assertEquals(array('chain', 'index'), TestController::$actions);
    }

}

class TestController implements Controller {

    public static $actions = array();

    public function setZibo(Zibo $zibo) {}
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