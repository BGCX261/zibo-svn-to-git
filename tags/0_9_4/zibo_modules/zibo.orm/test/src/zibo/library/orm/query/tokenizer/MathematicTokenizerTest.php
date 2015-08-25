<?php

namespace zibo\library\orm\query\tokenizer;

use zibo\test\BaseTestCase;

class MathematicTokenizerTest extends BaseTestCase {

    private $tokenizer;

    protected function setUp() {
        $this->tokenizer = new MathematicTokenizer();
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
            array(array('5', '+', '10'), '5 + 10'),
            array(array('5', '+', '10 - 9'), '5 + (10 - 9)'),
            array(array('MAX(2 + 3)'), 'MAX(2 + 3)'),
            array(array('MIN(1)', '+', '5'), 'MIN(1) + 5'),
            array(array('MIN(1)', '+', 'MAX(2 + 3)'), 'MIN(1) + MAX(2 + 3)'),
            array(array('MIN(1)', '+', 'MAX(2 + 3)'), '(MIN(1) + MAX(2 + 3))'),
            array(array('MIN(1)', '+', 'MAX(2 + 3)'), '(((MIN(1) + MAX(2 + 3))))'),
            array(array('MIN(1) + MAX(2 + 3)', '-', '5'), '((MIN(1) + MAX(2 + 3)) - 5)'),
        );
    }

}