<?php

namespace zibo\library;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class UrlTest extends BaseTestCase {

    /**
     * @dataProvider providerConstruct
     */
    public function testConstruct($url, $protocol, $host, $port, $path, $query, $baseUrl, $basePath) {
        $request = new Url($url);

        $this->assertEquals($url, $request->getUrl());
        $this->assertEquals($protocol, $request->getProtocol());
        $this->assertEquals($host, $request->getHost());
        $this->assertEquals($port, $request->getPort());
        $this->assertEquals($path, $request->getPath());
        $this->assertEquals($query, $request->getQuery());
        $this->assertEquals($baseUrl, $request->getBaseUrl());
        $this->assertEquals($basePath, $request->getBasePath());
    }

    public function providerConstruct() {
        return array(
            array('http://localhost', 'http', 'localhost', 80, '/', '', 'http://localhost', 'http://localhost/'),
            array('http://localhost/folder/index.html?test=1', 'http', 'localhost', 80, '/folder/index.html', '?test=1', 'http://localhost', 'http://localhost/folder/'),
            array('mysql://localhost:3306/database', 'mysql', 'localhost', 3306, '/database', '', 'mysql://localhost:3306', 'mysql://localhost:3306/'),
        );
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidUrlPassed
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidUrlPassed($url) {
        new Url($url);
    }

    public function providerConstructThrowsExceptionWhenInvalidUrlPassed() {
        return array(
            array(null),
            array($this),
        );
    }

}