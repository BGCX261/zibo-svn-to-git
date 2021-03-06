<?php

namespace zibo\library\orm\definition;

use zibo\core\Zibo;

use zibo\library\database\definition\Field;
use zibo\library\database\definition\ForeignKey;
use zibo\library\database\definition\Index;
use zibo\library\database\definition\Table;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\field\HasField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\exception\OrmException;
use zibo\library\orm\model\data\format\DataFormat as Format;
use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\Boolean;
use zibo\library\String;

/**
 * Definition of a Model
 */
class ModelTable {

    /**
     * Type identifier for a property field
     * @var integer
     */
    const PROPERTY = 1;

    /**
     * Type identifier for a belongs to field
     * @var integer
     */
    const BELONGS_TO = 2;

    /**
     * Type identifier for a has one field
     * @var integer
     */
    const HAS_ONE = 3;

    /**
     * Type identifier for a has many field
     * @var integer
     */
    const HAS_MANY = 4;

    /**
     * Name of the primary key field
     * @var string
     */
    const PRIMARY_KEY = 'id';

    /**
     * Regular expression for the model name
     * @var string
     */
    const REGEX_NAME = '/^([a-zA-Z0-9_]){3,}$/';

    /**
     * Name of the model
     * @var string
     */
    private $name;

    /**
     * The group of this model
     * @var string
     */
    private $group;

    /**
     * Flag to see if this model is logged, true to log, false otherwise
     * @var boolean
     */
    private $isLogged;

    /**
     * Flag to see if this model has localized fields
     * @var boolean
     */
    private $isLocalized;

    /**
     * The fields of this model
     * @var array
     */
    private $fields;

    /**
     * The indexes of this model
     * @var array
     */
    private $indexes;

    /**
     * Array with formats to generate a string representation of a data object
     * @var array
     */
    private $dataFormats;

    /**
     * Flag to see if deletes should be blocked when a record is still linked by another model
     * @var boolean
     */
    private $willBlockDeleteWhenUsed;

    /**
     * Construct this model definition
     * @param string $name name of the model
     * @param boolean $isLogged set to true to log this model
     * @return null
     */
    public function __construct($name, $isLogged = false) {
        $this->setName($name);
        $this->setIsLogged($isLogged);

        $this->isLocalized = false;
        $this->fields = array();
        $this->indexes = array();
        $this->dataFormats = array();
        $this->willBlockDeleteWhenUsed = false;

        $primaryKey = new PropertyField(self::PRIMARY_KEY, Field::TYPE_PRIMARY_KEY);
        $primaryKey->setIsAutoNumbering(true);
        $primaryKey->setIsPrimaryKey(true);

        $this->addField($primaryKey);
    }

    /**
     * Return the fields to serialize
     * @return array Array with field names
     */
    public function __sleep() {
        $fields = array('name', 'fields');

        if ($this->isLogged) {
            $fields[] = 'isLogged';
        }

        if ($this->isLocalized) {
            $fields[] = 'isLocalized';
        }

        if ($this->indexes) {
            $fields[] = 'indexes';
        }

        if ($this->dataFormats) {
            $fields[] = 'dataFormats';
        }

        if ($this->willBlockDeleteWhenUsed) {
            $fields[] = 'willBlockDeleteWhenUsed';
        }

        if ($this->group) {
            $fields[] = 'group';
        }

        return $fields;
    }

    /**
     * Reinitialize this object after unserializing
     * @return null
     */
    public function __wakeup() {
        if (!$this->indexes) {
            $this->indexes = array();
        }

        if (!$this->dataFormats) {
            $this->dataFormats = array();
        }
    }

    /**
     * Sets the name of this model
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the name is empty or invalid
     */
    private function setName($name) {
        if (String::isEmpty($name)) {
            throw new ModelException('Provided name is empty');
        }

        $this->name = $name;
    }

    /**
     * Get the name of this model
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the group of this model
     * @param string $group
     * @return null
     * @throws zibo\ZiboException when the group is empty or invalid
     */
    public function setGroup($group) {
        if ($group !== null && String::isEmpty($group)) {
            throw new ModelException('Provided group is empty');
        }

        $this->group = $group;
    }

    /**
     * Get the group of this model
     * @return string
     */
    public function getGroup() {
        return $this->group;
    }

    /**
     * Sets the is logged flag
     * @param boolean $isLogged
     * @return null
     */
    private function setIsLogged($isLogged) {
        $this->isLogged = Boolean::getBoolean($isLogged);
    }

    /**
     * Gets the is logged flag
     * @return boolean
     */
    public function isLogged() {
        return $this->isLogged;
    }

    /**
     * Gets the is localized flag
     * @return boolean
     */
    public function isLocalized() {
        return $this->isLocalized;
    }

