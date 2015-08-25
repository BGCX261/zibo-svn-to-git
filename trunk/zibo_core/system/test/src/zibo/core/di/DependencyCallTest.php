<?php

namespace zibo\core\di;

use zibo\library\ObjectFactory;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class DependencyCallTest extends BaseTestCase {

    public function testConstruct() {
        $methodName = 'methodName';

        $call = new DependencyCall($methodName);

        $this->assertEquals($methodName, $call->getMethodName());

        $arguments = $call->getArguments();

        $this->assertNull($arguments);
    }

    public function testArguments() {
        $argument = new DependencyCallArgument();
        $call = new DependencyCall('methodName');

        $call->addArgument($argument);
        $expected = array($argument);

        $this->assertEquals($expected, $call->getArguments());

        $call->addArgument($argument);
        $expected[] = $argument;

        $this->assertEquals($expected, $call->getArguments());

        $call->clearArguments();

        $this->assertNull($call->getArguments());
    }

}