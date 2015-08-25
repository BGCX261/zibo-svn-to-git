<?php

namespace zibo\core\dispatcher;

use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class GenericDispatcherTest extends BaseTestCase {

    private $baseUrl;
    private $basePath;
    private $controllerName;
    private $actionName;

    private $controllerMock;

    protected function setUp() {
        $fileBrowser = $this->getMock('zibo\\core\\filesystem\\FileBrowser');
        $configIO = new ConfigIOMock();
        $zibo = new Zibo($fileBrowser, $configIO);

        $this->baseUrl = 'http://localhost/';
        $this->basePath = 'http://localhost/test';
        $this->controllerName = 'package\\controllerClassName';
        $this->actionName = 'actionMethodName';

        $this->controllerMock = $this->getMock('zibo\\core\\controller\\AbstractController', array($this->actionName, 'setRequest', 'setResponse', 'preAction', 'postAction', 'testAction', 'indexAction'));

        $objectFactoryMock = $this->getMock('zibo\\library\\ObjectFactory', array('create'), array());
        $objectFactoryMockCall = $objectFactoryMock->expects($this->once());
        $objectFactoryMockCall->method('create');
        $objectFactoryMockCall->with($this->equalTo($this->controllerName));
        $objectFactoryMockCall->will($this->returnValue($this->controllerMock));

        $this->dispatcher = new GenericDispatcher($zibo, $objectFactoryMock);
    }

    public function testDispatch() {
        $controllerMockActionCall = $this->controllerMock->expects($this->once());
        $controllerMockActionCall->method($this->actionName);
        $controllerMockActionCall->will($this->returnValue(null));

        $request = new Request($this->baseUrl, $this->basePath, $this->controllerName, $this->actionName);
        $response = new Response();
        $result = $this->dispatcher->dispatch($request, $response);
        $this->assertNull($result);
    }

    public function testDispatchWithParameters() {
        $returnValue = new Request($this->baseUrl, $this->basePath, 'controller', 'action');
        $param1 = 1;
        $param2 = 2;
        $controllerMockActionCall = $this->controllerMock->expects($this->once());
        $controllerMockActionCall->method($this->actionName);
        $controllerMockActionCall->with($this->equalTo($param1), $this->equalTo($param2));
        $controllerMockActionCall->will($this->returnValue($returnValue));

        $request = new Request($this->baseUrl, $this->basePath, $this->controllerName, $this->actionName, array($param1, $param2));
        $response = new Response();
        $result = $this->dispatcher->dispatch($request, $response);
        $this->assertEquals($returnValue, $result);
    }

    public function testDispatchWithAsterixAsMethodNameWillCallFirstParameterAsAction() {
        $returnValue = new Request($this->baseUrl, $this->basePath, 'controller', 'action');
        $param1 = 'test';
        $param2 = 2;
        $controllerMockActionCall = $this->controllerMock->expects($this->once());
        $controllerMockActionCall->method($param1 . 'Action');
        $controllerMockActionCall->with($this->equalTo($param2));
        $controllerMockActionCall->will($this->returnValue($returnValue));

        $request = new Request($this->baseUrl, $this->basePath, $this->controllerName, null, array($param1, $param2));
        $response = new Response();
        $result = $this->dispatcher->dispatch($request, $response);
        $this->assertEquals($returnValue, $result);
    }

    public function testDispatchWithAsterixAsMethodNameWillCallIndexActionIfFirstParameterDoesNotExist() {
        $returnValue = new Request($this->baseUrl, $this->basePath, 'controller', 'action');
        $param1 = 'unexistant';
        $param2 = 2;
        $controllerMockActionCall = $this->controllerMock->expects($this->once());
        $controllerMockActionCall->method('indexAction');
        $controllerMockActionCall->with($this->equalTo($param1), $this->equalTo($param2));
        $controllerMockActionCall->will($this->returnValue($returnValue));

        $request = new Request($this->baseUrl, $this->basePath, $this->controllerName, null, array($param1, $param2));
        $response = new Response();
        $result = $this->dispatcher->dispatch($request, $response);
        $this->assertEquals($returnValue, $result);
    }

}