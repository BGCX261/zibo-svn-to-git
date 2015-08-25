<?php

namespace zibo\library\spider;

use zibo\test\BaseTestCase;

use \Exception;

class CrawlTest extends BaseTestCase {

    /**
     * @dataProvider providerCrawl
     */
    public function testCrawl($error, $responseCode, $redirect, $hasContent, $baseUrl, $url) {
        $crawl = new Crawl($url);
        $crawl->performCrawl();

        $this->assertEquals($baseUrl, $crawl->getBaseUrl());

        $response = $crawl->getResponse();

        if ($responseCode) {
            $this->assertNotNull($response);
            $this->assertEquals($responseCode, $response->getResponseCode());
            $this->assertEquals($redirect, $response->isRedirect());

            if ($hasContent) {
                $this->assertNotNull($response->getContent());
            } else {
                $this->assertNull($response->getContent());
            }
        } else {
            $this->assertNull($response);
        }
    }

    public function providerCrawl() {
        return array(
            array(null, 301, true, false, 'http://localhost/kayalion/', 'http://localhost/kayalion/joppa'),
            array(null, 200, false, true, 'http://localhost/kayalion/joppa/', 'http://localhost/kayalion/joppa/'),
            array(null, 404, false, false, 'http://localhost/unexistant/', 'http://localhost/unexistant/'),
        );
    }

    /**
     * @dataProvider providerCrawlThrowsExceptionWhenAErrorOccurs
     */
    public function testCrawlThrowsExceptionWhenAErrorOccurs($error, $url) {
        try {
            $crawl = new Crawl($url);

            $this->fail();
        } catch (Exception $exception) {
            if ($error) {
                $this->assertEquals($error, $exception->getMessage());
            }
        }
    }

    public function providerCrawlThrowsExceptionWhenAErrorOccurs() {
        return array(
            array('Could not connect to lets-hope-this-domain-does-not-exist.com', 'http://lets-hope-this-domain-does-not-exist.com/test/'),
        );
    }



}