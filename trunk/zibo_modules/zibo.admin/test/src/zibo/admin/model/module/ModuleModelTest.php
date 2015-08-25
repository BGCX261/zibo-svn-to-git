<?php

namespace zibo\admin\model\module;

use zibo\core\Zibo;

use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ModuleModelTest extends BaseTestCase {

    private $model;

    protected function setUp() {
        $configIO = new ConfigIOMock();
        $browser = $this->getMock('zibo\\library\\filesystem\\browser\\Browser');
        Zibo::getInstance($browser, $configIO);

        $this->model = new ModuleModel();
    }

    protected function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testAddModule() {
        $namespace = 'namespace';
        $name = 'name';

        $module = new Module($namespace, $name, '1.0.0', '0.1.0');
        $this->model->addModules(array($module));

        $modules = Reflection::getProperty($this->model, 'modules');

        $this->assertNotNull($modules);
        $this->assertTrue(isset($modules[$namespace][$name]));
        $this->assertEquals($module, $modules[$namespace][$name]);
    }

    /**
     * @expectedException zibo\admin\model\module\exception\ModuleZiboVersionNeededException
     */
    public function testAddModuleThrowsExceptionWhenZiboVersionIsEmpty() {
        $module = new Module('namespace', 'name', '1.0.0');
        $this->model->addModules(array($module));
    }

    /**
     * @expectedException zibo\admin\model\module\exception\ModuleZiboVersionNotInstalledException
     */
    public function testAddModuleThrowsExceptionWhenZiboVersionIsTooHigh() {
        $module = new Module('namespace', 'name', '1.0.0', '10.0.0');
        $this->model->addModules(array($module));
    }

    /**
     * @expectedException zibo\admin\model\module\exception\ModuleDependencyNotInstalledException
     */
    public function testAddModuleThrowsExceptionWhenDependencyNotInstalled() {
        $dependency = new Module('namespace', 'dependency', '1.0.0');
        $module = new Module('namespace', 'name', '1.0.0', '0.1.0', array($dependency));

        $this->model->addModules(array($module));
    }

    /**
     * @expectedException zibo\admin\model\module\exception\ModuleDependencyVersionNotInstalledException
     */
    public function testAddModuleThrowsExceptionWhenDependencyInstalledButVersionNotTooLow() {
        $dependency = new Module('namespace', 'dependency', '1.0.0', '0.1.0');
        $this->model->addModules(array($dependency));

        $dependency = new Module('namespace', 'dependency', '2.0.0');
        $module = new Module('namespace', 'name', '0.1.0', '0.1.0', array($dependency));

        $this->model->addModules(array($module));
    }

    public function testAddModules() {
        $namespace = 'namespace';
        $name1 = 'name1';
        $name2 = 'name2';
        $name3 = 'name2';

        $dependency1 = new Module($namespace, $name1, '1.0.0');
        $dependency2 = new Module($namespace, $name2, '1.0.0');
        $module3 = new Module($namespace, $name3, '0.1.0', '0.1.0', array($dependency1, $dependency2));
        $module2 = new Module($namespace, $name2, '1.0.0', '0.1.0', array($dependency1));
        $module1 = new Module($namespace, $name1, '1.0.0', '0.1.0');

        $modules = array($module1, $module2, $module3);
        $this->model->addModules($modules);

        $modules = Reflection::getProperty($this->model, 'modules');

        $this->assertNotNull($modules);
        $this->assertTrue(isset($modules[$namespace][$name1]));
        $this->assertTrue(isset($modules[$namespace][$name2]));
        $this->assertTrue(isset($modules[$namespace][$name3]));
    }

    public function testAddModuleUpdatesUsage() {
        $namespace = 'namespace';
        $name1 = 'name1';
        $name2 = 'name2';

        $dependency1 = new Module($namespace, $name1, '1.0.0');
        $module2 = new Module($namespace, $name2, '1.0.0', '0.1.0', array($dependency1));
        $module1 = new Module($namespace, $name1, '1.0.0', '0.1.0');

        $modules = array($module1, $module2);
        $this->model->addModules($modules);

        $modules = Reflection::getProperty($this->model, 'modules');

        $module1 = $modules[$namespace][$name1];
        $usage = $module1->getUsage();

        $this->assertNotNull($usage);
        $this->assertTrue(count($usage) == 1);

        $dependency = array_shift($usage);
        $this->assertEquals($module2->getNamespace(), $dependency->getNamespace());
        $this->assertEquals($module2->getName(), $dependency->getName());
    }

    public function testRemoveModule() {
        $namespace = 'namespace';
        $name1 = 'name1';
        $name2 = 'name2';

        $dependency1 = new Module($namespace, $name1, '1.0.0');
        $module2 = new Module($namespace, $name2, '1.0.0', '0.1.0', array($dependency1));
        $module1 = new Module($namespace, $name1, '1.0.0', '0.1.0');

        $modules = array($module1, $module2);
        $this->model->addModules($modules);

        $this->model->removeModules(array($module2));

        $modules = Reflection::getProperty($this->model, 'modules');

        $this->assertNotNull($modules);
        $this->assertFalse(isset($modules[$namespace][$name2]));
        $this->assertTrue(isset($modules[$namespace][$name1]));
    }

    /**
     * @expectedException zibo\admin\model\module\exception\ModuleStillInUseException
     */
    public function testRemoveModuleThrowsExceptionWhenModuleIsUsedByAnotherModule() {
        $namespace = 'namespace';
        $name1 = 'name1';
        $name2 = 'name2';

        $dependency1 = new Module($namespace, $name1, '1.0.0');
        $module2 = new Module($namespace, $name2, '1.0.0', '0.1.0', array($dependency1));
        $module1 = new Module($namespace, $name1, '1.0.0', '0.1.0');

        $modules = array($module1, $module2);
        $this->model->addModules($modules);

        $this->model->removeModules(array($module1));
    }

}