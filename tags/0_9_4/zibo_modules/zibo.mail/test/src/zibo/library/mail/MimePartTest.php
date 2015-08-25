<?php

namespace zibo\library\mail;

use zibo\test\BaseTestCase;

class MimePartTest extends BaseTestCase {

    private $mimePart;

    public function setUp() {
        $this->mimePart = new MimePart();
    }

    public function tearDown() {
        unset($this->mimePart);
    }

    public function testGetCharsetOnNewObjectReturnsUtf8() {
        $this->assertSame('utf-8', $this->mimePart->getCharset());
    }

    public function testGetMimeTypeOnNewObjectReturnsTextPlain() {
        $this->assertSame('text/plain', $this->mimePart->getMimeType());
    }

    public function testGetTransferEncodingOnNewObjectReturns7Bit() {
        $this->assertSame('7bit',  $this->mimePart->getTransferEncoding());
    }

    public function testGetTransferEncodingReturnConfiguredTransferEncoding() {
        $this->mimePart->setTransferEncoding('base64');
        $this->assertSame('base64', $this->mimePart->getTransferEncoding());
    }

    public function testGetCharsetReturnsConfiguredCharset() {
        $this->mimePart->setCharset('iso-8859-1');
        $this->assertSame('iso-8859-1', $this->mimePart->getCharset());
    }

    public function testGetMimeTypeReturnsConfiguredMimeType() {
        $this->mimePart->setMimeType('text/html');
        $this->assertSame('text/html', $this->mimePart->getMimeType());
    }

    public function testGetBodyReturnsConfiguredBody() {
        $text = 'some text for the body';
        $this->mimePart->setBody($text);
        $this->assertSame($text, $this->mimePart->getBody());
    }

    public function testSetBodyWrapsWordsAt78Chars() {
        $msg = str_repeat('word ', 16);
        $this->mimePart->setBody($msg);

        $msg = wordwrap($msg, 78, "\n", false);

        $this->assertSame($msg, $this->mimePart->getBody());
    }

}