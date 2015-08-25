<?php

namespace zibo\library\tokenizer\symbol;

use zibo\test\BaseTestCase;

class SimpleSymbolTest extends BaseTestCase {

	/**
     * @dataProvider provideTokenize
	 */
	public function testTokenize($expected, $process, $toProcess, $willIncludeSymbols) {
		$symbol = new SimpleSymbol('AND', $willIncludeSymbols);

        $result = $symbol->tokenize($process, $toProcess);

		$this->assertEquals($expected, $result);
	}

	public function provideTokenize() {
	    return array(
	       array(array('test', 'AND'), 'testAND', 'testANDtest', true),
	       array(null, 'test', 'testANDtest', true),
	       array(array('AND'), 'AND', 'ANDtest', true),
	       array(array('test'), 'testAND', 'testANDtest', false),
	       array(null, 'test', 'testANDtest', false),
	       array(array(), 'AND', 'ANDtest', false),
	    );
	}

}