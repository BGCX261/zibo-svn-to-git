<?php

namespace zibo\admin\model\module\io;

use zibo\core\Zibo;

use zibo\admin\model\module\Module;

use zibo\library\config\ini\IniIO;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

use \ReflectionObject;

use \DOMDocument;

class ModuleXmlIOTest extends BaseTestCase {

    public function setUp() {
        $browser = $this->getMock('zibo\\library\\filesystem\\browser\\Browser');
        $browser->expects($this->any())->method('getIncludePaths')->will($this->returnValue(array()));
        $configIO = new ConfigIOMock();
        Zibo::getInstance($browser, $configIO);

        $this->setUpApplication(__DIR__ . '/../../../../../../application');
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);

        if (file_exists('application/config/modules.temp.xml')) {
            unlink('application/config/modules.temp.xml');
        }

        $this->tearDownApplication();
    }

    public function testReadModules() {
        $smarty = new Module('zibo', 'smarty', '1.0.0');
        $xml = new Module('zibo', 'xml', '1.0.0');

        $admin = new Module('zibo', 'admin', '0.1.0', '0.1.0', array($smarty, $xml));
        $xml = new Module('zibo', 'xml', '1.0.0', '0.1.0');

        $expected = array($admin, $xml);

        $io = new XmlModuleIO();
        $modules = $io->readModules(new File('application/config'));

        $this->assertEquals($expected, $modules);
    }

    /**
     * @expectedException zibo\admin\model\module\exception\ModuleDefinitionNotFoundException
     */
    public function testReadModulesThrowsExceptionWhenModuleFileDoesNotExist() {
        $io = new XmlModuleIO();
        $io->readModules(new File('system'));
    }

    public function testWriteModules() {
        $smarty = new Module('zibo', 'smarty', '1.0.0');
        $xml = new Module('zibo', 'xml', '1.0.0');

        $admin = new Module('zibo', 'admin', '0.1.0', '0.1.0', array($smarty, $xml));
        $xml = new Module('zibo', 'xml', '1.0.0', '0.1.0');

        $modules = array('zibo' => array('admin' => $admin, 'xml' => $xml));
        $path = new File('application/data');

        $io = new XmlModuleIO();
        $io->writeModules($path, $modules);

        $file = new File($path, XmlModuleIO::MODULE_FILE);

        $this->assertXmlFileEqualsXmlFile('application/config/module.xml', $file->getPath());
    }

}