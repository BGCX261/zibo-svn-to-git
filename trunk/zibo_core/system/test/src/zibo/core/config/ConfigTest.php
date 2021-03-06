<?php

namespace zibo\core\config;

use zibo\test\mock\ConfigIOMock;
use zibo\test\Reflection;
use zibo\test\BaseTestCase;

class ConfigTest extends BaseTestCase {

    /**
     * @var Config
     */
    private $config;

    private $ioMock;

    private $key;
    private $value;
    private $mockFile;
    private $mockResult;
    private $mockValue;

    protected function setUp() {
        $this->key = 'file.section';
        $this->value = array(
            'key' => array(
                '1' => 'value',
                '2' => 'value',
            ),
        );
        $this->mockFile = 'file';
        $this->mockResult = array(
            'section' => array(
                'key' => array(
                    '1' => 'value',
                    '2' => 'value',
                ),
            ),
        );
        $this->mockValue = 'test';

        $this->ioMock = $this->getMock('zibo\\core\\config\\io\\ConfigIO');
        $this->config = new Config($this->ioMock);
    }

    protected function setMockReadExpectation($expectation) {
        $this->ioMock
               ->expects($expectation)
               ->method('read')
               ->with($this->equalTo($this->mockFile))
               ->will($this->returnValue($this->mockResult));
    }

    protected function setMockWriteExpectation($expectation) {
        $this->ioMock
               ->expects($expectation)
               ->method('write')
               ->with($this->equalTo($this->key), $this->equalTo($this->mockValue));
    }

    public function testGetValue() {
        $this->setMockReadExpectation($this->once());
        $value = $this->config->get($this->key);
        $this->assertEquals($this->value, $value);
        $value = $this->config->get('file.section.key.1');
        $this->assertEquals('value', $value);
    }

    public function testGetValueTwiceReadsFileOnlyOnce() {
        $this->setMockReadExpectation($this->once());
        $this->config->get($this->key);
        $this->config->get($this->key);
    }

    /**
     * @expectedException zibo\core\config\exception\ConfigException
     */
    public function testGetValueThrowsExceptionWhenKeyIsEmpty() {
        $this->config->get('');
    }

    public function testGetDefaultValueWhenKeyDoesNotExists() {
        $this->setMockReadExpectation($this->once());
        $value = $this->config->get('file.unknown', $this->value);
        $this->assertEquals($this->value, $value);
    }

    public function testSetValue() {
        $this->setMockWriteExpectation($this->once());
        $this->config->set($this->key, $this->mockValue);
        $value = $this->config->get($this->key);
        $this->assertEquals($this->mockValue, $value);
    }

    public function testSetValueNullRemovesKey() {
        $default = 'default';
        $this->mockValue = null;

        $this->setMockWriteExpectation($this->once());
        $this->config->set($this->key, $this->mockValue);

        $value = $this->config->get($this->key, $default);
        $this->assertEquals($default, $value);
    }

    public function testParseConfigTree() {
        $configTree = array(
            "section1" => array(
                "type1" => 1,
                "type2" => array(
                    "subtype1" => 'value',
                    "subtype2" => 'value',
                ),
                "type3" => "testValue",
            ),
            "section2" => array(
                "type1" => 1,
            ),
        );

        $configList = Config::parseConfigTree($configTree);

        $expected = array(
            'section1.type1' => 1,
            'section1.type2.subtype1' => 'value',
            'section1.type2.subtype2' => 'value',
            'section1.type3' => 'testValue',
            'section2.type1' => 1,
        );

        $this->assertEquals($expected, $configList);
    }

}