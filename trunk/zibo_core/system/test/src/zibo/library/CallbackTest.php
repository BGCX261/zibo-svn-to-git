<?php

namespace zibo\library;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

class CallbackTest extends BaseTestCase {

    private $invoked = false;

    /**
     * @dataProvider providerConstruct
     */
    public function testConstruct($expected, $callback) {
        $callback = new Callback($callback);
        $result = $callback->__toString();
        $this->assertEquals($expected, $result);
    }

    public function providerConstruct() {
        return array(
            array('str_replace', 'str_replace'),
            array('zibo\\library\\Url::getBaseUrl', array('zibo\\library\\Url', 'getBaseUrl')),
            array('zibo\\library\\CallbackTest->testConstruct', array($this, 'testConstruct')),
        );
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidCallbackPassed
     * @expectedException zibo\ZiboException
     */
    public function testConstructThrowsExceptionWhenInvalidCallbackPassed($callback) {
        new Callback($callback);
    }

    public function providerConstructThrowsExceptionWhenInvalidCallbackPassed() {
        return array(
            array(''),
            array(array('testClass', 'testFunction', '1 more')),
            array(array('object' => 'testClass', 'function' => 'testFunction')),
            array(array('test', '')),
            array(array('', 'test')),
            array(array('', $this)),
            array($this),
        );
    }

    public function testInvoke() {
        $this->invoked = false;
        $callback = new Callback(array($this, 'invoke'));
        $callback->invoke();
        $this->assertEquals(true, $this->invoked);
    }

    public function testInvokeReturnsValue() {
        $value = 'value';
        $callback = new Callback(array($this, 'invoke'));
        $result = $callback->invoke($value);
        $this->assertEquals($value, $result);
    }

    public function testInvokeWithArguments() {
        $this->invoked = false;
        $callback = new Callback(array($this, 'invoke'));
        $callback->invoke('test', true);

        $arguments = array('test', true);
        $this->assertEquals($arguments, $this->invoked);
    }

    /**
     * @dataProvider providerInvokeThrowsExceptionWhenUnableToInvokeCallback
     * @expectedException zibo\ZiboException
     */
    public function testInvokeThrowsExceptionWhenUnableToInvokeCallback($callback) {
        $callback = new Callback($callback);
        $callback->invoke();
    }

    public function providerInvokeThrowsExceptionWhenUnableToInvokeCallback() {
        return array(
            array('unexistingFunction'),
            array(array('unexistingClass', 'function')),
            array(array($this, 'unexistingFunction')),
        );
    }

    public function invoke() {
        $args = func_get_args();
        if (empty($args)) {
            $args = true;
        } elseif (count($args) == 1) {
            $args = array_shift($args);
        }

        return $this->invoked = $args;
    }

}