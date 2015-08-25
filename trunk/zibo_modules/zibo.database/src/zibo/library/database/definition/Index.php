<?php

namespace zibo\library\database\definition;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

/**
 * Definition of an index of a table
 */
class Index {

    /**
     * Name of this index
     * @var string
     */
    private $name;

    /**
     * Array with the fields of this index
     * @var array
     */
    private $fields;

    /**
     * Construct a new index
     * @param string name name of the index
     */
    public function __construct($name, array $fields) {
        $this->setName($name);
        $this->setFields($fields);
    }

    /**
     * Set the name of this index
     * @param string name name of this index
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\database\exception\DatabaseException when the name of the index is empty
     */
    private function setName($name) {
        if (!String::isString($name, String::NOT_EMPTY)) {
            throw new DatabaseException('Name is empty');
        }
        $this->name = $name;
    }

    /**
     * Get the name of this index
     * @return string name of this index
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the fields for this index
     * @param array fields array with Field instances
     * @throws zibo\library\database\exception\DatabaseException when an empty array is provided for the fields or when there is a non Field instance in the array
     */
    private function setFields(array $fields) {
        if (empty($fields)) {
            throw new DatabaseException('No fields provided for this index');
        }

        $this->fields = array();
        foreach ($fields as $index => $field) {
            if (!$field instanceof Field) {
                throw new DatabaseException('Provided fields does not contain a Field instance on index ' . $index);
            }
            $this->fields[$field->getName()] = $field;
        }
    }

    /**
     * Get the fields for this index
     * @return array array with Field instances
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Checks if the provided index has the same fields as this index
     * @param Index $index Index to check
     * @return boolean True when the provided index has the same fields, false otherwise
     */
    public function equals(Index $index) {
        $fields = $index->getFields();

        foreach ($fields as $fieldName => $field) {
            if (!isset($this->fields[$fieldName])) {
                return false;
            }
        }

        foreach ($this->fields as $fieldName => $field) {
            if (!isset($fields[$fieldName])) {
                return false;
            }
        }

        return true;
    }

}