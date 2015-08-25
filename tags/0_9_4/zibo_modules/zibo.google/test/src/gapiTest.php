<?php

namespace zibo\library\google;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \gapi;

class gapiTest extends BaseTestCase {

	/**
	 * @expectedException Exception
	 */
	public function testConstruct() {
	    $gapi = new gapi('username', 'password');
	}

}