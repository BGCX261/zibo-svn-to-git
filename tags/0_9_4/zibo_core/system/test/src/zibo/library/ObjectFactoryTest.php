<?php

namespace zibo\library;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class ObjectFactoryTest extends BaseTestCase {

    /**
     * @var ObjectFactory
     */
    private $factory;

    protected function setUp() {
        $this->factory = new ObjectFactory();
    }

    public function testCreate() {
        $object = $this->factory->create('zibo\\library\\ObjectFactory');
        $this->assertNotNull($object, 'Result is null');
        $this->assertTrue($object instanceof ObjectFactory, 'Result is not an instance of the requested class');
    }

    public function testCreateThrowsExceptionWhenProvidedClassDoesNotExtendsNeededClass() {
        try {
            $this->factory->create('zibo\\library\\ObjectFactory', 'zibo\\library\\String');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    public function testCreateThrowsExceptionWhenProvidedClassDoesNotImplementNeededClass() {
        try {
            $this->factory->create('zibo\\library\\ObjectFactory', 'zibo\\core\\Controller');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    public function testCreateNonExistingClassThrowsException() {
        try {
            $this->factory->create('nonExistingClass');
            $this->fail('Exception expected for creating instance of non existing class');
        } catch (ZiboException $e) {
        }
    }

}