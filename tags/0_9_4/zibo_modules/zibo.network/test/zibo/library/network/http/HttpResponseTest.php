<?php

namespace zibo\library\network\http;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class HttpResponseTest extends BaseTestCase {

    /**
     * @dataProvider providerIsRedirect
     */
    public function testIsRedirect($expected, $responseCode) {
        $response = new HttpResponse('HTTP/1.1 ' . $responseCode);
//        Reflection::setProperty($response, 'responseCode', $responseCode);

        $this->assertEquals($expected, $response->isRedirect());
    }

    public function providerIsRedirect() {
        return array(
            array(true, 301),
            array(true, 302),
            array(false, 200),
            array(false, 404),
            array(false, 501),
        );
    }

    /**
     * @dataProvider providerParseResponse
     */
    public function testParseResponse($responseCode, $headers, $response) {
        $response = new HttpResponse($response);

        $this->assertEquals($responseCode, $response->getResponseCode());
        $this->assertEquals($headers, $response->getHeaders());
    }

    public function providerParseResponse() {
        $response1 = "HTTP/1.1 301 Moved Permanently\r\nDate: Tue, 28 Sep 2010 20:16:33 GMT\r\nServer: Apache/2.2.14 (Ubuntu)";
        $headers1 = array(
            'Date' => 'Tue, 28 Sep 2010 20:16:33 GMT',
            'Server' => 'Apache/2.2.14 (Ubuntu)',
        );

        $response2 = "HTTP/1.1 404\r
Date: Fri, 01 Oct 2010 12:23:43 GMT\r
Server: Apache/1.3.34 (Debian) FrontPage/5.0.2.2635 mod_ssl/2.8.25 OpenSSL/0.9.8c\r
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0\r
Expires: Thu, 19 Nov 1981 08:52:00 GMT\r
Pragma: no-cache\r
X-Powered-By: PHP/5.2.12-0.dotdeb.0\r
Keep-Alive: timeout=15, max=100\r
Connection: Keep-Alive\r
Content-Type: text/html; charset: utf-8";
        $headers2 = array(
            'Date' => 'Fri, 01 Oct 2010 12:23:43 GMT',
            'Server' => 'Apache/1.3.34 (Debian) FrontPage/5.0.2.2635 mod_ssl/2.8.25 OpenSSL/0.9.8c',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Expires' => 'Thu, 19 Nov 1981 08:52:00 GMT',
            'Pragma' => 'no-cache',
            'X-Powered-By' => 'PHP/5.2.12-0.dotdeb.0',
            'Keep-Alive' => 'timeout=15, max=100',
            'Connection' => 'Keep-Alive',
            'Content-Type' => 'text/html; charset: utf-8'
        );

        return array(
            array(301, $headers1, $response1),
            array(404, $headers2, $response2),
        );
    }

}