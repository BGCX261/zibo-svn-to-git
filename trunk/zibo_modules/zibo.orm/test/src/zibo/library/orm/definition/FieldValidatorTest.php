<?php

namespace zibo\library\orm\definition;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class FieldValidatorTest extends BaseTestCase {

	public function testConstruct() {
		$name = 'name';
		$options = array('option' => 'value');

		$fieldValidator = new FieldValidator($name, $options);

		$this->assertEquals($name, Reflection::getProperty($fieldValidator, 'name'));
		$this->assertEquals($options, Reflection::getProperty($fieldValidator, 'options'));
	}

	/**
	 * @dataProvider providerConstructThrowsExceptionWithInvalidName
	 * @expectedException zibo\ZiboException
	 */
	public function testConstructThrowsExceptionWithInvalidName($name) {
	    new FieldValidator($name, array());
	}

	public function providerConstructThrowsExceptionWithInvalidName() {
	    return array(
	       array(null),
	       array(''),
	       array($this),
	       array(array()),
	    );
	}

	/**
     * @dataProvider providerEquals
	 */
	public function testEquals($expected, $fieldValidator1, $fieldValidator2) {
	    $result = $fieldValidator1->equals($fieldValidator2);

	    $this->assertEquals($expected, $result);
	}

	public function providerEquals() {
	    return array(
	       array(true, new FieldValidator('name'), new FieldValidator('name')),
	       array(true, new FieldValidator('name'), new FieldValidator('name', array())),
	       array(true, new FieldValidator('name', array('option1' => 1, 'option2' => 2)), new FieldValidator('name', array('option1' => 1, 'option2' => 2))),
	       array(false, new FieldValidator('name'), new FieldValidator('name', array('option'))),
	       array(false, new FieldValidator('name'), new FieldValidator('name2')),
	       array(false, new FieldValidator('name', array('option1' => 1)), new FieldValidator('name', array('option2' => 1))),
	       array(false, new FieldValidator('name', array('option1' => 1, 'option2' => 1)), new FieldValidator('name', array('option2' => 1, 'option3' => 1))),
	       array(false, new FieldValidator('name', array('option1' => 1, 'option3' => 1)), new FieldValidator('name', array('option1' => 1, 'option2' => 1))),
	       array(false, new FieldValidator('name', array('option1' => 1, 'option2' => 2)), new FieldValidator('name', array('option1' => 1, 'option2' => 3))),
	    );
	}

}