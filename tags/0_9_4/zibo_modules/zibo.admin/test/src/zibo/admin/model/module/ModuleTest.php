<?php

namespace zibo\admin\model\module;

use zibo\test\BaseTestCase;

class ModuleTest extends BaseTestCase {

    public function testConstruct() {
        $module = new Module('zibo', 'admin', '0.1.0', '0.1.0');
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenNamespaceIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenNamespaceIsInvalid($namespace) {
        new Module($namespace, 'admin', '0.1.0', '0.1.0');
    }

    public function providerConstructThrowsExceptionWhenNamespaceIsInvalid() {
        return array(
            array(''),
            array(null),
            array($this),
        );
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenNameIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenNameIsInvalid($name) {
        new Module('zibo', $name, '0.1.0', '0.1.0');
    }

    public function providerConstructThrowsExceptionWhenNameIsInvalid() {
        return array(
            array(''),
            array(null),
            array($this),
        );
    }

    /**
     *
     * @dataProvider providerConstructThrowsExceptionWhenVersionIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenVersionIsInvalid($version) {
        $module = new Module('zibo', 'admin', $version, '0.1.0');
    }

    public function providerConstructThrowsExceptionWhenVersionIsInvalid() {
        return array(
            array(''),
            array(null),
            array($this),
        );
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenDependenciesIsInvalid
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenDependenciesIsInvalid($dependencies) {
        $module = new Module('zibo', 'admin', '0.1.0', '0.1.0', $dependencies);
    }

    public function providerConstructThrowsExceptionWhenDependenciesIsInvalid() {
        return array(
            array(array('test')),
        );
    }

}