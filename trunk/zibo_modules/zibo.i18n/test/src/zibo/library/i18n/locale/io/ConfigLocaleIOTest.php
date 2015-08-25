<?php

namespace zibo\library\i18n\locale\io;

use zibo\core\filesystem\GenericFileBrowser;
use zibo\core\Zibo;

use zibo\library\filesystem\File;

use zibo\library\i18n\I18n;
use zibo\library\i18n\locale\Locale;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ConfigLocaleIOTest extends BaseTestCase {

    private $code = 'locale';

    private $name = 'Locale name';

    private $plural = 'plural';

    private $configIOMock;

    private $io;

    public function setUp() {
        $this->configIOMock = new ConfigIOMock();

        $browser = new GenericFileBrowser(new File(getcwd()));

        $zibo = new Zibo($browser, $this->configIOMock);

        $this->io = new ConfigLocaleIO($zibo);
    }

    /**
     * @dataProvider providerGetLocales
     */
    public function testGetLocales(Locale $locale, $options) {
        $this->configIOMock->setValues('l10n', array(
                $locale->getCode() => $options
            )
        );

        $result = $this->io->getLocales();

        $expected = array(
            $locale->getCode() => $locale
        );

        $this->assertEquals($expected, $result);
    }

    public function providerGetLocales() {
        $provider = array();

        $options = array(
            'name' => $this->name,
            'plural' => $this->plural,
        );

        $locale = new Locale($this->code, $options['name'], $options['plural']);

        $provider[] = array($locale, $options);

        $options = array(
            'name' => $this->name,
            'plural' => $this->plural,
            'format' => array(
                'date' => array(
                    'default' => 'Y-m-d H:i:s',
                )
            )
        );
        $locale = new Locale($this->code . '2', $options['name'], $options['plural']);
        $locale->setDateFormat('default', 'Y-m-d H:i:s');

        $provider[] = array($locale, $options);

        return $provider;
    }

}