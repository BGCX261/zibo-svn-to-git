<?php

namespace zibo\library\orm\definition;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class DataFormatTest extends BaseTestCase {

	public function testConstruct() {
		$name = 'name';
		$format = 'format';

		$dataFormat = new DataFormat($name, $format);

		$this->assertEquals($name, Reflection::getProperty($dataFormat, 'name'));
		$this->assertEquals($format, Reflection::getProperty($dataFormat, 'format'));
	}

	/**
	 * @dataProvider providerConstructThrowsExceptionWithInvalidNameOrFormat
	 * @expectedException zibo\ZiboException
	 */
	public function testConstructThrowsExceptionWithInvalidNameOrFormat($name, $format) {
	    new DataFormat($name, $format);
	}

	public function providerConstructThrowsExceptionWithInvalidNameOrFormat() {
	    return array(
	       array('name', null),
	       array('name', ''),
	       array('name', $this),
	       array(null, 'format'),
	       array('', 'format'),
	       array($this, 'format'),
	    );
	}

}