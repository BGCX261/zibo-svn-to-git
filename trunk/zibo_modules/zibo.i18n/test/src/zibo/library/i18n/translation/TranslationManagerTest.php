<?php

namespace zibo\library\i18n\translation;

use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\translation\io\IOMock;
use zibo\library\i18n\translation\Translator;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ManagerTest extends BaseTestCase {

    public function setUp() {
        $this->englishLocale = new Locale('en', 'English', 'return $n != 1;');

        $translationIO = new IOMock();
        $this->manager = new TranslationManager($translationIO);
    }

    public function testGetTranslatorReturnsInstanceOfTranslator() {
        $translator = $this->manager->getTranslator($this->englishLocale);

        $this->assertTrue($translator instanceof Translator);
    }

    public function testGetTranslatorReturnsTranslatorWithIOConfiguredInConfig() {
       $translator = $this->manager->getTranslator($this->englishLocale);

       $this->assertEquals('A label', $translator->translate('label'));
    }
}