<?php

namespace zibo\library\i18n\locale\io;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;

use zibo\library\filesystem\File;

use zibo\library\i18n\I18n;
use zibo\library\i18n\locale\Locale;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ConfigLocaleIOTest extends BaseTestCase {

    private $localeCode = 'locale';
    private $localeName = 'Locale name';
    private $localeNativeName = 'Locale native name';
    private $plural = 'plural';

    private $configIOMock;

    private $io;

    public function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $this->configIOMock = new ConfigIOMock();
        Zibo::getInstance($browser, $this->configIOMock);

        $this->io = new ConfigLocaleIO();
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    /**
     * @dataProvider providerGetAllLocales
     */
    public function testGetAllLocales(Locale $locale, $options) {
        $this->configIOMock->setValues('l10n', array(
                $locale->getCode() => $options
            )
        );

        $result = $this->io->getAllLocales();
        $expected = array(
            $locale->getCode() => $locale
        );
        $this->assertEquals($expected, $result);
    }

    public function providerGetAllLocales() {
        $provider = array();

        $options = array(
            'name' => $this->localeName,
            'native' => $this->localeNativeName,
            'plural' => $this->plural,
        );
        $locale = new Locale($this->localeCode, $options['name'], $options['native'], $options['plural']);
        $provider[] = array($locale, $options);

        $options = array(
            'name' => $this->localeName,
            'native' => $this->localeNativeName,
            'plural' => $this->plural,
            'format' => array(
                'date' => array(
                    'default' => 'Y-m-d H:i:s',
                )
            )
        );
        $locale = new Locale($this->localeCode . '2', $options['name'], $options['native'], $options['plural']);
        $locale->setDateFormat('default', 'Y-m-d H:i:s');
        $provider[] = array($locale, $options);

        return $provider;
    }

    /**
     * @dataProvider providerGetAllLocales
     *
     * @param Locale $locale
     * @param array $options
     */
    public function testGetLocaleReturnsNullIfLocaleNotFound(Locale $locale, $options) {
        $this->configIOMock->setValues('l10n', array( $locale->getCode() => $options ));
        $nonExistingLocale = $this->io->getLocale('nonexistinglocalecode');
        $this->assertNull($nonExistingLocale);
        $existingLocale = $this->io->getLocale($locale->getCode());
        $result = $this->assertEquals($locale, $existingLocale);
    }
}