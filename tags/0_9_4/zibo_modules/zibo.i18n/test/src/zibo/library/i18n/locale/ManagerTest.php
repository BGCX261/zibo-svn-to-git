<?php

namespace zibo\library\i18n\locale;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;

use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \StdClass;

class ManagerTest extends BaseTestCase {

    public function setUp() {
        $this->browser = new GenericBrowser(new File(getcwd()));
        $this->configIOMock = new ConfigIOMock();
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function providerGetAllLocalesSortsLocalesAccordingToConfigValue() {
        return array(
            array('nl', 'nl'),
            array('fr', 'fr'),
            array('en', 'en'),
            array('en,nl', 'en', 'nl'),
            array('en,fr', 'en', 'fr'),
            array('nl,en', 'nl', 'en'),
            array('nl,fr', 'nl', 'fr'),
            array('fr,en', 'fr', 'en'),
            array('fr,nl', 'fr', 'nl'),
            array('fr,nl,en', 'fr', 'nl', 'en', 'en_GB'),
            array('fr,en,nl', 'fr', 'en', 'nl', 'en_GB'),
            array('en,fr,nl', 'en', 'fr', 'nl', 'en_GB'),
            array('en,nl,fr', 'en', 'nl', 'fr', 'en_GB'),
            array('nl,fr,en', 'nl', 'fr', 'en', 'en_GB'),
            array('nl,en,fr', 'nl', 'en', 'fr', 'en_GB'),

        );
    }

    /**
     * @dataProvider providerGetAllLocalesSortsLocalesAccordingToConfigValue
     */
    public function testGetAllLocalesSortsLocalesAccordingToConfigValue($orderConfig, $codeA, $codeB = null, $codeC = null, $codeD = null) {
        $this->configIOMock->setValues('i18n', array(
                'locale' => array(
                    'io' => 'zibo\\library\\i18n\\locale\\io\\IOMock',
                    'order' => $orderConfig,
                ),
            )
        );

        Zibo::getInstance($this->browser, $this->configIOMock);

        $manager = new Manager();

        $locales = $manager->getAllLocales();
        $this->assertEquals(4, count($locales));

        $i = 0;
        foreach ($locales as $key => $locale) {
            switch($i) {
                case 0:
                    {
                        $this->assertEquals($codeA, $key);
                        $this->assertEquals($codeA, $locale->getCode());
                    } break;
                case 1:
                    {
                        if ($codeB) {
                            $this->assertEquals($codeB, $key);
                            $this->assertEquals($codeB, $locale->getCode());
                        }
                    } break;
                case 2:
                    {
                        if ($codeC) {
                            $this->assertEquals($codeC, $key);
                            $this->assertEquals($codeC, $locale->getCode());
                        }
                    } break;
                case 3:
                    {
                        if ($codeD) {
                            $this->assertEquals($codeD, $key);
                            $this->assertEquals($codeD, $locale->getCode());
                        }
                    } break;
                default:
                    {

                    }
            }

            $i++;
        }
    }

    public function testGetLocaleReturnsInstanceOfLocale() {
        $this->configIOMock->setValues('i18n', array(
                'locale' => array(
                    'io' => 'zibo\\library\\i18n\\locale\\io\\IOMock',
                    'order' => '',
                ),
            )
        );

        Zibo::getInstance($this->browser, $this->configIOMock);

        $manager = new Manager();

        $this->assertType('zibo\\library\\i18n\\locale\\Locale', $manager->getLocale('en'));
    }

    public function testGetLocaleReturnsNullIfLocaleNotFound() {
        $this->configIOMock->setValues('i18n', array(
                'locale' => array(
                    'io' => 'zibo\\library\\i18n\\locale\\io\\IOMock',
                    'order' => '',
                ),
            )
        );

        Zibo::getInstance($this->browser, $this->configIOMock);

        $manager = new Manager();

        $this->assertNull($manager->getLocale('foo'));
    }

    /**
     * @dataProvider providerGetLocaleThrowsInvalidArgumentExceptionIfArgumentIsNotAString
     * @expectedException \InvalidArgumentException
     */
    public function testGetLocaleThrowsInvalidArgumentExceptionIfArgumentIsNotAString($arg) {
        $this->configIOMock->setValues('i18n', array(
                'locale' => array(
                    'io' => 'zibo\\library\\i18n\\locale\\io\\IOMock',
                    'order' => '',
                ),
            )
        );

        Zibo::getInstance($this->browser, $this->configIOMock);

        $manager = new Manager();

        $locale = $manager->getLocale($arg);
    }

    public function providerGetLocaleThrowsInvalidArgumentExceptionIfArgumentIsNotAString() {
        return array(
            array(1),
            array(1.1),
            array(array('whatever')),
            array(new StdClass()),
        );
    }
}