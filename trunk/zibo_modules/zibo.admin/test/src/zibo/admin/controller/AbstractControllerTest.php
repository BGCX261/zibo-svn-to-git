<?php

namespace zibo\admin\controller;

use zibo\core\Request;

use zibo\test\BaseTestCase;

class AbstractControllerTest extends BaseTestCase {

    private $controller;

    private $request;

    public function setUp() {
        $this->request = new Request(
            'http://localhost/site',
            'http://localhost/site/path',
            'zibo\\core\\Controller',
            '*',
            array('page', '7', 'add')
        );

        $this->controller = new AbstractControllerMock();
        $this->controller->setRequest($this->request);
    }

    /**
     * @dataProvider providerForward
     */
    public function testForward($expected, $controllerClass, $action, $parameters, $basePath) {
        $result = $this->controller->mockForward($controllerClass, $action, $parameters, $basePath);
        $this->assertEquals($expected, $result);
    }

    public function providerForward() {
        $controller = 'zibo\admin\controller\AbstractController';
        $request1 = new Request('http://localhost/site', 'http://localhost/site/path', $controller, '*', array('page', '7', 'add'));
        $request2 = new Request('http://localhost/site', 'http://localhost/site/path', $controller, 'testAction', array('action'));
        $request3 = new Request('http://localhost/site', 'http://localhost/site/something/else', $controller, 'testAction', array('page', '7', 'add'));
        $request4 = new Request('http://localhost/site', 'http://localhost/site/something/else/page', $controller, '*', array('7', 'add'));
        $request5 = new Request('http://localhost/site', 'http://localhost/site/path/page/7', $controller, '*', array('add'));

        return array(
            array($request1, $controller, null, false, null),
            array($request2, $controller, 'testAction', array('action'), null),
            array($request3, $controller, 'testAction', false, 'http://localhost/site/something/else'),
            array($request4, $controller, null, true, 'http://localhost/site/something/else'),
            array($request5, $controller, null, 2, null),
        );
    }

}