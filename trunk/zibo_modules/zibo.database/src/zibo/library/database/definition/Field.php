<?php

namespace zibo\library\database\definition;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

/**
 * Definition of a field of a table
 */
class Field {

    /**
     * Type name of a binary field
     * @var string
     */
    const TYPE_BINARY = 'binary';

    /**
     * Type name of a boolean field
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Type name of a date field
     * @var string
     */
    const TYPE_DATE = 'date';

    /**
     * Type name of a datetime field
     * @var string
     */
    const TYPE_DATETIME = 'datetime';

    /**
     * Type name of a email field
     * @var string
     */
    const TYPE_EMAIL = 'email';

    /**
     * Type name of a float field
     * @var string
     */
    const TYPE_FLOAT = 'float';

    /**
     * Type name of a file field
     * @var string
     */
    const TYPE_FILE = 'file';

    /**
     * Type name of a foreign key field
     * @var string
     */
    const TYPE_FOREIGN_KEY = 'fk';

    /**
     * Type name of a image field
     * @var string
     */
    const TYPE_IMAGE = 'image';

    /**
     * Type name of a integer field
     * @var string
     */
    const TYPE_INTEGER = 'integer';

    /**
     * Type name of a password field
     * @var string
     */
    const TYPE_PASSWORD = 'password';

    /**
     * Type name of a primary key field
     * @var string
     */
    const TYPE_PRIMARY_KEY = 'pk';

    /**
     * Type name of a string field
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * Type name of a text field
     * @var string
     */
    const TYPE_TEXT = 'text';

    /**
     * Type name of a website field
     * @var string
     */
    const TYPE_WEBSITE = 'website';

    /**
     * Name of the field
     * @var string
     */
    protected $name;

    /**
     * Type of the field
     * @var string
     */
    protected $type;

    /**
     * Default value for this field
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Flag to see if this field is autonumbered
     * @var boolean
     */
    protected $isAutoNumbering;

    /**
     * Flag to see if this field is a primary index
     * @var boolean
     */
    protected $isPrimaryKey;

    /**
     * Flag to see if this field is indexed
     * @var boolean
     */
    protected $isIndexed;

    /**
     * Flag to see if this field is unique
     * @var boolean
     */
    protected $isUnique;

    /**
     * Constructs a new field
     * @param string $name Name of the field
     * @param string $type Type of the field
     * @param mixed $defaultValue Default value of the field
     * @return null
     */
    public function __construct($name, $type, $defaultValue = null) {
        $this->setName($name);
        $this->setType($type);
        $this->setDefaultValue($defaultValue);
    }

    /**
     * Return the fields to serialize
     * @return array Array with field names
     */
    public function __sleep() {
        $fields = array('name', 'type');

        if ($this->defaultValue !== null) {
            $fields[] = 'defaultValue';
        }
        if ($this->isAutoNumbering) {
            $fields[] = 'isAutoNumbering';
        }
        if ($this->isPrimaryKey) {
            $fields[] = 'isPrimaryKey';
        }
        if ($this->isIndexed) {
            $fields[] = 'isIndexed';
        }
        if ($this->isUnique) {
            $fields[] = 'isUnique';
        }

        return $fields;
    }

    /**
     * Sets the name of this field
     * @param string $name Name of this field
     * @return null
     */
    public function setName($name) {
        if (!String::isString($name, String::NOT_EMPTY)) {
            throw new DatabaseException('Provided name is empty or invalid');
        }

        $this->name = $name;
    }

    /**
     * Gets the name of this field
     * @return string Name of this field
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the type of this field. Types are defined in the database configuration on a driver/protocol level
     * @param string $type Type of this field, can be a defined type or a direct database type
     * @return null
     */
    public function setType($type) {
        if (!String::isString($type, String::NOT_EMPTY)) {
            throw new DatabaseException('Provided type is empty or invalid');
        }

        $this->type = $type;
    }

    /**
     * Gets the type of this field. Types are defined in the database configuration on a driver/protocol level
     * @return string Type of this field
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the default value of this field
     * @param mixed $defaultValue Default value for the field
     * @return null
     */
    public function setDefaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
    }

    /**
     * Gets the default value of this field
     * @return mixed Default value of this field
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }

    /**
     * Sets whether this field uses autonumbering
     * @param boolean $isAutonumbering true if this field uses autonumbering, false otherwise
     * @return null
     */
    public function setIsAutoNumbering($isAutoNumbering) {
        if (!is_bool($isAutoNumbering)) {
            throw new DatabaseException('No boolean passed');
        }

        $this->isAutoNumbering = $isAutoNumbering;
    }

    /**
     * Checks whether this field uses autonumbering
     * @return boolean True if this field uses autonumbering, false otherwise
     */
    public function isAutoNumbering() {
        return $this->isAutoNumbering;
    }

    /**
     * Setq whether this field is a primary key in the containing table
     * @param boolean $isPrimaryKey True if this field is a primary key, false otherwise
     * @return null
     */
    public function setIsPrimaryKey($isPrimaryKey) {
        if (!is_bool($isPrimaryKey)) {
            throw new DatabaseException('No boolean passed');
        }
        $this->isPrimaryKey = $isPrimaryKey;
    }

    /**
     * Checks whether this field is a primary key in the containing table
     * @return boolean True if this field is a primary key, false otherwise
     */
    public function isPrimaryKey() {
        return $this->isPrimaryKey;
    }

    /**
     * Set whether this field is indexed
     * @param boolean $isIndexed True if this field is indexed, false otherwise
     * @return null
     */
    public function setIsIndexed($isIndexed) {
        if (!is_bool($isIndexed)) {
            throw new DatabaseException('No boolean passed');
        }

        $this->isIndexed = $isIndexed;
    }

    /**
     * Checks whether this field is indexed
     * @return boolean True if this field is indexed, false otherwise
     */
    public function isIndexed() {
        return $this->isIndexed;
    }

    /**
     * Sets whether this field is unique
     * @param boolean $isUnique
     * @return null
     */
    public function setIsUnique($isUnique) {
        if (!is_bool($isUnique)) {
            throw new DatabaseException('No boolean passed');
        }

        if ($isUnique) {
            $this->setIsIndexed(false);
        }

        $this->isUnique = $isUnique;
    }

    /**
     * Checks whether this field is unique
     * @return boolean True if this field is unique, false otherwise
     */
    public function isUnique() {
        return $this->isUnique;
    }

}