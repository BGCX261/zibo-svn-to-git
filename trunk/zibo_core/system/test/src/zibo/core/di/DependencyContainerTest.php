<?php

namespace zibo\core\di;

use zibo\library\ObjectFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class DependencyContainerTest extends BaseTestCase {

    public function testConstruct() {
        $container = new DependencyContainer();

        $this->assertNotNull($container);
        $this->assertEquals(array(), Reflection::getProperty($container, 'dependencies'));
    }

    public function testAddDependency() {
        $for = 'foo';
        $className = 'className';
        $dependency = new Dependency($className);
        $container = new DependencyContainer();

        $container->addDependency($for, $dependency);
        $expected = array($for => array(0 => $dependency));

        $this->assertEquals($expected, Reflection::getProperty($container, 'dependencies'));
        $this->assertEquals(0, $dependency->getId());

        $id = 'id';
        $dependency->setId($id);
        $container->addDependency($for, $dependency);
        $expected[$for][$id] = $dependency;

        $this->assertEquals($expected, Reflection::getProperty($container, 'dependencies'));

        $for = "bar";
        $container->addDependency($for, $dependency);
        $expected[$for][$id] = $dependency;

        $this->assertEquals($expected, Reflection::getProperty($container, 'dependencies'));

        $for = "foo";
        $dependency->setId();
        $container->addDependency($for, $dependency);
        $expected[$for][1] = $dependency;

        $this->assertEquals($expected, Reflection::getProperty($container, 'dependencies'));
        $this->assertEquals(1, $dependency->getId());
    }

    /**
     * @dataProvider providerAddDependencyThrowsExceptionWhenInvalidForProvided
     * @expectedException zibo\ZiboException
     */
    public function testAddDependencyThrowsExceptionWhenInvalidForProvided($for) {
        $container = new DependencyContainer();
        $container->addDependency($for, new Dependency('className'));
    }

    public function providerAddDependencyThrowsExceptionWhenInvalidForProvided() {
        return array(
            array(''),
            array(null),
            array(array()),
            array($this),
        );
    }

}