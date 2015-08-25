<?php

namespace zibo\library\config\io\ini;

use zibo\core\Autoloader;
use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\Reflection;
use zibo\test\BaseTestCase;

use zibo\ZiboException;

class IniConfigIOTest extends BaseTestCase {

    private $ini;

    protected function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../../../../application');

        // testRead() also needs config from "modules" dir in test environment
        $ziboRoot = new File(__DIR__ . '/../../../../../..');
        $browser = new GenericBrowser($ziboRoot);

        $environment = $this->getMock('zibo\\core\\environment\\Environment');
        //$zibo->setEnvironment($environment);

        $this->ini = new IniConfigIO($environment, $browser);
    }

    protected function tearDown() {
        $this->tearDownApplication();

        try {
            Reflection::setProperty(Zibo::getInstance(), 'instance', null);
        } catch (ZiboException $e) {

        }
    }

    public function testRead() {
        $expected = array('key' => 'value', 'key2' => 'value');
        $values = $this->ini->read('file');
        $this->assertEquals($expected, $values);
    }

    public function testReadWithSections() {
        $expected = array(
            'section1' => array(
                'key' => array(
                    'subkey1' => 'value',
                    'subkey2' => 'subvalue',
                ),
            ),
            'section2' => array(
                'key' => 'value',
            ),
        );
        $values = $this->ini->read('sections');
        $this->assertEquals($expected, $values);
    }

    public function testReadWithVariables() {
        $values = $this->ini->read('variables');
        $this->assertTrue(isset($values['key']));
        $this->assertNotEquals('%path%/config', $values['key']);
    }

    public function testReadAll() {
        $file = array(
            'key' => 'value',
            'key2' => 'value',
        );
        $sections = array(
            'section1' => array(
                'key' => array(
                    'subkey1' => 'value',
                    'subkey2' => 'subvalue',
                ),
            ),
            'section2' => array(
                'key' => 'value',
            )
        );
        $variables = array(
            'key' => '%oath%/config',
        );

        $config = $this->ini->readAll();
        $this->assertTrue(isset($config['file']));
        $this->assertEquals($file, $config['file']);
        $this->assertTrue(isset($config['sections']));
        $this->assertEquals($sections, $config['sections']);
        $this->assertTrue(isset($config['variables']));
        $this->assertTrue(isset($config['variables']['key']));
        $this->assertTrue(!empty($config['variables']['key']));
        $this->assertTrue($config['variables']['key'] != $variables['key']);
    }

    /**
     * @expectedException zibo\library\config\exception\ConfigException
     */
    public function testReadThrowsExceptionWhenFileNameIsEmpty() {
        $this->ini->read('');
    }

    public function testWrite() {
        $this->ini->write('test.setting', '123');

        $this->assertFileEquals('application/data/config/testWrite.ini', 'application/config/test.ini');
    }

    public function testWriteOverwritesExisting() {
        $this->ini->write('test.setting', '123');
        $this->ini->write('test.setting', '456');

        $this->assertFileEquals('application/data/config/testOverwrite.ini', 'application/config/test.ini');
    }

    public function testWriteConvertsBooleansTo0And1() {
        $this->ini->write('test.setting', true);
        $this->ini->write('test.setting_bis', false);

        $this->assertFileEquals('application/data/config/testWriteHandlesBooleans.ini', 'application/config/test.ini');
    }

    public function testWriteEscapesSpecialCharacters() {
        $string = '"\\' . "\ntesting new lines";
        $this->ini->write('test.setting', $string);

        $this->assertFileEquals('application/data/config/testWriteEscapesSpecialCharacters.ini', 'application/config/test.ini');
    }

    public function testWriteNullValueRemovesKey() {
        $this->ini->write('test.setting1', '123');
        $this->ini->write('test.setting2', '456');

        $this->assertFileEquals('application/data/config/testNullBefore.ini', 'application/config/test.ini');

        $this->ini->write('test.setting2', null);

        $this->assertFileEquals('application/data/config/testNullAfter.ini', 'application/config/test.ini');
    }
}