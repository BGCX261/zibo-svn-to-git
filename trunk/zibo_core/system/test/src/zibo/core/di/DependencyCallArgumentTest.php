<?php

namespace zibo\core\di;

use zibo\library\ObjectFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class DependencyCallArgumentTest extends BaseTestCase {

    public function testConstruct() {
        $argument = new DependencyCallArgument();

        $this->assertEquals(DependencyCallArgument::TYPE_NULL, $argument->getType());
        $this->assertNull($argument->getValue());
        $this->assertNull($argument->getDependencyId());

        $value = 'value';
        $argument = new DependencyCallArgument(DependencyCallArgument::TYPE_VALUE, $value);

        $this->assertEquals(DependencyCallArgument::TYPE_VALUE, $argument->getType());
        $this->assertEquals($value, $argument->getValue());
        $this->assertNull($argument->getDependencyId());

        $argument = new DependencyCallArgument(DependencyCallArgument::TYPE_DEPENDENCY, $value);

        $this->assertEquals(DependencyCallArgument::TYPE_DEPENDENCY, $argument->getType());
        $this->assertEquals($value, $argument->getValue());
        $this->assertNull($argument->getDependencyId());

        $dependencyId = 'id';
        $argument = new DependencyCallArgument(DependencyCallArgument::TYPE_DEPENDENCY, $value, $dependencyId);

        $this->assertEquals(DependencyCallArgument::TYPE_DEPENDENCY, $argument->getType());
        $this->assertEquals($value, $argument->getValue());
        $this->assertEquals($dependencyId, $argument->getDependencyId());
    }

    /**
     * @dataProvider providerSetTypeThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testSetTypeThrowsExceptionWhenInvalidValuePassed($type) {
        new DependencyCallArgument($type);
    }

    public function providerSetTypeThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array('invalid'),
            array(''),
            array(null),
            array(array()),
            array($this),
        );
    }

    /**
     * @dataProvider providerSetValueThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testSetValueThrowsExceptionWhenInvalidValuePassed($type, $value, $dependencyId) {
        new DependencyCallArgument($type, $value, $dependencyId);
    }

    public function providerSetValueThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array('dependency', null, null),
            array('dependency', array(), null),
            array('dependency', $this, null),
            array('dependency', 'value', array()),
            array('dependency', 'value', $this),
            array('config', null, null),
            array('config', array(), null),
            array('config', $this, null),
        );
    }

}