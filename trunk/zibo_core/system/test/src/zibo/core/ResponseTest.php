<?php

namespace zibo\core;

use zibo\library\message\Message;
use zibo\library\http\Header;
use zibo\library\http\HeaderContainer;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ResponseTest extends BaseTestCase {

    private $response;

    public function setUp() {
        $this->response = new Response();
    }

    public function testConstruct() {
        $headers = Reflection::getProperty($this->response, 'headers');

        $this->assertEquals(200, Reflection::getProperty($this->response, 'statusCode'));
        $this->assertTrue($headers instanceof HeaderContainer);
        $this->assertTrue($headers->hasHeader('Date'));
    }

    public function testSetStatusCode() {
        $code = 404;

        $this->response->setStatusCode($code);

        $this->assertEquals($code, Reflection::getProperty($this->response, 'statusCode'));
    }

    /**
     * @dataProvider providerSetStatusCodeThrowsExceptionWhenInvalidCodePassed
     * @expectedException zibo\ZiboException
     */
    public function testSetStatusCodeThrowsExceptionWhenInvalidCodePassed($code) {
        $this->response->setStatusCode($code);
    }

    public function providerSetStatusCodeThrowsExceptionWhenInvalidCodePassed() {
        return array(
            array('test'),
            array(array()),
            array($this),
        );
    }

    public function testAddHeaderChangesStatusCodeTo302FoundWhenHeaderNameIsLocationAndStatusCodeWasNot3xx() {
        $this->assertEquals(200, $this->response->getStatusCode());

        $this->response->addHeader('Location', 'http://example.com');

        $this->assertEquals(302, $this->response->getStatusCode());
    }

    public function testAddHeaderDoesNotChangeStatusCodeWhenHeaderNameIsLocationAndStatusCodeIs3xx() {
        $this->response->setStatusCode(301);

        $this->response->addHeader('Location', 'http://example.com');

        $this->assertEquals(301, $this->response->getStatusCode());

        $this->response->setRedirect('http://example.com');

        $this->assertEquals(301, $this->response->getStatusCode());
    }

    public function testSetRedirectSetsLocationHeaderAndSetsStatusTo302FoundIfNoStatusCodeProvided() {
        $location = 'http://example.com';

        $this->assertEquals(200, $this->response->getStatusCode());

        $this->response->removeHeader('Date');
        $this->response->setRedirect($location);

        $headers = $this->response->getHeaders();

        $this->assertEquals(1, count($headers));

        foreach ($headers as $header) {
            $this->assertEquals('Location', $header->getName());
            $this->assertEquals($location, $header->getValue());
        }

        $this->assertEquals(302, $this->response->getStatusCode());
    }

    public function testSetRedirectSetsLocationAndStatus() {
        $location = 'http://example.com';
        $status = 307;

        $this->response->removeHeader('Date');
        $this->response->setRedirect($location, $status);

        $headers = $this->response->getHeaders();

        $this->assertEquals(1, count($headers));

        foreach ($headers as $header) {
            $this->assertEquals('Location', $header->getName());
            $this->assertEquals($location, $header->getValue());
        }

        $this->assertEquals($status, $this->response->getStatusCode());
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetRedirectThrowsExceptionWhenInvalidStatusCodeProvided() {
        $this->response->setRedirect('http://example.com', 500);
    }

    /**
     * @dataProvider providerWillRedirectChecksIfStatusCodeIsBetween300And399
     */
    public function testWillRedirectChecksIfStatusCodeIsBetween300And399 ($code, $redirectExpected) {
        $this->response->setStatusCode($code);
        $this->assertEquals($redirectExpected, $this->response->willRedirect());
    }

    public function providerWillRedirectChecksIfStatusCodeIsBetween300And399() {
        return array(
            array(299, false),
            array(300, true),
            array(399, true),
            array(400, false),
        );
    }

    public function testClearRedirectResetsStatusCodeTo200OKAndRemovesLocationHeader() {
        $this->response->removeHeader('Date');
        $this->response->setRedirect('http://www.example.com');
        $this->response->clearRedirect();

        $this->assertEquals(200, $this->response->getStatusCode());

        $headers = $this->response->getHeaders();

        $this->assertEquals(0, count($headers));
    }

    public function testSetAndGetLastModified() {
        $time = time();

        $this->response->setLastModified($time);

        $header = $this->response->getHeader('Last-Modified');

        $this->assertNotNull($header);
        $this->assertEquals(Header::parseTime($time), $header);
        $this->assertEquals($time, $this->response->getLastModified());

        $this->response->setLastModified();

        $header = $this->response->getHeader('Last-Modified');

        $this->assertNull($header);
        $this->assertNull($this->response->getLastModified());
    }

    public function testSetAndGetETag() {
        $eTag = 'abc';

        $this->response->setETag($eTag);

        $header = $this->response->getHeader('ETag');

        $this->assertNotNull($header);
        $this->assertEquals($eTag, $header);
        $this->assertEquals($eTag, $this->response->getETag());

        $this->response->setETag();

        $header = $this->response->getHeader('ETag');

        $this->assertNull($header);
        $this->assertNull($this->response->getETag());
    }

    /**
     * @dataProvider providerIsNotModified
     */
    public function testIsNotModified($expected, $eTag, $lastModified, $ifNoneMatch, $ifModifiedSince) {
        $_SERVER['HTTP_IF_NONE_MATCH'] = $ifNoneMatch;
        $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $ifModifiedSince;

        $request = new Request('baseUrl', 'basePath', 'controller');

        $this->response->setLastModified($lastModified);
        $this->response->setETag($eTag);

        $result = $this->response->isNotModified($request);

        $this->assertEquals($expected, $result);
    }

    public function providerIsNotModified() {
        $timestamp = 1291219200; // = date
        $date = 'Thu, 01 Dec 2010 16:00:00 GMT'; // = timestamp

        $date2 = 'Thu, 02 Feb 2012 12:00:00 GMT';

        return array(
            array(false, null, null, null, null),
            array(false, 'abc', null, null, null),
            array(false, null, null, 'abc', null),
            array(false, 'def', null, 'abc', null),
            array(true, null, null, '*', null),
            array(true, 'abc', null, 'abc', null),
            array(true, 'abc', null, '*', null),
            array(true, 'def', null, 'abc, "def"', null),
            array(false, null, $timestamp, null, null),
            array(false, null, $timestamp, null, $date2),
            array(true, null, $timestamp, null, $date),
            array(false, 'def', $timestamp, 'abc', $date),
            array(false, 'abc', $timestamp, 'abc', $date2),
            array(false, 'abc', $timestamp, '*', $date2),
            array(true, 'abc', $timestamp, 'abc', $date),
        );
    }

    public function testGetMessagesReturnsAddedMessages() {
        $this->response->addMessage(new Message('some text', 'some type'));

        $messages = $this->response->getMessages();

        $this->assertEquals(1, count($messages));

        foreach ($messages as $message) {
            $this->assertEquals('some text', $message->getMessage());
            $this->assertEquals('some type', $message->getType());
        }
    }

}