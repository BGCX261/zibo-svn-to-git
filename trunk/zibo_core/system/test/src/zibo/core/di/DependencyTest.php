<?php

namespace zibo\core\di;

use zibo\library\ObjectFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class DependencyTest extends BaseTestCase {

    public function testSetClassName() {
        $className = 'className';
        $dependency = new Dependency($className);
        $this->assertEquals($className, Reflection::getProperty($dependency, 'className'));
        $this->assertNull(Reflection::getProperty($dependency, 'id'));
        $this->assertNull(Reflection::getProperty($dependency, 'constructorArguments'));
        $this->assertNull(Reflection::getProperty($dependency, 'calls'));
    }

    /**
     * @dataProvider providerSetClassNameThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testSetClassNameThrowsExceptionWhenInvalidValuePassed($value) {
        $dependency = new Dependency('className');
        $dependency->setClassName($value);
    }

    public function providerSetClassNameThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array(''),
            array(null),
            array(array()),
            array($this),
        );
    }

    public function testGetClassName() {
        $className = 'className';

        $dependency = new Dependency($className);

        $this->assertEquals($className, $dependency->GetClassName());
    }

    public function testSetId() {
        $id = 'id';
        $dependency = new Dependency('className', $id);
        $this->assertEquals($id, Reflection::getProperty($dependency, 'id'));
    }

    /**
     * @dataProvider providerSetIdThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testSetIdThrowsExceptionWhenInvalidValuePassed($value) {
        $dependency = new Dependency('className');
        $dependency->setId($value);
    }

    public function providerSetIdThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array(''),
            array(array()),
            array($this),
        );
    }

    public function testGetId() {
        $id = 'id';

        $dependency = new Dependency('className', $id);

        $this->assertEquals($id, $dependency->getId());
    }

    public function testAddCall() {
        $dependency = new Dependency('className');
        $call = new DependencyCall('methodName');

        $dependency->addCall($call);
        $expected = array($call);

        $this->assertEquals($expected, $dependency->getCalls());

        $dependency->addCall($call);
        $expected[] = $call;

        $this->assertEquals($expected, $dependency->getCalls());
    }

    public function testAddCallWithConstructorCallWillNotAddCallButSetConstructorArguments() {
        $dependency = new Dependency('className');
        $call = new DependencyCall('__construct');

        $dependency->addCall($call);

        $this->assertNull($dependency->getCalls());
        $this->assertNull($dependency->getConstructorArguments());

        $argument = new DependencyCallArgument();

        $call->addArgument($argument);

        $dependency->addCall($call);
        $expected = array($argument);

        $this->assertNull($dependency->getCalls());
        $this->assertEquals($expected, $dependency->getConstructorArguments());
    }

}