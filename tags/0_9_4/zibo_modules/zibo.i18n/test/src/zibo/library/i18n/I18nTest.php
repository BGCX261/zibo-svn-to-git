<?php

namespace zibo\library\i18n;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;

use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class I18nTest extends BaseTestCase {

    private $configIOMock;

    public function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $this->configIOMock = new ConfigIOMock();
        $this->configIOMock->setValues('i18n', array(
                'locale' => array(
                    'io' => 'zibo\\library\\i18n\\locale\\io\\IOMock',
                    'negotiator' => 'zibo\\library\\i18n\\locale\\negotiator\\NegotiatorMock',
                    'order' => 'nl,en,fr,en_GB',
                ),
            )
        );

        Zibo::getInstance($browser, $this->configIOMock);
    }

    public function tearDown() {
        Reflection::setProperty(I18n::getInstance(), 'instance', null);
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testGetAllLocales() {
        $i18n = I18n::getInstance();

        $locales = $i18n->getAllLocales();

        $this->assertEquals(4, count($locales));
    }

    public function testGetLocaleWithoutParameterGivesFirstOfTheLocalesList() {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $i18n = I18n::getInstance();
        $locale = $i18n->getLocale();
        $this->assertType('zibo\\library\\i18n\\locale\\Locale', $locale);
        $this->assertEquals('nl', $locale->getCode());
    }

    public function testHasLocaleReturnsIfLocaleExists() {
        $i18n = I18n::getInstance();

        $this->assertTrue($i18n->hasLocale('en'));
        $this->assertTrue($i18n->hasLocale('nl'));
        $this->assertTrue($i18n->hasLocale('en_GB'));
        $this->assertTrue($i18n->hasLocale('fr'));

        $this->assertFalse($i18n->hasLocale('ru'));
        $this->assertFalse($i18n->hasLocale('it'));
        $this->assertFalse($i18n->hasLocale('de'));
        $this->assertFalse($i18n->hasLocale('uk'));
    }

    public function testSetCurrentLocaleModifiesCurrentLocale() {
        $i18n = I18n::getInstance();

        $locale = $i18n->getLocale();
        $this->assertType('zibo\\library\\i18n\\locale\Locale', $locale);
        $this->assertEquals('nl', $locale->getCode());

        $locale = $i18n->getLocale('en_GB');
        $this->assertType('zibo\\library\\i18n\\locale\Locale', $locale);

        $i18n->setCurrentLocale($locale);

        $locale = $i18n->getLocale();
        $this->assertType('zibo\\library\\i18n\\locale\Locale', $locale);
        $this->assertEquals('en_GB', $locale->getCode());
    }

    public function testGetLocaleList() {
        $expected = array('nl' => 'Nederlands', 'en' => 'English', 'fr' => 'français', 'en_GB' => 'British English');
        $result = I18n::getInstance()->getLocaleList();
        $this->assertEquals($expected, $result);
    }

    public function testGetLocaleCodeList() {
        $expected = array('nl' => 'nl', 'en' => 'en', 'fr' => 'fr', 'en_GB' => 'en_GB');
        $result = I18n::getInstance()->getLocaleCodeList();
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException zibo\library\i18n\exception\LocaleNotFoundException
     */
    public function testGetLocaleThrowsLocaleNotFoundExceptionWhenLocaleNotAvailable() {
        $i18n = I18n::getInstance();

        $locale = $i18n->getLocale('uk');
    }

    public function testGetTranslatorAcceptsStringArgument() {
        $translator = I18n::getInstance()->getTranslator('en');
        $this->assertType('zibo\\library\\i18n\\translation\\Translator', $translator);
    }

    public function testGetTranslatorAcceptsLocaleObjectArgument() {
        $i18n = I18n::getInstance();
        $locale = $i18n->getLocale('en');
        $this->assertType('zibo\\library\\i18n\\locale\Locale', $locale);
        $translator = $i18n->getTranslator($locale);
        $this->assertType('zibo\\library\\i18n\\translation\\Translator', $translator);
    }

    /**
     * @dataProvider providerGetTranslatorThrowsInvalidArgumentExceptionWhenArgumentIsNotALocaleObjectOrString
     * @expectedException \InvalidArgumentException
     */
    public function testGetTranslatorThrowsInvalidArgumentExceptionWhenArgumentIsNotALocaleObjectOrString($argument) {
        $i18n = I18n::getInstance();

        $translator = $i18n->getTranslator($argument);
    }

    public function providerGetTranslatorThrowsInvalidArgumentExceptionWhenArgumentIsNotALocaleObjectOrString() {
        return array(
            array(1),
            array(1.1),
            array(array('whatever')),
        );
    }
}