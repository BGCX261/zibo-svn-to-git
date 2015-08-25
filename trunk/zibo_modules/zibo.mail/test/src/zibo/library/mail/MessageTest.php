<?php

namespace zibo\library\mail;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\config\io\ini\IniConfigIO;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

use \PHPUnit_Framework_Constraint_IsType;

use \stdClass;

class MessageTest extends BaseTestCase {

    private $message;

    protected function setUp() {
        $path = new File(__DIR__ . '/../../../../');

        $this->setUpApplication($path->getPath());

        $browser = new GenericBrowser(new File(getcwd()));
        $configIO = new IniConfigIO(Environment::getInstance(), $browser);

        Zibo::getInstance($browser, $configIO);

        $this->message = new Message();
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);

        unset($this->message);
    }

    public function testGetSubjectOnNewObjectReturnsStringNoSubject() {
        $this->assertSame('no subject', $this->message->getSubject());
    }

    public function testIsHtmlMessageOnNewObjectReturnsBooleanFalse() {
        $this->assertFalse($this->message->isHtmlMessage());
    }

    public function testGetPartsOnNewObjectReturnsArrayWithPlainTextPartCalledBodyWithEmptyStringBody() {
        $parts = $this->message->getParts();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $parts);
        $this->assertSame(1, count($parts));

        $this->assertArrayHasKey('body', $parts);

        $bodyPart = $parts['body'];
        $this->assertSame('text/plain', $bodyPart->getMimeType());
        $this->assertSame('', $bodyPart->getBody());
    }

    public function testHasPartOnNewObjectWithNameBodyReturnsTrue() {
        $this->assertTrue($this->message->hasPart('body'));
    }

    public function testGetPartOnNewObjectWithNameBodyReturnsPlainTextPartWithEmptyStringBody() {
        $bodyPart = $this->message->getPart('body');
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_OBJECT, $bodyPart);
        $this->assertEquals('zibo\\library\\mail\\MimePart', get_class($bodyPart));
    }

    public function testGetToOnNewObjectReturnsEmptyArray() {
        $to = $this->message->getTo();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $to);
        $this->assertTrue(empty($to));
    }

    public function testGetCcOnNewObjectReturnsEmptyArray() {
        $cc = $this->message->getCc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $cc);
        $this->assertTrue(empty($cc));
    }

    public function testGetBccOnNewObjectReturnsEmptyArray() {
        $bcc = $this->message->getBcc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $bcc);
        $this->assertTrue(empty($bcc));
    }

    public function testGetReplyToOnNewObjectReturnsNull() {
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NULL, $this->message->getReplyTo());
    }

    public function testGetFromOnNewObjectReturnsNull() {
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NULL, $this->message->getFrom());
    }

    public function testSetFromAcceptsInstanceOfAddress() {
        $address = new Address('test <test@example.com>');
        $this->message->setFrom($address);
        $this->assertSame($address, $this->message->getFrom());
    }

    public function testSetFromAcceptsStringWithAddress() {
        $addressString = 'test <test@example.com>';
        $this->message->setFrom($addressString);
        $this->assertEquals(new Address($addressString), $this->message->getFrom());
    }

    public function testGetReturnPathOnNewObjectReturnsNull() {
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NULL, $this->message->getReturnPath());
    }

    public function testGetReturnPathReturnsFromWhenNoReturnPathSet() {
        $address = new Address('test <test@example.com>');
        $this->message->setFrom($address);

        $this->assertSame($address, $this->message->getReturnPath());
    }

    public function testSetReturnPathAcceptsInstanceOfAddress() {
        $address = new Address('test <test@example.com>');
        $this->message->setReturnPath($address);
        $this->assertSame($address, $this->message->getReturnPath());
    }

    public function testSetReturnPathAcceptsStringWithAddress() {
        $addressString = 'test <test@example.com>';
        $this->message->setReturnPath($addressString);
        $this->assertEquals(new Address($addressString), $this->message->getReturnPath());
    }

    public function testSetReplyToAcceptsInstanceOfAddress() {
        $address = new Address('test <test@example.com>');
        $this->message->setReplyTo($address);
        $this->assertSame($address, $this->message->getReplyTo());
    }

    public function testSetReplyToAcceptsStringWithAddress() {
        $addressString = 'test <test@example.com>';
        $this->message->setReplyTo($addressString);
        $this->assertEquals(new Address($addressString), $this->message->getReplyTo());
    }

    public function testSetToAcceptsOneInstanceOfAddress() {
        $address = new Address('test <test@example.com>');
        $this->message->setTo($address);
        $to = $this->message->getTo();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $to);
        $this->assertSame(1, count($to));
        $this->assertSame($address, array_pop($to));
    }

    public function testSetToAcceptsArrayOfInstancesOfAddress() {
        $addressA = new Address('test <test@example.com>');
        $addressB = new Address('test bis <test_bis@example.com>');

        $addresses = array($addressA, $addressB);
        $this->message->setTo($addresses);

        $to = $this->message->getTo();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $to);
        $this->assertSame(2, count($to));

        $this->assertSame($addressB, array_pop($to));
        $this->assertSame($addressA, array_pop($to));
    }

    public function testSetToAcceptsOneStringWithAddress() {
        $addressString = 'test <test@example.com>';
        $this->message->setTo($addressString);

        $to = $this->message->getTo();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $to);
        $this->assertSame(1, count($to));

        $this->assertEquals(new Address($addressString), array_pop($to));
    }

    public function testSetToAcceptsArrayOfStringsWithAddress() {
        $addressStringA = 'test <test@example.com>';
        $addressStringB = 'test bis <test_bis@example.com>';

        $addresses = array($addressStringA, $addressStringB);
        $this->message->setTo($addresses);

        $to = $this->message->getTo();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $to);
        $this->assertSame(2, count($to));

        $this->assertEquals(new Address($addressStringB), array_pop($to));
        $this->assertEquals(new Address($addressStringA), array_pop($to));
    }

   public function testSetCcAcceptsOneInstanceOfAddress() {
        $address = new Address('test <test@example.com>');
        $this->message->setCc($address);
        $cc = $this->message->getCc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $cc);
        $this->assertSame(1, count($cc));
        $this->assertSame($address, array_pop($cc));
    }

    public function testSetCcAcceptsArrayOfInstancesOfAddress() {
        $addressA = new Address('test <test@example.com>');
        $addressB = new Address('test bis <test_bis@example.com>');

        $addresses = array($addressA, $addressB);
        $this->message->setCc($addresses);

        $cc = $this->message->getCc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $cc);
        $this->assertSame(2, count($cc));

        $this->assertSame($addressB, array_pop($cc));
        $this->assertSame($addressA, array_pop($cc));
    }

    public function testSetCcAcceptsOneStringWithAddress() {
        $addressString = 'test <test@example.com>';
        $this->message->setCc($addressString);

        $cc = $this->message->getCc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $cc);
        $this->assertSame(1, count($cc));

        $this->assertEquals(new Address($addressString), array_pop($cc));
    }

    public function testSetCcAcceptsArrayOfStringsWithAddress() {
        $addressStringA = 'test <test@example.com>';
        $addressStringB = 'test bis <test_bis@example.com>';

        $addresses = array($addressStringA, $addressStringB);
        $this->message->setCc($addresses);

        $cc = $this->message->getCc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $cc);
        $this->assertSame(2, count($cc));

        $this->assertEquals(new Address($addressStringB), array_pop($cc));
        $this->assertEquals(new Address($addressStringA), array_pop($cc));
    }

   public function testSetBccAcceptsOneInstanceOfAddress() {
        $address = new Address('test <test@example.com>');
        $this->message->setBcc($address);
        $bcc = $this->message->getBcc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $bcc);
        $this->assertSame(1, count($bcc));
        $this->assertSame($address, array_pop($bcc));
    }

    public function testSetBccAcceptsArrayOfInstancesOfAddress() {
        $addressA = new Address('test <test@example.com>');
        $addressB = new Address('test bis <test_bis@example.com>');

        $addresses = array($addressA, $addressB);
        $this->message->setBcc($addresses);

        $bcc = $this->message->getBcc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $bcc);
        $this->assertSame(2, count($bcc));

        $this->assertSame($addressB, array_pop($bcc));
        $this->assertSame($addressA, array_pop($bcc));
    }

    public function testSetBccAcceptsOneStringWithAddress() {
        $addressString = 'test <test@example.com>';
        $this->message->setBcc($addressString);

        $bcc = $this->message->getBcc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $bcc);
        $this->assertSame(1, count($bcc));

        $this->assertEquals(new Address($addressString), array_pop($bcc));
    }

    public function testSetBccAcceptsArrayOfStringsWithAddress() {
        $addressStringA = 'test <test@example.com>';
        $addressStringB = 'test bis <test_bis@example.com>';

        $addresses = array($addressStringA, $addressStringB);
        $this->message->setBcc($addresses);

        $bcc = $this->message->getBcc();
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $bcc);
        $this->assertSame(2, count($bcc));

        $this->assertEquals(new Address($addressStringB), array_pop($bcc));
        $this->assertEquals(new Address($addressStringA), array_pop($bcc));
    }

    public function providerTestSubjectWithNonStringThrowsZiboException() {
        return array(
            array(null),
            array(false),
            array(true),
            array(array()),
            array(1),
            array(1.1),
            array(new stdClass()),
        );
    }

    /**
     * @expectedException zibo\ZiboException
     * @dataProvider providerTestSubjectWithNonStringThrowsZiboException
     */
    public function testSetSubjectWithNonStringThrowsZiboException($nonString) {
        $this->message->setSubject($nonString);
    }

    public function testSetSubjectTrimsSubject() {
        $subject = '    some subject with leading and trailing space       ';
        $expectedSubject = trim($subject);

        $this->message->setSubject($subject);

        $this->assertSame($expectedSubject, $this->message->getSubject());
    }

    public function testSetSubjectWithEmptyStringSetsSubjectToStringNoSubject() {
        $this->message->setSubject('');

        $this->assertSame('no subject', $this->message->getSubject());
    }

    public function testHasPartWithNameThatNotExistsReturnsFalse() {
        $this->assertFalse($this->message->hasPart('unexisting part'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testGetPartWithNameThatNotExistsThrowsZiboException() {
        $part = $this->message->getPart('unexisting part');
    }

    public function testHasPartReturnsFalseAfterPartWasRemoved() {
        $this->assertTrue($this->message->hasPart('body'));
        $part = $this->message->removePart('body');
        $this->assertFalse($this->message->hasPart('body'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRemovePartWithNameThatNotExistsThrowsZiboException() {
        $this->message->removePart('unexisting part');
    }

    public function testGetPartsAfterAddPartReturnsSameInstance() {
        $part = $this->getMock('zibo\\library\\mail\\MimePart');
        $name = $this->message->addPart($part);
        $returnedPart = $this->message->getPart($name);
        $this->assertSame($part, $returnedPart);
    }

    public function testSetPartOverwritesExistingPartWithSameNew() {
        $partA = $this->getMock('zibo\\library\\mail\\MimePart');
        $this->message->addPart($partA, 'test');

        $partB = $this->getMock('zibo\\library\\mail\\MimePart');
        $name = $this->message->addPart($partB, 'test');
        $returnedPart = $this->message->getPart('test');


        $this->assertNotSame($partA, $returnedPart);
        $this->assertSame($partB, $returnedPart);
    }

    public function testSetMessageDoesNotWrapWordLongerThan70Chars() {
        $longWord = 'w' . str_repeat('o', 70) . 'rd';
        $this->message->setMessage($longWord);

        $this->assertSame($longWord, $this->message->getMessage());
    }

}