<?php

namespace zibo\library\orm\definition;

use zibo\library\database\definition\Index;
use zibo\library\database\definition\Table;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\model\data\format\DataFormatter;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class ModelTableTest extends BaseTestCase {

    public function testConstruct() {
        $name = 'table';

        $table = new ModelTable($name);
        $fields = $table->getFields();

        $this->assertEquals($name, Reflection::getProperty($table, 'name'));
        $this->assertTrue(is_array($fields));
        $this->assertEquals(1, count($fields));
        $this->assertTrue(array_key_exists(ModelTable::PRIMARY_KEY, $fields));
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidNamePassed
     * @expectedException zibo\ZiboException
     */
	public function testConstructThrowsExceptionWhenInvalidNamePassed($name) {
		new ModelTable($name);
	}

	public function providerConstructThrowsExceptionWhenInvalidNamePassed() {
	    return array(
	       array(''),
	       array($this),
	    );
	}

	/**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidIsLoggedPassed
     * @expectedException zibo\ZiboException
     */
	public function testConstructThrowsExceptionWhenInvalidIsLoggedPassed($isLogged) {
		new ModelTable('name', $isLogged);
	}

	public function providerConstructThrowsExceptionWhenInvalidIsLoggedPassed() {
	    return array(
	       array('test'),
	       array(''),
	       array($this),
	    );
	}

    /**
     * @dataProvider providerSetWillBlockDeleteWhenUsedThrowsExceptionWhenNoBooleanPassed
     * @expectedException zibo\ZiboException
     */
    public function testSetWillBlockDeleteWhenUsedThrowsExceptionWhenNoBooleanPassed($flag) {
        $table = new ModelTable('table');
        $table->setWillBlockDeleteWhenUsed($flag);
    }

    public function providerSetWillBlockDeleteWhenUsedThrowsExceptionWhenNoBooleanPassed() {
        return array(
            array('test'),
            array($this),
        );
    }

    public function testGetDatabaseTable() {
        $field1 = new PropertyField('field1', 'type');
        $field2 = new PropertyField('field2', 'type');
        $field3 = new HasManyField('field3', 'model');
        $index = new Index('field2', array($field2));

        $modelTable = new ModelTable('table');
        $modelTable->addField($field1);
        $modelTable->addField($field2);
        $modelTable->addField($field3);
        $modelTable->addIndex($index);

        $pk = new PropertyField('id', 'pk');
        $pk->setIsAutonumbering(true);
        $pk->setIsPrimaryKey(true);

        $databaseTable = new Table('table');
        $databaseTable->addField($pk);
        $databaseTable->addField($field1);
        $databaseTable->addField($field2);
        $databaseTable->addIndex($index);

        $this->assertEquals($databaseTable, $modelTable->getDatabaseTable());
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testAddFieldThrowsExceptionWhenTheFieldExists() {
        $table = new ModelTable('table');
        $table->addField(new PropertyField('id', 'pk'));
    }

	public function testSetField() {
		$table = new ModelTable('table');
		$field = new PropertyField('field', 'type');

		$table->setField($field);
		$tableFields = $table->getFields();

		$found = false;
		foreach ($tableFields as $tableField) {
			if ($tableField == $field) {
				$found = true;
			}
		}

		$this->assertTrue($found);
	}

	public function testSetBelongsToFieldCreatesIndex() {
		$fieldName = 'field';
		$table = new ModelTable('table');
		$field = new BelongsToField($fieldName, 'type');

		$table->setField($field);

		$this->assertTrue($table->hasIndex($fieldName));
	}

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testSetFieldThrowsExceptionWhenTryingToAddAnotherHasFieldWithTheSameModelAndTheSameLinkModel() {
        $field1 = new HasOneField('field1', 'model');
        $field1->setLinkModelName('link');

        $field2 = new HasOneField('field2', 'model');
        $field2->setLinkModelName('link');

        $table = new ModelTable('table');
        $table->addField($field1);
        $table->addField($field2);
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testGetFieldThrowsExceptionWhenFieldNotFound() {
        $table = new ModelTable('table');
    	$table->getField('unexistant');
    }

    public function testOrderFields() {
        $table = new ModelTable('table');
        $table->addField(new PropertyField('name1', 'type'));
        $table->addField(new PropertyField('name2', 'type'));
        $table->addField(new PropertyField('name3', 'type'));
        $table->addField(new PropertyField('name4', 'type'));

        $fields = $table->getFields();

        $this->assertEquals(array('id', 'name1', 'name2', 'name3', 'name4'), array_keys($fields));

        $table->orderFields(array('name3', 'name2'));

        $fields = $table->getFields();

        $this->assertEquals(array('id', 'name3', 'name2', 'name1', 'name4'), array_keys($fields));

        $table->orderFields(array('name4', 'name3', 'id', 'name2', 'name1'));

        $fields = $table->getFields();

        $this->assertEquals(array('id', 'name4', 'name3', 'name2', 'name1'), array_keys($fields));
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testOrderFieldsThrowsExceptionWhenAnUnexistantFieldIsReferenced() {
        $table = new ModelTable('table');
        $table->addField(new PropertyField('name1', 'type'));
        $table->addField(new PropertyField('name2', 'type'));
        $table->addField(new PropertyField('name3', 'type'));

        $table->orderFields(array('name4', 'name3'));
    }

    public function testHasRelationFields() {
        $table = new ModelTable('table');

        $this->assertFalse($table->hasRelationFields());

        $table->addField(new HasManyField('field', 'model'));

        $this->assertTrue($table->hasRelationFields());
    }

    public function testGetRelationFields() {
		$property = new PropertyField('field1', 'type1');
		$hasMany = new HasManyField('field2', 'model1');
		$belongsTo = new BelongsToField('field3', 'model2');
        $table = new ModelTable('table');
        $table->addField($property);
        $table->addField($hasMany);
        $table->addField($belongsTo);

        $expected = array(
            ModelTable::BELONGS_TO => array(),
            ModelTable::HAS_ONE => array(),
            ModelTable::HAS_MANY => array($hasMany->getName() => $hasMany),
        );
        $this->assertEquals($expected, $table->getRelationFields('model1'), 'model1');

        $expected = array(
            ModelTable::BELONGS_TO => array(),
            ModelTable::HAS_ONE => array(),
            ModelTable::HAS_MANY => array(),
        );
        $this->assertEquals($expected, $table->getRelationFields('type1'), 'no match');

        $expected = array(
            ModelTable::BELONGS_TO => array($belongsTo->getName() => $belongsTo),
            ModelTable::HAS_ONE => array(),
            ModelTable::HAS_MANY => array(),
        );
        $this->assertEquals($expected, $table->getRelationFields('model2'), 'model2');
	}

	/**
	 * @expectedException zibo\library\orm\exception\OrmException
	 */
	public function testGetRelationFieldsThrowsExceptionWhenInvalidTypeProvided() {
	    $table = new ModelTable('table');

	    $table->getRelationFields('model', 'test');
	}

    public function testAddIndex() {
        $table = new ModelTable('table');
        $field = new PropertyField('field', 'type');
        $index = new Index('index', array($field));

        $table->addField($field);
        $table->addIndex($index);

        $indexes = $table->getIndexes();

        $expected = array($index->getName() => $index);

        $this->assertEquals($expected, $indexes);
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testAddIndexThrowsExceptionWhenAddingAnIndexWithTheSameName() {
        $table = new ModelTable('table');
        $field = new PropertyField('field', 'type');
        $index = new Index('index', array($field));

        $table->addField($field);
        $table->addIndex($index);
        $table->addIndex($index);
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testSetIndexThrowsExceptionWhenIndexFieldDoesNotExist() {
        $table = new ModelTable('table');
        $field = new PropertyField('field', 'type');
        $index = new Index('index', array(new PropertyField('field2', 'type')));

        $table->addField($field);
        $table->setIndex($index);
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testSetIndexThrowsExceptionWhenAddingAHasField() {
        $table = new ModelTable('table');
        $field = new HasManyField('field', 'table2');
        $index = new Index('index', array($field));

        $table->addField($field);
        $table->addIndex($index);
    }

    /**
     * @dataProvider providerGetIndexThrowsExceptionWhenInvalidNameProvided
     * @expectedException zibo\ZiboException
     */
    public function testGetIndexThrowsExceptionWhenInvalidNameProvided($name) {
        $table = new ModelTable('table');
        $table->getIndex($name);
    }

    public function providerGetIndexThrowsExceptionWhenInvalidNameProvided() {
        return array(
            array(''),
            array('name'),
            array($this),
        );
    }

    public function testHasIndex() {
        $table = new ModelTable('table');
        $field = new PropertyField('field', 'type');
        $index = new Index('index', array($field));

        $table->addField($field);
        $table->addIndex($index);

        $this->assertTrue($table->hasIndex('index'));
        $this->assertFalse($table->hasIndex('unexistant'));
    }

    /**
     * @dataProvider providerHasIndexThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testHasIndexThrowsExceptionWhenInvalidValuePassed($value) {
        $table = new ModelTable('table');
        $table->hasIndex($value);
    }

    public function providerHasIndexThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array(''),
            array(array()),
            array($this),
        );
    }

    public function testSetDataFormat() {
        $name = 'name';

        $format = new DataFormat($name, 'format');

        $table = new ModelTable('table');
        $table->setDataFormat(clone($format));

        $formats = Reflection::getProperty($table, 'dataFormats');

        $this->assertTrue(is_array($formats));
        $this->assertEquals(1, count($formats));
        $this->assertTrue(array_key_exists($name, $formats));
        $this->assertEquals($format, $formats[$name]);
    }

    public function testGetDataFormat() {
        $name = 'name';

        $format = new DataFormat($name, 'format');


        $table = new ModelTable('table');
        $table->setDataFormat(clone($format));

        $tableFormat = $table->getDataFormat($name);

        $this->assertEquals($format, $tableFormat);
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testGetDataFormatThrowsExceptionWhenDataFormatNotSet() {
        $table = new ModelTable('table');
        $table->getDataFormat('unexistant');
    }

    public function testGetDataFormatReturnsFalseWhenDataFormatNotSet() {
        $table = new ModelTable('table');
        $this->assertFalse($table->getDataFormat('unexistant', false));
    }

    public function testGetDataFormatReturnsDefaultTitleFormatWhenTitleFormatRequestedButNotSet() {
        $modelName = 'table';

        $table = new ModelTable($modelName);
        $format = $table->getDataFormat(DataFormatter::FORMAT_TITLE);

        $this->assertEquals(DataFormatter::FORMAT_TITLE, $format->getName());
        $this->assertEquals($modelName . ' {id}', $format->getFormat());
    }

    public function testHasDataFormat() {
        $name = 'name';

        $table = new ModelTable('table');
        $table->setDataFormat(new DataFormat($name, 'format'));

        $this->assertFalse($table->hasDataFormat('unexistant'));
        $this->assertTrue($table->hasDataFormat($name));
    }

    /**
     * @dataProvider providerHasDataFormatThrowsExceptionWhenInvalidValuePassed
     * @expectedException zibo\ZiboException
     */
    public function testHasDataFormatThrowsExceptionWhenInvalidValuePassed($value) {
        $table = new ModelTable('table');
        $table->hasDataFormat($value);
    }

    public function providerHasDataFormatThrowsExceptionWhenInvalidValuePassed() {
        return array(
            array(''),
            array(array()),
            array($this),
        );
    }

    public function testRemoveDataFormat() {
        $nameRemove = 'remove';
        $nameOther = 'other';

        $table = new ModelTable('table');
        $table->setDataFormat(new DataFormat($nameRemove, 'format'));
        $table->setDataFormat(new DataFormat($nameOther, 'format'));

        $dataFormats = Reflection::getProperty($table, 'dataFormats');

        $this->assertEquals(2, count($dataFormats));
        $this->assertTrue(array_key_exists($nameRemove, $dataFormats));
        $this->assertTrue(array_key_exists($nameOther, $dataFormats));

        $table->removeDataFormat($nameRemove);

        $dataFormats = Reflection::getProperty($table, 'dataFormats');

        $this->assertEquals(1, count($dataFormats));
        $this->assertTrue(array_key_exists($nameOther, $dataFormats));
    }

    /**
     * @dataProvider providerRemoveDataFormatThrowsExceptionWhenInvalidNameProvided
     * @expectedException zibo\ZiboException
     */
    public function testRemoveDataFormatThrowsExceptionWhenInvalidNameProvided($name) {
        $table = new ModelTable('table');
        $table->removeDataFormat($name);
    }

    public function providerRemoveDataFormatThrowsExceptionWhenInvalidNameProvided() {
        return array(
            array(null),
            array($this),
            array(array()),
        );
    }

    /**
     * @expectedException zibo\library\orm\exception\ModelException
     */
    public function testRemoveDataFormatThrowsExceptionWhenDataFormatNotSet() {
        $table = new ModelTable('table');
        $table->removeDataFormat('unexistant');
    }

    public function testGetDataFormats() {
        $name = 'name';

        $table = new ModelTable('table');

        $formats = $table->getDataFormats(false);

        $this->assertTrue(is_array($formats));
        $this->assertEquals(0, count($formats));

        $formats = $table->getDataFormats(true);

        $this->assertTrue(is_array($formats));
        $this->assertEquals(1, count($formats));
        $this->assertTrue(array_key_exists(DataFormatter::FORMAT_TITLE, $formats));

        $table->setDataFormat(new DataFormat($name, 'format'));

        $formats = $table->getDataFormats(true);

        $this->assertTrue(is_array($formats));
        $this->assertEquals(2, count($formats));
        $this->assertTrue(array_key_exists(DataFormatter::FORMAT_TITLE, $formats));
        $this->assertTrue(array_key_exists($name, $formats));

        $formats = $table->getDataFormats(false);

        $this->assertTrue(is_array($formats));
        $this->assertEquals(1, count($formats));
        $this->assertFalse(array_key_exists(DataFormatter::FORMAT_TITLE, $formats));
        $this->assertTrue(array_key_exists($name, $formats));

        $table->setDataFormat(new DataFormat(DataFormatter::FORMAT_TITLE, 'format'));

        $formats = $table->getDataFormats(true);

        $this->assertTrue(is_array($formats));
        $this->assertEquals(2, count($formats));
        $this->assertTrue(array_key_exists(DataFormatter::FORMAT_TITLE, $formats));
        $this->assertTrue(array_key_exists($name, $formats));

        $formats = $table->getDataFormats(false);

        $this->assertTrue(is_array($formats));
        $this->assertEquals(2, count($formats));
        $this->assertTrue(array_key_exists(DataFormatter::FORMAT_TITLE, $formats));
        $this->assertTrue(array_key_exists($name, $formats));
    }

}