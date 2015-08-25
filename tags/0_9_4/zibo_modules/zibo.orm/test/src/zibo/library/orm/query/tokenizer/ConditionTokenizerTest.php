<?php

namespace zibo\library\orm\query\tokenizer;

use zibo\test\BaseTestCase;

class ConditionTokenizerTest extends BaseTestCase {

    private $tokenizer;

    protected function setUp() {
        $this->tokenizer = new ConditionTokenizer();
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
            array(array('{field} = 1'), '{field} = 1'),
            array(array('MAX({field}) = %2%'), 'MAX({field}) = %2%'),
            array(array('{field} = 1', 'OR', '{field} = 2'), '{field} = 1 OR {field} = 2'),
            array(array('{field} = %2%', 'AND', array('{field2} <= %1%', 'OR', '{field2} <= %2%')), '{field} = %2% AND ({field2} <= %1% OR {field2} <= %2%)'),
            array(array(array('{field2} <= %1%', 'OR', '{field2} <= %2%'), 'AND', '{field} = %2%'), '({field2} <= %1% OR {field2} <= %2%) AND {field} = %2%'),
            array(array('{field} = 5', 'AND', array(array('{field2} <= %1%'), 'OR', array('{field2} >= %2%'))), '{field} = 5 AND (({field2} <= %1%) OR ({field2} >= %2%))'),
            array(array(array(array('{room} = 0', 'OR', '{room} IS NULL'), 'AND', '{to} = 3')), '(({room} = 0 OR {room} IS NULL) AND {to} = 3)'),
            array(array('(LENGTH({parent}) - LENGTH(REPLACE({parent}, %2%, %3%))) <= %1%'), '(LENGTH({parent}) - LENGTH(REPLACE({parent}, %2%, %3%))) <= %1%'),
            array(array('(LENGTH({parent}) - LENGTH(REPLACE({parent}, %2%, %3%))) <= %1%', 'AND', array('{field} = 5', 'OR', '{field} IN (6, 7, 8)')), '(LENGTH({parent}) - LENGTH(REPLACE({parent}, %2%, %3%))) <= %1% AND ({field} = 5 OR {field} IN (6, 7, 8))'),
        );
    }

}