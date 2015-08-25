<?php

namespace zibo\xmlrpc\parser;

use zibo\test\BaseTestCase;

class ParameterParserTest extends BaseTestCase {

    private $parser;

    protected function setUp() {
        $this->parser = new ParameterParser();
    }

    /**
     * @dataProvider providerParse
     */
    public function testParse($expected, $value) {
        $result = $this->parser->parse($value);
        $this->assertEquals($expected, $result);
    }

    public function providerParse() {
        return array(
            array(array(42), '42'),
            array(array(true), 'true'),
            array(array(false), 'false'),
            array(array(null), 'null'),
            array(array("42"), '"42"'),
            array(array('answer, to everything'), '"answer, to everything"'),
            array(array('quote: "test"'), '"quote: \\"test\\""'),
            array(array(15, '', 'test'), '15, "", "test"'),
            array(array(15, ''), '15, ""'),
            array(array(array(42, 'test')), '[42, "test"]'),
            array(array(array(42, 'test, test2')), '[42, "test, test2"]'),
            array(array(array('value1' => 42, 'value2' => 'test')), '{value1: 42, value2: "test"}'),
            array(array(42, 'test'), '42, "test"'),
            array(array("42", 'test'), '"42", "test"'),
            array(array("42", 'test'), '"42","test"'),
            array(array(42, array('test', 'answer')), '42, ["test", "answer"]'),
            array(array(array('test' => 'system.listMethods', 'sme' => 42)), '{ test: "system.listMethods", sme: 42}'),
            array(array(array(array('methodName' => 'security.authenticate', 'params' => array('webservice', 'w3bs3rv1c3')))), '[{methodName: "security.authenticate", params: ["webservice", "w3bs3rv1c3"]}]'),
        );
    }

    /**
     * @dataProvider providerParseThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testParseThrowsExceptionWhenInvalidValuePassed($value) {
        $this->parser->parse($value);
    }

    public function providerParseThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array(''),
            array('"testinvalid'), // string not closed
            array('[42'), // array not closed
            array('{data: 42'), // struct not closed
            array('"test"invalid'), // string not closed nor escaped
            array('invalid value'), // no string delimiters
            array('{invalid value}'), // no key value separator
            array('{invalid value: 21}'), // invalid key
            array($this),
        );
    }

    /**
     * @dataProvider providerUnparse
     */
    public function testUnparse($expected, $value) {
        $result = $this->parser->unparse($value);
        $this->assertEquals($expected, $result);
    }

    public function providerUnparse() {
        return array(
            array('42', 42),
            array('true', true),
            array('false', false),
            array('null', null),
            array('"test"', 'test'),
            array('"answer, to everything"', 'answer, to everything'),
            array('"quote: \\"test\\""', 'quote: "test"'),
            array('"quote: \'test\'"', 'quote: \'test\''),
            array('[42, "test"]', array(42, 'test')),
            array('[42, "test, test2"]', array(42, 'test, test2')),
            array('{value1: 42, value2: "test"}', array('value1' => 42, 'value2' => 'test')),
            array('[42, ["test", "answer"]]', array(42, array('test', 'answer'))),
        );
    }

}