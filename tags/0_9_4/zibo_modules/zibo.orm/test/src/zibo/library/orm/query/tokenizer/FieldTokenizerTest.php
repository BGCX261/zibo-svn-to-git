<?php

namespace zibo\library\orm\query\tokenizer;

use zibo\test\BaseTestCase;

class FieldTokenizerTest extends BaseTestCase {

    private $tokenizer;

    protected function setUp() {
        $this->tokenizer = new FieldTokenizer();
    }

    /**
     * @dataProvider providerTokenize
     */
    public function testTokenize($expected, $value) {
        $result = $this->tokenizer->tokenize($value);

        $this->assertEquals($expected, $result);
    }

    public function providerTokenize() {
        return array(
            array(array('{field}'), '{field}'),
            array(array('{field}', '{field2}', '{field3} AS f3'), '{field}, {field2}, {field3} AS f3'),
            array(array('5'), '5'),
            array(array('5 + 10'), '5 + 10'),
            array(array('MAX(2 + 3)'), 'MAX(2 + 3)'),
            array(array('{field}', 'MAX(2 + 3)'), '{field}, MAX(2 + 3)'),
            array(array('{field}', 'MAX(2 + 3)', '{field2}'), '{field}, MAX(2 + 3), {field2}'),
            array(array('{field}', 'MAX(2 + 3, 7)', '{field2}'), '{field}, MAX(2 + 3, 7), {field2}'),
            array(array('LENGTH({parent}) - LENGTH(REPLACE({parent}, %1%, %2%)) AS levels'), 'LENGTH({parent}) - LENGTH(REPLACE({parent}, %1%, %2%)) AS levels'),
        );
    }

}