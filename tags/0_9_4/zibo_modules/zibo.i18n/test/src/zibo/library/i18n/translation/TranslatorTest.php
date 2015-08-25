<?php

namespace zibo\library\i18n\translation;

use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\translation\io\IOMock;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class TranslatorTest extends BaseTestCase {

    public function setUp() {
        $this->englishLocale = new Locale('en', 'English', 'English', '$n != 1');
        $this->dutchLocale = new Locale('nl', 'Dutch', 'Nederlands', '$n != 1');
        $this->frenchLocale = new Locale('fr', 'French', 'français', '$n > 1');

        $this->io = new IOMock();
    }

    public function tearDown() {

    }

    public function testConstruct() {
        $translator = new Translator($this->englishLocale, $this->io);
        $this->assertEquals($this->englishLocale, Reflection::getProperty($translator, 'locale'));
    }

    /**
     * @dataProvider providerTranslatePluralUsesPluralRuleFromLocale
     */
    public function testTranslatePluralUsesPluralRuleFromLocale(Translator $translator, $n, $expected) {
        $result = $translator->translatePlural($n, 'apple');
        $this->assertEquals($expected, $result);
    }

    public function providerTranslatePluralUsesPluralRuleFromLocale() {
        $englishLocale = new Locale('en', 'English', 'English', '$n != 1');
        $dutchLocale = new Locale('nl', 'Dutch', 'Nederlands', '$n != 1');
        $frenchLocale = new Locale('fr', 'French', 'français', '$n > 1');
        $russianLocale = new Locale('ru', 'Russian', 'русский',
            '($n % 10 == 1 && $n % 100 != 11) ? 0 : ($n % 10 >=2 && $n % 10 <=4 && ($n % 100 < 10 || $n % 100 >= 20) ? 1 : 2)');

        $io = new IOMock();

        return array(
            array(new Translator($englishLocale, $io), 0, '0 apples'),
            array(new Translator($englishLocale, $io), 1, '1 apple'),
            array(new Translator($englishLocale, $io), 2, '2 apples'),
            array(new Translator($englishLocale, $io), 3, '3 apples'),

            array(new Translator($dutchLocale, $io), 0, '0 appels'),
            array(new Translator($dutchLocale, $io), 1, '1 appel'),
            array(new Translator($dutchLocale, $io), 2, '2 appels'),
            array(new Translator($dutchLocale, $io), 3, '3 appels'),

            array(new Translator($frenchLocale, $io), 0, '0 pomme'),
            array(new Translator($frenchLocale, $io), 1, '1 pomme'),
            array(new Translator($frenchLocale, $io), 2, '2 pommes'),
            array(new Translator($frenchLocale, $io), 3, '3 pommes'),

            array(new Translator($russianLocale, $io), 0, '0 яблок'),
            array(new Translator($russianLocale, $io), 1, '1 яблоко'),
            array(new Translator($russianLocale, $io), 2, '2 яблока'),
            array(new Translator($russianLocale, $io), 3, '3 яблока'),
            array(new Translator($russianLocale, $io), 4, '4 яблока'),
            array(new Translator($russianLocale, $io), 5, '5 яблок'),
            array(new Translator($russianLocale, $io), 6, '6 яблок'),
            array(new Translator($russianLocale, $io), 7, '7 яблок'),
            array(new Translator($russianLocale, $io), 8, '8 яблок'),
            array(new Translator($russianLocale, $io), 9, '9 яблок'),
            array(new Translator($russianLocale, $io), 10, '10 яблок'),
            array(new Translator($russianLocale, $io), 11, '11 яблок'),
            array(new Translator($russianLocale, $io), 12, '12 яблок'),
            array(new Translator($russianLocale, $io), 13, '13 яблок'),
            array(new Translator($russianLocale, $io), 14, '14 яблок'),
            array(new Translator($russianLocale, $io), 15, '15 яблок'),
            array(new Translator($russianLocale, $io), 16, '16 яблок'),
            array(new Translator($russianLocale, $io), 17, '17 яблок'),
            array(new Translator($russianLocale, $io), 18, '18 яблок'),
            array(new Translator($russianLocale, $io), 19, '19 яблок'),
            array(new Translator($russianLocale, $io), 20, '20 яблок'),
            array(new Translator($russianLocale, $io), 21, '21 яблоко'),
            array(new Translator($russianLocale, $io), 22, '22 яблока'),
            array(new Translator($russianLocale, $io), 23, '23 яблока'),
            array(new Translator($russianLocale, $io), 24, '24 яблока'),
            array(new Translator($russianLocale, $io), 25, '25 яблок'),
            array(new Translator($russianLocale, $io), 26, '26 яблок'),
            array(new Translator($russianLocale, $io), 27, '27 яблок'),
            array(new Translator($russianLocale, $io), 28, '28 яблок'),
            array(new Translator($russianLocale, $io), 29, '29 яблок'),
        );
    }

    /**
     * @dataProvider providerTranslate
     */
    public function testTranslate($expected, $key, $vars = null, $default = null) {
        $translator = new Translator($this->englishLocale, $this->io);
        $result = $translator->translate($key, $vars, $default);
        $this->assertEquals($expected, $result);
    }

    public function providerTranslate() {
        return array(
            array('A label', 'label'),
            array('This is a label with name Label1', 'label.vars', array('object' => 'label', 'name' => 'Label1')),
            array('This is a translation', 'label.var', 'translation'),
            array('[untranslated]', 'untranslated'),
            array('Default translation test', 'untranslated', 'translation', 'Default %1% test'),
        );
    }

}