    /**
     * Sets whether this model will block deletes when a record is still in use by another record
     * @param boolean $flag True to block deletes, false otherwise
     * @return null
     */
    public function setWillBlockDeleteWhenUsed($flag) {
        $this->willBlockDeleteWhenUsed = Boolean::getBoolean($flag);
    }

    /**
     * Gets whether this model will block deletes when a record is still in use by another record
     * @return boolean True to block deletes, false otherwise
     */
    public function willBlockDeleteWhenUsed() {
        return $this->willBlockDeleteWhenUsed;
    }

    /**
     * Get the database table definition of this model
     * @return zibo\library\database\definition\Table
     */
    public function getDatabaseTable() {
        $table = new Table($this->name);

        foreach ($this->fields as $fieldName => $field) {
            if ($field->isLocalized() || $field instanceof HasManyField || $field instanceof HasOneField) {
                continue;
            }

            $table->addField($field);

            if ($field instanceof BelongsToField) {
                $name = $this->name . '_' . ucfirst($fieldName);
                if (strlen($name) > 64) {
                    $name = '_' . ucfirst($fieldName);
                    $name = substr($this->name, 0, 64 - strlen($name)) . $name;
                }

                $foreignKey = new ForeignKey($fieldName, $field->getRelationModelName(), self::PRIMARY_KEY, $name);
                $table->setForeignKey($foreignKey);
            }
        }

        foreach ($this->indexes as $index) {
            $fields = $index->getFields();
            foreach ($fields as $field) {
                if ($field->isLocalized()) {
                    continue 2;
                }
            }

            $table->addIndex($index);
        }

        return $table;
    }

    /**
     * Adds a field to this model
     * @param zibo\library\orm\definition\field\ModelField $field
     * @return null
     * @throws zibo\library\orm\exception\ModelException when the name of the field is already set in this model
     * @throws zibo\library\orm\exception\ModelException when the field has the same link as another field in this model
     */
    public function addField(ModelField $field) {
        if (isset($this->fields[$field->getName()])) {
            throw new ModelException($field->getName() . ' is already set');
        }

        $this->setField($field);
    }

    /**
     * Sets a field to this model
     * @param zibo\library\orm\definition\field\ModelField $field
     * @return null
     * @throws zibo\library\orm\exception\ModelException when the field has the same link as another field in this model
     */
    public function setField(ModelField $field) {
        if ($field->isLocalized() && !$this->isLocalized) {
            $this->isLocalized = true;
        }

        $name = $field->getName();

        $addIndex = false;

        if ($field instanceof BelongsToField) {
            $field->setDefaultValue(0);
            $addIndex = true;
        } elseif ($field instanceof HasField) {
            if ($field instanceof HasOneField) {
                $type = self::HAS_ONE;
            } else {
                $type = self::HAS_MANY;
            }

            $relationFields = $this->getRelationFields($field->getRelationModelName(), $type);

            $numRelationFields = count($relationFields);
            if ($numRelationFields > 0) {
                $linkModelName = $field->getLinkModelName();
                if ($linkModelName) {
                    foreach ($relationFields as $relationFieldName => $relationField) {
                        if ($relationFieldName == $name) {
                            continue;
                        }

                        if ($relationField->getLinkModelName() == $linkModelName) {
                            throw new ModelException('Can\'t add ' . $name . ' to ' . $this->name . ': ' . $field->getRelationModelName() . ' is already linked with link model ' . $linkModelName . ' through the ' . $relationFieldName . ' field, check the link models.');
                        }
                    }
                }
            }
        }

        $this->fields[$name] = $field;

        if ($addIndex && !$this->hasIndex($name)) {
            $index = new Index($name, array($field));
            $this->addIndex($index);
        }
    }

    /**
     * Removes a field from this model
     * @param string $name Name of the field
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     * @throws zibo\library\orm\exception\ModelException when the field is not in this model
     */
    public function removeField($name) {
        $field = $this->getField($name);

        unset($this->fields[$name]);

        foreach ($this->indexes as $indexName => $index) {
            $removeIndex = false;

            $indexFields = $index->getFields();
            foreach ($indexFields as $indexField) {
                if ($indexField->getName() == $name) {
                    $removeIndex = true;
                    break;
                }
            }

            if ($removeIndex) {
                unset($this->indexes[$indexName]);
            }
        }

        if (!$this->isLocalized || ($this->isLocalized && !$field->isLocalized())) {
            return;
        }

        $this->isLocalized = false;

        foreach ($this->fields as $field) {
            if (!$field->isLocalized()) {
                continue;
            }

            $this->isLocalized = true;
            break;
        }
    }

