<?php

namespace zibo\library\mail;

use zibo\core\environment\Environment;
use zibo\core\Zibo;

use zibo\library\config\io\ini\IniConfigIO;
use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;
use zibo\library\validation\ValidationFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

use zibo\library\validation\validator\EmailValidator;

use \Exception;

class AddressTest extends BaseTestCase {

    protected function setUp() {
        $path = new File(__DIR__ . '/../../../../');

        $this->setUpApplication($path->getPath());

        $browser = new GenericBrowser(new File(getcwd()));
        $configIO = new IniConfigIO(Environment::getInstance(), $browser);

        Zibo::getInstance($browser, $configIO);

        $validationFactory = ValidationFactory::getInstance();
        $validationFactory->registerValidator('email', 'zibo\\library\\mail\\AddressValidator');
    }

    protected function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    /**
     * @dataProvider providerConstruct
     */
    public function testConstruct($string, $name, $email, $toString) {
        $address = new Address($string);
        $this->assertEquals($name, $address->getDisplayName());
        $this->assertEquals($email, $address->getEmailAddress());
        $this->assertEquals($toString, $address->__toString());
    }

    public function providerConstruct() {
        return array(
            array('info@domain.com', 'info', 'info@domain.com', 'info <info@domain.com>'),
            array('Domain.com <info@domain.com>', 'Domain.com', 'info@domain.com', 'Domain.com <info@domain.com>'),
        );
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidEmailAddressPassed
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidEmailAddressPassed($address) {
        new Address($address);
    }

    public function providerConstructThrowsExceptionWhenInvalidEmailAddressPassed() {
        return array(
            array(null),
            array($this),
            array('infodomain.com'),
            array('Domain.com <infodomain.com>'),
        );
    }

    public function testConstructUsesValidatorRegisteredAsEmailWithValidationFactory() {
        try {
            $invalidAddress = new Address('invalid@example.com');
            $this->fail('Should have thrown an exception');
        } catch (Exception $e) {

        }
    }
}

class AddressValidator extends EmailValidator {
    public function isValid($value) {
        if ($value === 'invalid@example.com') {
            return false;
        }

        return parent::isValid($value);
    }
}