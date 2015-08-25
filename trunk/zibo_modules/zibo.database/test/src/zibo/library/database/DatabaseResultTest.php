<?php

namespace zibo\library\database;

use zibo\test\BaseTestCase;

class DatabaseResultTest extends BaseTestCase {

	protected $result;

	public function setUp() {
		$this->result = new DatabaseResult('sql');
	}

	/**
	 * @expectedException zibo\library\database\exception\DatabaseException
	 */
    public function testGetFirstThrowsExceptionWhenRowCountIsZero() {
        $this->result->getFirst();
    }

	/**
	 * @expectedException zibo\library\database\exception\DatabaseException
	 */
    public function testGetLastThrowsExceptionWhenRowCountIsZero() {
        $this->result->getLast();
    }

}