    /**
     * Checks whether this model has a field
     * @param string $name Name of the field
     * @return boolean True if this model has the provided field, false otherwise
     * @throws zibo\ZiboException when the provided name is empty or invalid
     */
    public function hasField($name) {
        if (String::isEmpty($name)) {
            throw new ModelException('Provided field name is empty');
        }

        return array_key_exists($name, $this->fields);
    }

    /**
     * Gets a field by name
     * @param string $name Name of the field
     * @return zibo\library\orm\definition\field\ModelField
     * @throws zibo\library\orm\exception\ModelException when the field is not in this model
     */
    public function getField($name) {
        if (!$this->hasField($name)) {
            throw new ModelException('Field ' . $name . ' not found in ' . $this->name);
        }

        return $this->fields[$name];
    }

    /**
     * Gets all the fields of this model
     * @return array Array with the name of the field as key and the ModelField object as value
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Order the field names in the order of the provided array
     * @param array $fieldNames Array with the new order of field names
     * @return null
     */
    public function orderFields(array $fieldNames) {
        $currentFields = $this->fields;

        $fields = array();
        $fields[self::PRIMARY_KEY] = $this->fields[self::PRIMARY_KEY];
        unset($this->fields[self::PRIMARY_KEY]);

        foreach ($fieldNames as $fieldName) {
            if ($fieldName == self::PRIMARY_KEY) {
                continue;
            }

            if (!$this->hasField($fieldName)) {
                $this->fields = $currentFields;
                throw new ModelException('Field ' . $fieldName . ' not found in ' . $this->name);
            }

            $fields[$fieldName] = $this->fields[$fieldName];
            unset($this->fields[$fieldName]);
        }

        foreach ($this->fields as $fieldName => $field) {
            $fields[$fieldName] = $field;
        }

        $this->fields = $fields;
    }

