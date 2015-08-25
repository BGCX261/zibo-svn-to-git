<?php

namespace zibo\library\mail\transport;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\config\io\ini\IniConfigIO;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;
use zibo\library\mail\Message;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class MessageParserTest extends BaseTestCase {

    public function setUp() {
        $path = new File(__DIR__ . '/../../../../../');

        $this->setUpApplication($path->getPath());

        $browser = new GenericBrowser(new File(getcwd()));
        $configIO = new IniConfigIO(Environment::getInstance(), $browser);

        Zibo::getInstance($browser, $configIO);
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testConstructWithASimpleMessage() {
        $from = 'info@domain.com';
        $to = 'info@domain.com';
        $subject = 'subject';
        $message = "Hello\n\nThis is a test message.";

        $mail = new Message();
        $mail->setFrom($from);
        $mail->setTo($to);
        $mail->setSubject($subject);
        $mail->setMessage($message);

        $expectedHeaders = array(
            'From: info <info@domain.com>',
            'To: info <info@domain.com>',
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=utf-8',
            'Content-Transfer-Encoding: 7bit',
        );
        $expectedHeaders = implode("\n", $expectedHeaders);

        $expectedMessage = $message . "\n\n";

        $messageParser = new MessageParser($mail);
        $headers = implode("\n", $messageParser->getHeaders());

        $this->assertEquals($subject, $messageParser->getSubject());
        $this->assertEquals($expectedHeaders, $headers);
        $this->assertEquals($expectedMessage, $messageParser->getBody());
    }

    public function testConstructWithAHtmlMessage() {
        $from = 'Domain <info@domain.com>';
        $to = 'info@domain.com';
        $subject = 'subject';
        $message = "Hello\n\nThis is a <strong>test</strong> message.";

        $mail = new Message();
        $mail->setFrom($from);
        $mail->setTo($to);
        $mail->setSubject($subject);
        $mail->setMessage($message);
        $mail->setIsHtmlMessage(true);

        $expectedHeaders = array(
            'From: Domain <info@domain.com>',
            'To: info <info@domain.com>',
            'MIME-Version: 1.0',
        );
        $expectedHeaders = implode("\n", $expectedHeaders);

        $messageParser = new MessageParser($mail);
        $headers = implode("\n", $messageParser->getHeaders());
        $body = $messageParser->getBody();

        $this->assertEquals($subject, $messageParser->getSubject());
        $this->assertContains($expectedHeaders, $headers);
        $this->assertContains($message, $body);
        $this->assertContains(strip_tags($message), $body);
        $this->assertContains('Content-Type: multipart/alternative; boundary=', $headers);
        $this->assertContains('Content-Type: text/html; charset=utf-8', $body);
        $this->assertContains('Content-Type: text/plain; charset=utf-8', $body);
    }

}