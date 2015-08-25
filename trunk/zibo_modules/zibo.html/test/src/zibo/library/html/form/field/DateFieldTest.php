<?php

namespace zibo\library\html\form\field;

use zibo\core\Zibo;

use zibo\library\config\ini\IniIO;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class DateFieldTest extends BaseTestCase {

    public function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $environment = $this->getMock('zibo\\core\\environment\\Environment');

        // we need mock the i18n subsystem through the configuration
        // as the datetime field currently relies on the locale to parse and format its value
        $configIOMock = new ConfigIOMock();

        $i18nConfigValues = array(
            'locale' => array(
                'io' => 'zibo\\library\\i18n\\locale\\io\\IOMock',
                'order' => 'en, nl',
             ),
        );
        $configIOMock->setValues('i18n', $i18nConfigValues);

        $zibo = Zibo::getInstance($browser, $configIOMock);
        $zibo->setEnvironment($environment);
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testGetHtmlDoesNotContainValueAttributeTwice() {
        $field = new DateField('test', time());
        $html = $field->getHtml();
        $this->assertEquals(1, preg_match_all('/\svalue\="/', $html, $matches), 'value attribute occurs more than once');
    }

    public function testGetHtmlGeneratesIdIfNotSetYet() {
        $field = new DateField('test');
        $html = $field->getHtml();
        $this->assertContains('id="', $html);
    }

    public function testGetProcessedValueHasNoHoursMinutesOrSeconds() {
        $_REQUEST['test'] = '15/01/2009';
        $time = mktime(0, 0, 0, 15, 1, 2009);
        $field = new DateField('test');
        $field->processRequest();
        $value = $field->getValue();
        $this->assertEquals($time, $value);
    }

}