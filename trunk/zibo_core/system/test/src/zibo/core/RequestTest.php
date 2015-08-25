<?php

namespace zibo\core;

use zibo\library\http\Header;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class RequestTest extends BaseTestCase {

    public function testConstruct() {
        $baseUrl = 'baseUrl';
        $basePath = 'baseUrl/basePath';
        $controllerName = 'controllerName';
        $actionName = 'actionName';
        $parameters = array('Value');
        $queryParameters = array('var1' => 'value1');
        $bodyParameters = array('var2' => 'value2');
        $route = '/basePath';

        $request = new Request($baseUrl, $basePath, $controllerName, $actionName, $parameters, $queryParameters, $bodyParameters);

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
        $acceptLanguage = 'en';

        $_SERVER = array();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $acceptLanguage;

        $request = new Request('baseUrl', 'basePath', 'controller');

        $this->assertNull($request->getHeader(Header::HEADER_USER_AGENT));
        $this->assertEquals($acceptLanguage, $request->getHeader(Header::HEADER_ACCEPT_LANGUAGE));
    }

    public function testGetAccept() {
        $accept = 'text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c';

        $_SERVER = array();
        $_SERVER['HTTP_ACCEPT'] = $accept;

        $request = new Request('baseUrl', 'basePath', 'controller');

        $accept = $request->getAccept();

        $expected = array(
            'text/x-c' => 1,
            'text/html' => 1,
            'text/x-dvi' => 0.8,
            'text/plain' => 0.5,
        );

        $this->assertEquals($expected, $accept);
    }

    public function testGetAcceptCharset() {
        $acceptCharset = 'ISO-8859-1,utf-8;q=0.7,*;q=0.7';

        $_SERVER = array();
        $_SERVER['HTTP_ACCEPT_CHARSET'] = $acceptCharset;

        $request = new Request('baseUrl', 'basePath', 'controller');

        $charset = $request->getAcceptCharset();

        $expected = array(
                    'ISO-8859-1' => 1,
                    '*' => 0.7,
                    'utf-8' => 0.7,
        );

        $this->assertEquals($expected, $charset);
    }

    public function testGetAcceptEncoding() {
        $acceptEncoding = 'gzip;q=1.0, identity; q=0.5, *;q=0';

        $_SERVER = array();
        $_SERVER['HTTP_ACCEPT_ENCODING'] = $acceptEncoding;

        $request = new Request('baseUrl', 'basePath', 'controller');

        $encoding = $request->getAcceptEncoding();

        $expected = array(
                    'gzip' => 1,
                    'identity' => 0.5,
        );

        $this->assertEquals($expected, $encoding);
    }

    public function testGetAcceptLanguage() {
        $acceptLanguage = 'da, en-gb;q=0.8, en;q=0.7';

        $_SERVER = array();
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $acceptLanguage;

        $request = new Request('baseUrl', 'basePath', 'controller');

        $language = $request->getAcceptLanguage();

        $expected = array(
                    'da' => 1,
                    'en-gb' => 0.8,
                    'en' => 0.7,
        );

        $this->assertEquals($expected, $language);
    }

    public function testIsNotXmlHttpRequest() {
        $request = new Request('baseUrl', 'basePath', 'controller');

        $this->assertFalse($request->isXmlHttpRequest());
    }

    public function testIsXmlHttpRequest() {
        $_SERVER = array();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = Request::XML_HTTP_REQUEST;

        $request = new Request('baseUrl', 'basePath', 'controller');

        $this->assertTrue($request->isXmlHttpRequest());
    }

}