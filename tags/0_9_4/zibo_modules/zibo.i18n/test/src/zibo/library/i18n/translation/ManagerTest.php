<?php

namespace zibo\library\i18n\translation;

use zibo\library\filesystem\browser\GenericBrowser;

use zibo\library\filesystem\File;

use zibo\library\i18n\locale\Locale;

use zibo\core\Zibo;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ManagerTest extends BaseTestCase {

    public function setUp() {
        $this->englishLocale = new Locale('en', 'English', 'English', 'return $n != 1;');

        $configIOMock = new ConfigIOMock();

        $browser = new GenericBrowser(new File(getcwd()));

        $values = array(
            'translation' => array(
                'io' => 'zibo\\library\\i18n\\translation\\io\\IOMock',
            ),
        );

        $configIOMock->setValues('i18n', $values);

        Zibo::getInstance($browser, $configIOMock);

        $this->manager = new Manager();
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testGetTranslatorReturnsInstanceOfTranslator() {
        $translator = $this->manager->getTranslator($this->englishLocale);

        $this->assertType('zibo\library\i18n\translation\Translator', $translator);
    }

    public function testGetTranslatorReturnsTranslatorWithIOConfiguredInConfig() {
       $translator = $this->manager->getTranslator($this->englishLocale);

       $this->assertEquals('A label', $translator->translate('label'));
    }
}