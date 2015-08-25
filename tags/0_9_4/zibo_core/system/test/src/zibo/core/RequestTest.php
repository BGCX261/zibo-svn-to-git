<?php

namespace zibo\core;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class RequestTest extends BaseTestCase {

    public function testConstruct() {
        $baseUrl = 'baseUrl';
        $basePath = 'baseUrl/basePath';
        $controllerName = 'controllerName';
        $actionName = 'actionName';
        $parameters = array('Value');
        $route = '/basePath';

        $request = new Request($baseUrl, $basePath, $controllerName, $actionName, $parameters);

        $this->assertEquals($baseUrl, Reflection::getProperty($request, 'baseUrl'));
        $this->assertEquals($basePath, Reflection::getProperty($request, 'basePath'));
        $this->assertEquals($controllerName, Reflection::getProperty($request, 'controllerName'));
        $this->assertEquals($actionName, Reflection::getProperty($request, 'actionName'));
        $this->assertEquals($parameters, Reflection::getProperty($request, 'parameters'));
        $this->assertEquals($route, Reflection::getProperty($request, 'route'));
    }

    /**
     * @dataProvider providerGetParametersAsString
     */
    public function testGetParametersAsString($expected, $request) {
        $parametersAsString = $request->getParametersAsString();

        $this->assertEquals($expected, $parametersAsString);
    }

    public function providerGetParametersAsString() {
        return array(
            array('', new Request('baseUrl', 'basePath', 'controller', 'action', array())),
            array('/test/parameters', new Request('baseUrl', 'basePath', 'controller', 'action', array('test', 'parameters'))),
        );
    }

    public function testGetHeader() {
        $request = new Request('baseUrl', 'basePath', 'controller', 'action');
        $acceptLanguage = 'en';

        $_SERVER[Request::HEADER_ACCEPT_LANGUAGE] = null;

        $this->assertNull($request->getHeader(Request::HEADER_ACCEPT_LANGUAGE));

        $_SERVER[Request::HEADER_ACCEPT_LANGUAGE] = $acceptLanguage;

        $this->assertEquals($acceptLanguage, $request->getHeader(Request::HEADER_ACCEPT_LANGUAGE));
    }

    public function testIsXmlHttpRequest() {
        $request = new Request('baseUrl', 'basePath', 'controller', 'action');

        $this->assertFalse($request->isXmlHttpRequest());

        $_SERVER[Request::HEADER_REQUEST] = Request::XML_HTTP_REQUEST;

        $this->assertTrue($request->isXmlHttpRequest());
    }

}