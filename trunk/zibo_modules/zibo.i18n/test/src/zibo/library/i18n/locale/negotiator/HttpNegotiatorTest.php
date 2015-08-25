<?php

namespace zibo\library\i18n\locale\negotiator;

use zibo\core\filesystem\GenericFileBrowser;
use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\i18n\locale\io\IOMock;
use zibo\library\i18n\locale\Locale;
use zibo\library\i18n\locale\LocaleManager;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class HttpNegotiatorTest extends BaseTestCase {

    public function setUp() {
        $browser = new GenericFileBrowser(new File(getcwd()));

        $configIO = new ConfigIOMock();
        $configIO->setValues('i18n', array(
            'locale' => array(
                'order' => 'en,du,fr',
            )
        ));

        $this->zibo = new Zibo($browser, $configIO);

        $io = new IOMock();
        $this->manager = new LocaleManager($io);

        $this->negotiator = new HttpNegotiator($this->zibo);

    }

    /**
     * @dataProvider providerGetLocale
     */
    public function testGetLocale($test, $expected) {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $test;

        $this->zibo->setRequest(new Request('baseUrl', 'basePath', 'controller'));

        $locale = $this->negotiator->getLocale($this->manager);

        if ($locale !== null) {
            $this->assertTrue($locale instanceof Locale);
            $this->assertEquals($expected, $locale->getCode());
        } else if ($expected !== null){
            $this->fail('Negotiator was not expected to return null, expected: ' . $expected);
        }
    }

    public function providerGetLocale() {
        return array(
           array('en', 'en'),
           array('es,en', 'en'),
           array('es,en-us;q=0.7,ar-lb;q=0.3', 'en'),
           array('en-us;du-nl;q=0.7,fr:q=0.3', 'en'),
           array('nl,en-us;q=0.7', 'nl'),
           array('en-gb,en-us;q=0.7,en', 'en_GB'),
           array('es,it', null),
        );
    }

}