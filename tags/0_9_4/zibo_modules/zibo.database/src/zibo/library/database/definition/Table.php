<?php

namespace zibo\library\database\definition;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

/**
 * Definition of a database table
 */
class Table {

    /**
     * Name of the table
     * @var string
     */
    protected $name;

    /**
     * Array with the fields of this table
     * @var array
     */
    protected $fields;

    /**
     * Array with foreign key definitions for this table
     * @var array
     */
    protected $foreignKeys;

    /**
     * Array with index definitions for this table
     * @var array
     */
    protected $indexes;

    /**
     * Constructs the table definition
     * @param string $name Name of the table
     * @return null
     */
    public function __construct($name) {
        $this->setName($name);
        $this->fields = array();
        $this->foreignKeys = array();
        $this->indexes = array();
    }

    /**
     * Sets the name of the table
     * @param string $name Name of the table
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\database\exception\DatabaseException when the name of the table is empty
     */
    protected function setName($name) {
        if (String::isEmpty($name)) {
            throw new DatabaseException('Provided name is empty');
        }

        $this->name = $name;
    }

    /**
     * Gets the name of the table
     * @return string Name of the table
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Adds a field to the table
     * @param Field $field Definition of the field to add
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the field is already set to this table
     */
    public function addField(Field $field) {
        if ($this->hasField($field->getName())) {
            throw new DatabaseException('Field ' . $field->getName() . ' is already set in this table');
        }

        $this->setField($field);
    }

    /**
     * Sets or adds a new field definition
     * @param Field $field Definition of the field to set or add
     * @return null
     */
    public function setField(Field $field) {
    	$this->fields[$field->getName()] = $field;
    }

    /**
     * Gets the field definition of a field
     * @param string $name Name of the field
     * @return Field Field definition of the field
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\database\exception\DatabaseException when the name is empty or the field does not exist
     */
    public function getField($name) {
        if (!$this->hasField($name)) {
            throw new DatabaseException('Field ' . $name . ' does not exist in this table');
        }

    	return $this->fields[$name];
    }

    /**
     * Gets the fields of this table
     * @return array Array with Field objects as value and the field name as key
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Gets the primary keys of this table
     * @return array Array with Field objects as value and the field name as key
     */
    public function getPrimaryKeys() {
        $primaryKeys = array();

        foreach($this->fields as $name => $field) {
            if (!$field->isPrimaryKey()) {
                continue;
            }

            $primaryKeys[$name] = $field;
        }

        return $primaryKeys;
    }

    /**
     * Checks whether this table has a certain field
     * @param string $name Name of the field
     * @return null
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\database\exception\DatabaseException when the name is empty
     */
    public function hasField($name) {
        if (String::isEmpty($name)) {
            throw new DatabaseException('Provided name is empty');
        }

        if (array_key_exists($name, $this->fields)) {
            return true;
        }

        return false;
    }

    /**
     * Adds a foreign key to the table
     * @param ForeignKey $foreignKey Definition of the foreign key to add
     * @throws zibo\library\database\exception\DatabaseException when the index is already set to this table
     */
    public function setForeignKey(ForeignKey $foreignKey) {
        $fieldName = $foreignKey->getFieldName();
        if (!$this->hasField($fieldName)) {
            throw new DatabaseException('Could not add the foreign key: The field is not set in this table');
        }

        $this->foreignKeys[$fieldName] = $foreignKey;
    }

    /**
     * Gets the foreign key definition for the provided field
     * @param $fieldName Name of the foreign key field
     * @return ForeignKey
     */
    public function getForeignKey($fieldName) {
        if (!$this->hasForeignKey($fieldName)) {
            throw new DatabaseException('Foreign key ' . $fieldName . ' is not defined in this table');
        }

        return $this->foreignKeys[$fieldName];
    }

    /**
     * Gets all the foreign keys of this table
     * @return array Array with ForeignKey objects
     */
    public function getForeignKeys() {
        return $this->foreignKeys;
    }

    /**
     * Checks whether this table has a certain foreign key
     * @param string $fieldName Name of the foreign key field
     * @return boolean True if the foreign key exists, false otherwise
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\database\exception\DatabaseException when the name is empty
     */
    public function hasForeignKey($fieldName) {
        if (String::isEmpty($fieldName)) {
            throw new DatabaseException('Provided field name is empty');
        }

        if (array_key_exists($fieldName, $this->foreignKeys)) {
            return true;
        }

        return false;
    }

    /**
     * Adds an index to the table
     * @param Index $index Definition of the index to add
     * @throws zibo\library\database\exception\DatabaseException when the index is already set to this table
     */
    public function addIndex(Index $index) {
        if ($this->hasIndex($index->getName())) {
            throw new DatabaseException('Index ' . $index->getName() . ' is already set in this table');
        }

        $this->setIndex($index);
    }

    /**
     * Sets or adds a new index definition
     * @param Index $index Definition of the index to set or add
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the index contains a field which is not in this table
     */
    public function setIndex(Index $index) {
        $fields = $index->getFields();
        foreach ($fields as $field) {
            if (!$this->hasField($field->getName())) {
                throw new DatabaseException('Cannot add the index: the field ' . $field->getName() . ' is not set in this table');
            }
        }

        $this->indexes[$index->getName()] = $index;
    }

    /**
     * Gets the index definition of an index
     * @param string $name Name of the index
     * @return Index Index definition of the index
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\database\exception\DatabaseException when the name is empty or the index does not exist
     */
    public function getIndex($name) {
        if (!$this->hasIndex($name)) {
            throw new DatabaseException('Index ' . $name . ' is not defined for this table');
        }

        return $this->indexes[$name];
    }

    /**
     * Gets all the indexes of this table
     * @return array Array with Index objects as value and the index name as key
     */
    public function getIndexes() {
        return $this->indexes;
    }

    /**
     * Checks whether this table has a certain index
     * @param string $name Name of the index
     * @return boolean True if the index exists, false otherwise
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\database\exception\DatabaseException when the name is empty
     */
    public function hasIndex($name) {
        if (String::isEmpty($name)) {
            throw new DatabaseException('Provided name is empty');
        }

        if (array_key_exists($name, $this->indexes)) {
            return true;
        }

        return false;
    }

}