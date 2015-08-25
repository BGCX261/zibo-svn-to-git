<?php

namespace zibo\library\i18n;

use zibo\library\i18n\locale\io\IOMock as LocaleIOMock;
use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\locale\LocaleManager;
use zibo\library\i18n\translation\io\IOMock as TranslationIOMock;
use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\translation\TranslationManager;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class I18nTest extends BaseTestCase {

    private $i18n;

    public function setUp() {
        $localeIO = new LocaleIOMock();
        $translationIO = new TranslationIOMock();

        $this->i18n = new I18n(new LocaleManager($localeIO), new TranslationManager($translationIO));
    }

    public function testGetLocales() {
        $locales = $this->i18n->getLocales();

        $this->assertEquals(4, count($locales));
    }

    public function testGetLocaleWithoutParameterGivesFirstOfTheLocalesList() {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $locale = $this->i18n->getLocale();

        $this->assertTrue($locale instanceof Locale);
        $this->assertEquals('nl', $locale->getCode());
    }

    public function testHasLocaleReturnsIfLocaleExists() {
        $this->assertTrue($this->i18n->hasLocale('en'));
        $this->assertTrue($this->i18n->hasLocale('nl'));
        $this->assertTrue($this->i18n->hasLocale('en_GB'));
        $this->assertTrue($this->i18n->hasLocale('fr'));

        $this->assertFalse($this->i18n->hasLocale('ru'));
        $this->assertFalse($this->i18n->hasLocale('it'));
        $this->assertFalse($this->i18n->hasLocale('de'));
        $this->assertFalse($this->i18n->hasLocale('uk'));
    }

    public function testSetCurrentLocaleModifiesCurrentLocale() {
        $locale = $this->i18n->getLocale();
        $this->assertTrue($locale instanceof Locale);
        $this->assertEquals('nl', $locale->getCode());

        $locale = $this->i18n->getLocale('en_GB');
        $this->assertTrue($locale instanceof Locale);

        $this->i18n->setCurrentLocale($locale);

        $locale = $this->i18n->getLocale();
        $this->assertTrue($locale instanceof Locale);
        $this->assertEquals('en_GB', $locale->getCode());
    }

    public function testGetLocaleList() {
        $expected = array(
        	'nl' => 'Nederlands',
        	'en' => 'English',
        	'fr' => 'français',
        	'en_GB' => 'British English'
        );

        $result = $this->i18n->getLocaleList();

        $this->assertEquals($expected, $result);
    }

    public function testGetLocaleCodeList() {
        $expected = array(
        	'nl' => 'nl',
        	'en' => 'en',
        	'fr' => 'fr',
        	'en_GB' => 'en_GB'
        );

        $result = $this->i18n->getLocaleCodeList();

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException zibo\library\i18n\exception\LocaleNotFoundException
     */
    public function testGetLocaleThrowsLocaleNotFoundExceptionWhenLocaleNotAvailable() {
        $this->i18n->getLocale('uk');
    }

    public function testGetTranslatorAcceptsStringArgument() {
        $translator = $this->i18n->getTranslator('en');

        $this->assertTrue($translator instanceof Translator);
    }

    public function testGetTranslatorAcceptsLocaleObjectArgument() {
        $locale = $this->i18n->getLocale('en');

        $this->assertTrue($locale instanceof Locale);

        $translator = $this->i18n->getTranslator($locale);

        $this->assertTrue($translator instanceof Translator);
    }

    /**
     * @dataProvider providerGetTranslatorThrowsInvalidArgumentExceptionWhenArgumentIsNotALocaleObjectOrString
     * @expectedException zibo\ZiboException
     */
    public function testGetTranslatorThrowsInvalidArgumentExceptionWhenArgumentIsNotALocaleObjectOrString($argument) {
        $this->i18n->getTranslator($argument);
    }

    public function providerGetTranslatorThrowsInvalidArgumentExceptionWhenArgumentIsNotALocaleObjectOrString() {
        return array(
            array(1),
            array(1.1),
            array(array('whatever')),
        );
    }
}