<?php

namespace zibo\core\module;

use zibo\core\Zibo;

use zibo\test\mock\ConfigIOMock;
use zibo\test\Reflection;
use zibo\test\BaseTestCase;

class ConfigModuleLoaderTest extends BaseTestCase {

    private $configIO;

    private $zibo;

    private $configModuleLoader;

    public function setUp() {
        $fileBrowser = $this->getMock('zibo\\core\\filesystem\\FileBrowser');
        $this->configIO = new ConfigIOMock();
        $this->zibo = new Zibo($fileBrowser, $this->configIO);
        $this->configModuleLoader = new ConfigModuleLoader();
    }

    public function testLoadModules() {
        $this->configIO->setValues(ConfigModuleLoader::CONFIG_MODULE, array(
            'namespace' => array(
                'name' => array(
                    'subname' => 'zibo\\core\\module\\TestModule',
                ),
            ),
        ));

        $result = $this->configModuleLoader->loadModules($this->zibo);

        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
        $this->assertEquals(array(new TestModule()), $result);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testLoadModulesThrowsExceptionWhenInvalidModuleDefined() {
        $this->configIO->setValues(ConfigModuleLoader::CONFIG_MODULE, array(
            'namespace' => array(
                'name' => array(
                    'subname' => 'zibo\\core\\module\\ConfigModuleLoader',
                ),
            ),
        ));

        $this->configModuleLoader->loadModules($this->zibo);
    }

}

class TestModule implements Module {

    public function boot(Zibo $zibo) {

    }

}