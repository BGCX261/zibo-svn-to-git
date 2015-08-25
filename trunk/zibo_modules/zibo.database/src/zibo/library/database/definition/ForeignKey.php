<?php

namespace zibo\library\database\definition;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

/**
 * Definition of a foreign key in a table
 */
class ForeignKey {

    /**
     * Name of the foreign key
     * @var string
     */
    private $name;

    /**
     * Name of the field
     * @var string
     */
    private $fieldName;

    /**
     * Name of the reference table
     * @var string
     */
    private $referenceTableName;

    /**
     * Name of the linked field in the reference table
     * @var string
     */
    private $referenceFieldName;

    /**
     * Construct a new foreign key
     * @param string $fieldName Name of the field
     * @param string $referenceTableName Name of the referenced table
     * @param string $referenceFieldName Name of the linked field in the referenced table
     * @return null
     */
    public function __construct($fieldName, $referenceTableName, $referenceFieldName, $name = null) {
        if (!$name) {
            $name = $fieldName;
        }

        if (!String::isString($fieldName, String::NOT_EMPTY)) {
            throw new DatabaseException('Provided name of the foreign key field is empty');
        }
        if (!String::isString($referenceTableName, String::NOT_EMPTY)) {
            throw new DatabaseException('Provided name of the reference table is empty');
        }
        if (!String::isString($referenceFieldName, String::NOT_EMPTY)) {
            throw new DatabaseException('Provided name of the reference field is empty');
        }
        if (!String::isString($name, String::NOT_EMPTY)) {
            throw new DatabaseException('Provided name of the foreign key is empty');
        }

        $this->name = $name;
        $this->fieldName = $fieldName;
        $this->referenceTableName = $referenceTableName;
        $this->referenceFieldName = $referenceFieldName;
    }

    /**
     * Gets the name of the foreign key
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the name of the field
     * @return string
     */
    public function getFieldName() {
        return $this->fieldName;
    }

    /**
     * Gets the name of the reference table
     * @return string
     */
    public function getReferenceTableName() {
        return $this->referenceTableName;
    }

    /**
     * Gets the name of the linked field in the reference table
     * @return string
     */
    public function getReferenceFieldName() {
        return $this->referenceFieldName;
    }

    /**
     * Checks whether the provided foreign key represents the same definition as this foreign key
     * @return boolean True if the 2 are the same, false otherwise
     */
    public function equals(ForeignKey $foreignKey) {
        if ($this->name != $foreignKey->getName()) {
            return false;
        }

        if ($this->fieldName != $foreignKey->getFieldName()) {
            return false;
        }

        if ($this->referenceTableName != $foreignKey->getReferenceTableName()) {
            return false;
        }

        if ($this->referenceFieldName != $foreignKey->getReferenceFieldName()) {
            return false;
        }

        return true;
    }

}