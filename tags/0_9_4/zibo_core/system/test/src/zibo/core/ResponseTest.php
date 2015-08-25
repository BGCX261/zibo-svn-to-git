<?php

namespace zibo\core;

use zibo\library\message\Message;

use zibo\test\BaseTestCase;

class ResponseTest extends BaseTestCase {

    private $response;

    public function setUp() {
        $this->response = new Response();
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetStatusCodeThrowsExceptionWhenInvalidCodePassed() {
        $this->response->setStatusCode('test');
    }

    public function testRemoveHeader() {
        $this->response->addHeader('test2' ,'value');
        $this->response->addHeader('test' ,'value');

        $this->response->removeHeader('test');

        $headers = $this->response->getHeaders();
        $this->assertEquals(1, count($headers));
    }

    public function testSetHeaderRemovesPreviouslySetHeadersWithEqualNames() {
        $this->response->addHeader('test1', 'value1');
        $this->response->addHeader('test2', 'value2');

        $this->response->setHeader('test1', 'value3');

        $headers = $this->response->getHeaders();

        $this->assertEquals(2, count($headers));

        foreach ($headers as $header) {
            $name = $header->getName();
            switch($name) {
                case 'test1':
                    {
                        $this->assertEquals('value3', $header->getValue());
                    } break;
                case 'test2':
                    {
                        $this->assertEquals('value2', $header->getValue());
                    } break;

                default:
                    $this->fail('Unexpected header: ' . $name . ', with value ' . $header->getValue());
            }
        }
    }

    public function testAddHeaderChangesStatusCodeTo302FoundWhenHeaderNameIsLocationAndStatusCodeWas200OK() {
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->response->addHeader('Location', 'http://example.com');

        $this->assertEquals(302, $this->response->getStatusCode());
    }

    public function testAddHeaderDoesNotChangeStatusCodeWhenHeaderNameIsLocationAndStatusCodeWasNot200OK() {
        $this->response->setStatusCode(301);
        $this->response->addHeader('Location', 'http://example.com');

        $this->assertEquals(301, $this->response->getStatusCode());
    }

    public function testSetRedirectSetsLocationHeaderAndSetsStatusTo302FoundIfNoStatusCodeProvided() {
        $location = 'http://example.com';

        $this->response->setStatusCode(301);
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

    public function providerWillRedirectChecksIfStatusCodeIsBetween300And399() {
        return array(
            array(299, false),
            array(300, true),
            array(399, true),
            array(400, false),
        );
    }

    /**
     * @dataProvider providerWillRedirectChecksIfStatusCodeIsBetween300And399
     */
    public function testWillRedirectChecksIfStatusCodeIsBetween300And399 ($code, $redirectExpected) {
        $this->response->setStatusCode($code);
        $this->assertEquals($redirectExpected, $this->response->willRedirect());
    }

    public function testClearRedirectResetsStatusCodeTo200OKAndRemovesLocationHeader() {
        $this->response->setRedirect('http://www.example.com');

        $this->response->clearRedirect();

        $this->assertEquals(200, $this->response->getStatusCode());

        $headers = $this->response->getHeaders();

        $this->assertEquals(0, count($headers));
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