    /**
     * Checks whether this model table has relation fields
     * @return boolean True when the model has relation fields, false otherwise
     */
    public function hasRelationFields() {
        foreach ($this->fields as $field) {
            if ($field instanceof RelationField) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the fields with a relation to the provided model
     * @param string $modelName Name of the relation model
     * @param integer $type Type identifier to get only the provided type
     * @return array An array with the fields for the provided type. If no type has been provided, an
     *              array with the type identifier as key and an array with the fields as value
     */
    public function getRelationFields($modelName, $type = null) {
        $result = array(
            self::BELONGS_TO => array(),
            self::HAS_ONE => array(),
            self::HAS_MANY => array(),
        );

        foreach ($this->fields as $fieldName => $field) {
            if (!($field instanceof RelationField)) {
                continue;
            }

            if ($field->getRelationModelName() != $modelName) {
                continue;
            }

            if ($field instanceof BelongsToField) {
                $result[self::BELONGS_TO][$fieldName] = $field;
                continue;
            }
            if ($field instanceof HasOneField) {
                $result[self::HAS_ONE][$fieldName] = $field;
                continue;
            }
            if ($field instanceof HasManyField) {
                $result[self::HAS_MANY][$fieldName] = $field;
                continue;
            }
        }

        if (!$type) {
            return $result;
        }

        if (!array_key_exists($type, $result)) {
            throw new OrmException('Provided type is not a valid relation type, use BELONGS_TO, HAS_ONE or HAS_MANY');
        }

        return $result[$type];
    }

    /**
     * Adds an index to the table
     * @param zibo\library\database\definition\Index $index index to add to the table
     * @return null
     * @throws zibo\library\orm\exception\ModelException when a field of the index is not in this table
     * @throws zibo\library\orm\exception\ModelException when a field of the index is not a property or a belongs to field
     */
    public function addIndex(Index $index) {
        if ($this->hasIndex($index->getName())) {
            throw new ModelException('Index ' . $index->getName() . ' is already set in this table');
        }

        $this->setIndex($index);
    }

    /**
     * Sets an index to the table
     * @param zibo\library\database\definition\Index $index index to add to the table
     * @return null
     * @throws zibo\library\orm\exception\ModelException when a field of the index is not in this table
     * @throws zibo\library\orm\exception\ModelException when a field of the index is not a property or a belongs to field
     * @throws zibo\library\orm\exception\ModelException when the index contains out of localized and unlocalized fields
     */
    public function setIndex(Index $index) {
        $isLocalized = null;

        $fields = $index->getFields();
        foreach ($fields as $fieldName => $field) {
            if (!$this->hasField($fieldName)) {
                throw new ModelException('Cannot add the index: the field ' . $fieldName . ' is not set in this table');
            }

            if ($this->fields[$fieldName] instanceof HasField) {
                throw new ModelException('Cannot add the index: the field ' . $fieldName . ' is not a property or a belongs to field');
            }

            if ($isLocalized === null) {
                $isLocalized = $field->isLocalized();
            } elseif ($field->isLocalized() != $isLocalized) {
                throw new ModelException('Cannot combine localized and unlocalized fields in 1 index');
            }
        }

        $this->indexes[$index->getName()] = $index;
    }

    /**
     * Gets the index definition of an index
     * @param string $name of the index
     * @return zibo\library\database\definition\Index index definition of the index
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\orm\exception\ModelException when the name is empty or the index does not exist
     */
    public function getIndex($name) {
        if (!$this->hasIndex($name)) {
            throw new ModelException('Index ' . $name . ' is not defined for this table');
        }

        return $this->indexes[$name];
    }

    /**
     * Gets the indexes of this table
     * @return array array with Index objects as value and the indexname as key
     */
    public function getIndexes() {
        return $this->indexes;
    }

    /**
     * Checks whether this table has a certain index
     * @param string $name name of the index
     * @throws zibo\ZiboException when no valid string provided as name
     * @throws zibo\library\orm\exception\ModelException when the name is empty
     * @throws zibo\ZiboException when the name is invalid
     */
    public function hasIndex($name) {
        if (String::isEmpty($name)) {
            throw new ModelException('Provided name is empty');
        }

        if (array_key_exists($name, $this->indexes)) {
            return true;
        }

        return false;
    }

    /**
     * Removes a index from this model
     * @param string $name Name of the index
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     * @throws zibo\library\orm\exception\ModelException when the index is not in this model
     */
    public function removeIndex($name) {
        $index = $this->getIndex($name);

        unset($this->indexes[$name]);
    }

    /**
     * Gets the fields which are possible to use in an index
     * @return array Array with the field name as key and value
     */
    public function getIndexFields() {
        $indexFields = array();

        foreach ($this->fields as $fieldName => $field) {
            if ($fieldName == ModelTable::PRIMARY_KEY) {
                continue;
            }

            if ($field instanceof PropertyField || $field instanceof BelongsToField) {
                $indexFields[$fieldName] = $fieldName;
            }
        }

        return $indexFields;
    }

    /**
     * Adds a data format
     * @param string $name Name of the format
     * @return null
     */
    public function setDataFormat(DataFormat $format) {
        $this->dataFormats[$format->getName()] = $format;
    }

    /**
     * Gets a data format
     * @param string $name Name of the format
     * @param boolean $throwException Set to true to throw an exception when the data format does not exist
     * @return DataFormat
     * @throws zibo\library\orm\exception\ModelException when there is no data format set with the provided name
     */
    public function getDataFormat($name, $throwException = true) {
        if ($this->hasDataFormat($name)) {
            return $this->dataFormats[$name];
        }

        if ($name == DataFormatter::FORMAT_TITLE) {
            return $this->createDefaultTitleDataFormat();
        }

        if ($throwException) {
            throw new ModelException('No data format set with name ' . $name);
        }

        return false;
    }

    /**
     * Checks if this model has a certain data format
     * @param string $name Name of the data format
     * @return boolean True if this table has a data format by the provided name, false otherwise
     * @throws zibo\ZiboException when the provided name is empty or not a string
     */
    public function hasDataFormat($name) {
        if (String::isEmpty($name)) {
            throw new ModelException('Provided name is empty');
        }

        return array_key_exists($name, $this->dataFormats);
    }

    /**
     * Removes a data format
     * @param string $name Name of the data format
     * @return null
     * @throws zibo\library\orm\exception\ModelException
     */
    public function removeDataFormat($name) {
        if (!$this->hasDataFormat($name)) {
            throw new ModelException('No data format set with name ' . $name);
        }

        unset($this->dataFormats[$name]);
    }

    /**
     * Gets all the data formats
     * @param boolean $createDefaultTitleDataFormat Set to false to skip adding the default title format if no title format is set
     * @return array Array with the name of the format as key and the format as value
     */
    public function getDataFormats($createDefaultTitleDataFormat = true) {
        $dataFormats = $this->dataFormats;

        if ($createDefaultTitleDataFormat && !array_key_exists(DataFormatter::FORMAT_TITLE, $dataFormats)) {
            $dataFormats[DataFormatter::FORMAT_TITLE] = $this->createDefaultTitleDataFormat();
        }

        return $dataFormats;
    }

    /**
     * Creates a default title data format
     * @return DataFormat
     */
    protected function createDefaultTitleDataFormat() {
        return new DataFormat(DataFormatter::FORMAT_TITLE, $this->name . ' ' . Format::SYMBOL_OPEN . self::PRIMARY_KEY . Format::SYMBOL_CLOSE);
    }

}