<?php

namespace zibo\library\i18n\locale\negotiator;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;

use zibo\library\filesystem\File;

use zibo\library\i18n\locale\io\IOMock;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class HttpNegotiatorTest extends BaseTestCase {

    public function setUp() {
        $this->io = new IOMock();
        $this->negotiator = new HttpNegotiator();

        $browser = new GenericBrowser(new File(getcwd()));

        $configIO = new ConfigIOMock();
        $configIO->setValues('i18n', array(
            'locale' => array(
                'order' => 'en,du,fr',
            )
        ));

        $this->zibo = Zibo::getInstance($browser, $configIO);
    }

    /**
     * @dataProvider providerGetLocale
     */
    public function testGetLocale($test, $expected) {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $test;

        $locale = $this->negotiator->getLocale($this->io);
        if ($locale !== null) {
            $this->assertType('zibo\\library\\i18n\\locale\\Locale', $locale);
            $this->assertEquals($expected, $locale->getCode());
        } else if ($expected !== null){
            $this->fail('Negotiator was not expected to return null, expected: ' . $expected);
        }
    }

    public function tearDown() {
        Reflection::setProperty($this->zibo, 'instance', null);
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