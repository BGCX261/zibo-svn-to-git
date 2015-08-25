<?php

namespace zibo\library\orm\model\meta;

use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\DatabaseManager;
use zibo\library\orm\definition\field\extended\VersionField;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\exception\OrmException;
use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\orm\model\LocalizedModel;
use zibo\library\orm\ModelManager;
use zibo\library\String;

/**
 * Meta of a model table
 */
class ModelMeta {

    const CLASS_DATA = 'zibo\\library\\orm\\model\\data\\Data';

    /**
     * Table definition of the model
     * @var zibo\library\orm\definition\ModelTable
     */
    private $table;

    /**
     * Class name for data objects of this model
     * @var string
     */
    private $dataClassName;

    /**
     * Flag to see whether the model table has been parsed
     * @var boolean
     */
    private $isParsed;

    /**
     * Array with ModelFields objects of the localized fields
     * @var array
     */
    private $localizedFields;

    /**
     * Array with ModelField objects of the property fields
     * @var array
     */
    private $properties;

    /**
     * Array with ModelField objects of the belongs to fields
     * @var array
     */
    private $belongsTo;

    /**
     * Array with ModelField objects of the has one fields
     * @var array
     */
    private $hasOne;

    /**
     * Array with ModelField objects of the has one fields
     * @var array
     */
    private $hasMany;

    /**
     * Array with RelationMeta objects
     * @var array
     */
    private $relations;

    /**
     * Array with the names of the models who have a relation with this model but where there is
     * no relation back.
     * @var array
     */
    private $unlinkedModels;

    /**
     * Formatter for the data of this model
     * @var zibo\library\orm\model\data\format\DataFormatter
     */
    private $dataFormatter;

    /**
     * Constructs a new model meta definition
     * @param zibo\library\orm\definition\ModelTable $table Table definition of the model
     * @param string $dataClassName Class name for data objects for this model
     * @return null
     * @throws zibo\ZiboException when the data class name is empty or invalid
     */
    public function __construct(ModelTable $table, $dataClassName = null) {
        $this->setDataClassName($dataClassName);

        $this->table = $table;
        $this->unlinkedModels = array();

        $this->dataFormatter = new DataFormatter();

        $this->isParsed = false;
    }

    /**
     * Return the fields to serialize
     * @return array Array with field names
     */
    public function __sleep() {
        $fields = array(
            'dataClassName',
            'table'
        );

        if ($this->unlinkedModels) {
            $fields[] = 'unlinkedModels';
        }

        if (!$this->isParsed) {
            return $fields;
        }

        $fields[] = 'properties';

        if ($this->localizedFields) {
            $fields[] = 'localizedFields';
        }

        if ($this->belongsTo) {
            $fields[] = 'belongsTo';
        }

        if ($this->hasOne) {
            $fields[] ='hasOne';
        }

        if ($this->hasMany) {
            $fields[] = 'hasMany';
        }

        if ($this->relations) {
            $fields[] = 'relations';
        }

        return $fields;
    }

    /**
     * Reinitialize the meta after sleeping
     * @return null
     */
    public function __wakeup() {
        $this->dataFormatter = new DataFormatter();

        if (!isset($this->unlinkedModels)) {
            $this->unlinkedModels = array();
        }

        if (!isset($this->isParsed)) {
            $this->isParsed = false;
        }

        if (!isset($this->localizedFields)) {
            $this->localizedFields = array();
        }

        if (!isset($this->belongsTo)) {
            $this->belongsTo = array();
        }

        if (!isset($this->hasOne)) {
            $this->hasOne = array();
        }

        if (!isset($this->hasMany)) {
            $this->hasMany = array();
        }

        if (!isset($this->relations)) {
            $this->relations = array();
        }
    }

    /**
     * Gets the name of the model
     * @return string
     */
    public function getName() {
        return $this->table->getName();
    }
    /**
     * Sets the class name for data objects of this model
     * @param string $dataClassName
     * @return null
     * @throws zibo\ZiboException when the data class name is empty or invalid
     */
    private function setDataClassName($dataClassName) {
        if ($dataClassName === null) {
            $dataClassName = self::CLASS_DATA;
        }

        if (String::isEmpty($dataClassName)) {
            throw new OrmException('Provided data class name is empty');
        }

        $this->dataClassName = $dataClassName;
    }

    /**
     * Gets the class name for data objects of this model
     * @return string
     */
    public function getDataClassName() {
        return $this->dataClassName;
    }

    /**
     * Checks whether the provided data is a valid data object for this model
     * @param mixed $data Data object to check
     * @param boolean $throwException True to throw the exception, false otherwise
     * @return boolean True if the data is valid, false otherwise
     * @throws zibo\library\orm\exception\ModelException when the $throwException flag is false and the data object is not valid
     */
    public function isValidData($data, $throwException = true) {
        $result = $data instanceof $this->dataClassName;

        if ($result || !$throwException) {
            return $result;
        }

        if (is_object($data)) {
            $type = get_class($data);
        } else {
            $type = gettype($data);
        }

        throw new ModelException('Provided ' . $type . ' value is not of the expected ' . $this->dataClassName . ' type');
    }

    /**
     * Gets the data formatter
     * @return zibo\library\orm\model\data\format\DataFormatter
     */
    public function getDataFormatter() {
        return $this->dataFormatter;
    }

    /**
     * Formats the data
     * @param mixed $data Data object of the model
     * @param string $formatName Name of the format
     * @return string Title for the data
     */
    public function formatData($data, $formatName = null) {
        if (!$formatName) {
            $formatName = DataFormatter::FORMAT_TITLE;
        }

        $dataFormat = $this->table->getDataFormat($formatName);

        return $this->dataFormatter->formatData($data, $dataFormat->getFormat());
    }

    /**
     * Gets whether to log model actions
     * @return boolean True to log, false otherwise
     */
    public function isLogged() {
        return $this->table->isLogged();
    }

    /**
     * Gets whether this model has localized fields
     * @return boolean True when there are localized fields, false otherwise
     */
    public function isLocalized() {
        return $this->table->isLocalized();
    }

    /**
     * Gets the name of the localized model of this model
     * @return string
     */
    public function getLocalizedModelName() {
        return $this->table->getName() . LocalizedModel::MODEL_SUFFIX;
    }

    /**
     * Gets the localized model of this model
     * @return zibo\library\orm\model\LocalizeModel
     */
    public function getLocalizedModel() {
        $localizedModelName = $this->getLocalizedModelName();
        return ModelManager::getInstance()->getModel($localizedModelName);
    }

    /**
     * Gets whether this model will block deletes when a record is still in use by another record
     * @return boolean True to block deletes, false otherwise
     */
    public function willBlockDeleteWhenUsed() {
        return $this->table->willBlockDeleteWhenUsed();
    }

    /**
     * Sets the models who have a relation with this model but where there is no relation back.
     * @param array $unlinkedModels Array with model names
     * @return null
     */
    public function setUnlinkedModels(array $unlinkedModels) {
        $this->unlinkedModels = $unlinkedModels;
    }

    /**
     * Gets the models who have a relation with this model but where there is no relation back.
     * @return array Array with model names
     */
    public function getUnlinkedModels() {
        return $this->unlinkedModels;
    }

    /**
     * Gets the table definition of this model
     * @return zibo\library\orm\definition\ModelTable
     */
    public function getModelTable() {
        return $this->table;
    }

    /**
     * Gets whether this model has a certain field
     * @param string $fieldName Name of the field to check
     * @return boolean True if the model contains the field, false otherwise
     */
    public function hasField($fieldName) {
        return $this->table->hasField($fieldName);
    }

    /**
     * Gets a field from this model
     * @param string $fieldName Name of the field
     * @return zibo\library\orm\definition\field\ModelField
     */
    public function getField($fieldName) {
        return $this->table->getField($fieldName);
    }

    /**
     * Gets all the fields of this model
     * @return array Array with ModelField objects
     */
    public function getFields() {
        return $this->table->getFields();
    }

    public function getLocalizedFields() {
        $this->parseMeta();

        return $this->localizedFields;
    }

    /**
     * Gets the property fields of this model
     * @return array Array with ModelField objects
     */
    public function getProperties() {
        $this->parseMeta();

        return $this->properties;
    }

    /**
     * Gets the belongs to fields of this model
     * @return array Array with BelongsToField objects
     */
    public function getBelongsTo() {
        $this->parseMeta();

        return $this->belongsTo;
    }

    /**
     * Gets the has one fields of this model
     * @return array Array with HasOneField objects
     */
    public function getHasOne() {
        $this->parseMeta();

        return $this->hasOne;
    }

    /**
     * Gets the has many fields of this model
     * @return array Array with HasManyField objects
     */
    public function getHasMany() {
        $this->parseMeta();

        return $this->hasMany;
    }

    /**
     * Gets the fields with a relation to the provided model
     * @param string $modelName Name of the relation model
     * @param integer $type Type identifier to get only the provided type
     * @return array An array with the fields for the provided type. If no type has been provided, an
     *              array with the type identifier as key and an array with the fields as value
     */
    public function getRelation($modelName, $type = null) {
        return $this->table->getRelationFields($modelName, $type);
    }

    /**
     * Gets whether this model has relation fields or only property fields
     * @return boolean True when the model has relation fields, false otherwise
     */
    public function hasRelations() {
        return $this->table->hasRelationFields();
    }

    /**
     * Gets whether this model has a relation with the provided model
     * @param string $modelName Name of the relation model
     * @param integer $type Type identifier to get only the provided type
     * @return boolean True if there is a field with a relation to the provided model, false otherwise
     */
    public function hasRelationWith($modelName, $type = null) {
        $relations = $this->table->getRelationFields($modelName);

        if ($type !== null) {
            return array_key_exists($type, $relations) && !empty($relations[$type]);
        }

        return !(empty($relations[ModelTable::HAS_MANY]) && empty($relations[ModelTable::HAS_ONE]) && empty($relations[ModelTable::BELONGS_TO]));
    }

    /**
     * Gets whether the relation of the provided field is a relation with the model itself
     * @param string $fieldName Name of the relation field
     * @return boolean True when the relation of the provided field is with the model itself, false otherwise
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    public function isRelationWithSelf($fieldName) {
        $relationMeta = $this->getRelationMeta($fieldName);
        return $relationMeta->isRelationWithSelf();
    }

    /**
     * Gets the relation model of the field
     * @param string $fieldName Name of the field
     * @return zibo\library\orm\model\Model
     */
    public function getRelationModel($fieldName) {
        $field = $this->table->getField($fieldName);

        if (!($field instanceof RelationField)) {
            throw new ModelException($fieldName . ' is not a relation field');
        }

        $relationModelName = $field->getRelationModelName();

        return ModelManager::getInstance()->getModel($relationModelName);
    }

    /**
     * Gets the model table definition of the relation model of the provided field
     * @param string $fieldName Name of the relation field
     * @return zibo\library\definition\ModelTable
     */
    public function getRelationModelTable($fieldName) {
        return $this->getRelationModel($fieldName)->getMeta()->getModelTable();
    }

    /**
     * Gets the link model for the provided relation field
     * @param string $fieldName Name of the relation field
     * @return null|zibo\library\orm\model\Model The link model if set, null otherwise
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    public function getRelationLinkModel($fieldName) {
        $relationMeta = $this->getRelationMeta($fieldName);

        $linkModelName = $relationMeta->getLinkModelName();

        if (!$linkModelName) {
            return null;
        }

        return ModelManager::getInstance()->getModel($linkModelName);
    }

    /**
     * Gets the model table definition of the link model of the provided field
     * @param string $fieldName Name of the relation field
     * @return zibo\library\definition\ModelTable
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    public function getRelationLinkModelTable($fieldName) {
        return $this->getRelationLinkModel($fieldName)->getMeta()->getModelTable();
    }

    /**
     * Gets the table expression of the relation model
     * @param string $fieldName Name of the relation field
     * @return zibo\library\database\statement\expression\TableExpression
     */
    public function getRelationTable($fieldName) {
        $model = $this->getRelationModel($fieldName);
        return new TableExpression($model->getName(), $fieldName);
    }

    /**
     * Gets the table expression of the localized relation model
     * @param string $fieldName Name of the relation field
     * @return zibo\library\database\statement\expression\TableExpression
     */
    public function getRelationLocalizedTable($fieldName) {
        $model = $this->getRelationModel($fieldName);

        if ($model->getMeta()->isLocalized()) {
            return new TableExpression($model->getName() . LocalizedModel::MODEL_SUFFIX, $fieldName . LocalizedModel::MODEL_SUFFIX);
        }

        return null;
    }

    /**
     * Gets the table expression of the link model
     * @param string $fieldName Name of the relation field
     * @return null|zibo\library\database\statement\expression\TableExpression
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    public function getRelationLinkTable($fieldName) {
        $model = $this->getRelationLinkModel($fieldName);
        if (!$model) {
            return null;
        }

        $alias = lcfirst($model->getName());

        return new TableExpression($model->getName(), $alias);
    }

    /**
     * Gets the fields of the relation model of the provided field
     * @param string $fieldName Name of the field to get the relation fields of
     * @return array Array with ModelField objects
     */
    public function getRelationFields($fieldName) {
        return $this->getRelationModelTable($fieldName)->getFields();
    }

    /**
     * Gets the foreign key for the provided relation field
     * @param string $fieldName Name of the relation field
     * @return null|zibo\library\orm\definition\field\ModelField
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    public function getRelationForeignKey($fieldName) {
        $relationMeta = $this->getRelationMeta($fieldName);
        return $relationMeta->getForeignKey();
    }

    /**
     * Gets the foreign key with the link model for the provided relation field
     * @param string $fieldName Name of the relation field
     * @return null|zibo\library\orm\definition\field\ModelField
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    public function getRelationForeignKeyToSelf($fieldName) {
        $relationMeta = $this->getRelationMeta($fieldName);
        return $relationMeta->getForeignKeyToSelf();
    }

    /**
     * Gets whether the relation of the provided field is a many to many relation
     * @param string $fieldName Name of the relation field
     * @return boolean True if the relation is a many to many relation, false otherwise
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    public function isHasManyAndBelongsToMany($fieldName) {
        $relation = $this->getRelationMeta($fieldName);
        return $relation->isHasManyAndBelongsToMany();
    }

    /**
     * Gets the order statement string for the relation of the provided has many field
     * @param string $fieldName Name of the has many field
     * @return null|string
     */
    public function getRelationOrder($fieldName) {
        $field = $this->table->getField($fieldName);

        if (!($field instanceof HasManyField)) {
            throw new ModelException($fieldName . ' is not a has many field');
        }

        return $field->getRelationOrder();
    }

    /**
     * Gets the driver of the database connection
     * @return zibo\library\database\driver\Driver
     */
    public function getConnection() {
        return DatabaseManager::getInstance()->getConnection();
    }

    /**
     * Gets the relation meta for the provided relation field
     * @param string $fieldName Name of the relation field
     * @return RelationMeta
     * @throws zibo\library\orm\exception\ModelException when no relation meta could be found for the provided field
     */
    private function getRelationMeta($fieldName) {
        $this->parseMeta();

        if (!array_key_exists($fieldName, $this->relations)) {
            throw new ModelException('Could not find the relation meta of ' . $fieldName);
        }

        return $this->relations[$fieldName];
    }

    /**
     * Makes sure the model table is parsed so the data for the methods of this object are initialized
     * @return null
     */
    public function parseMeta() {
        if ($this->isParsed) {
            return;
        }

        $this->localizedFields = array();
        $this->properties = array();
        $this->belongsTo = array();
        $this->hasOne = array();
        $this->hasMany = array();
        $this->relations = array();

        $modelManager = ModelManager::getInstance();

        $fields = $this->table->getFields();
        foreach ($fields as $fieldName => $field) {
            if ($fieldName != VersionField::NAME && $field->isLocalized()) {
                $this->localizedFields[$fieldName] = $field;
            }

            if ($field instanceof HasField) {
                $this->parseHasField($field, $modelManager);
            } elseif ($field instanceof BelongsToField) {
                $this->parseBelongsToField($field, $modelManager);
            } else {
                $this->parsePropertyField($field);
            }
        }

        $this->isParsed = true;
    }

    /**
     * Parses a has field into the meta
     * @param zibo\library\orm\definition\field\HasField $field
     * @param zibo\library\orm\ModelManager $modelManager Instance of the model manager
     * @return null
     */
    private function parseHasField(HasField $field, ModelManager $modelManager) {
        $name = $field->getName();
        $modelName = $this->table->getName();

        $relationModelName = $field->getRelationModelName();
        $relationModel = $modelManager->getModel($relationModelName);

        $relation = new RelationMeta();
        $relation->setIsRelationWithSelf($relationModelName == $modelName);

        $linkModelName = $field->getLinkModelName();
        if ($field->isLocalized()) {
            $localizedMeta = $this->getLocalizedModel()->getMeta();
            $localizedField = $localizedMeta->getField($name);

            $linkModelName = $localizedField->getLinkModelName();
            $field->setLinkModelName($linkModelName);
            $relation->setLinkModelName($linkModelName);
        } elseif ($linkModelName) {
            $relation->setLinkModelName($linkModelName);

            $linkModel = $modelManager->getModel($linkModelName);
            $linkModelTable = $linkModel->getMeta()->getModelTable();

            if (!$relation->isRelationWithSelf()) {
                $relation->setForeignKey($this->getForeignKey($linkModelTable, $relationModelName));
                $relation->setForeignKeyToSelf($this->getForeignKey($linkModelTable, $modelName));
            } else {
                $relation->setForeignKey($this->getForeignKeys($linkModelTable, $modelName));
            }
        } else {
            $relationModelTable = $relationModel->getMeta()->getModelTable();
            $foreignKey = $field->getForeignKeyName();

            $relation->setForeignKey($this->getForeignKey($relationModelTable, $modelName, $foreignKey));
        }

        $this->relations[$name] = $relation;

        if ($field instanceof HasOneField) {
            $this->hasOne[$name] = $field;
            return;
        }

        if ($linkModelName) {
            $relation->setIsHasManyAndBelongsToMany(true);
        }
        $this->hasMany[$name] = $field;
    }

    /**
     * Parses a belongs to field in the meta
     * @param zibo\library\orm\definition\field\BelongsToField $field
     * @return null
     * @param zibo\library\orm\ModelManager $modelManager Instance of the model manager
     */
    private function parseBelongsToField(BelongsToField $field, ModelManager $modelManager) {
        $name = $field->getName();
        $modelName = $this->getName();
        $relationModelName = $field->getRelationModelName();

        try {
            $relationModel = $modelManager->getModel($relationModelName);
        } catch (OrmException $exception) {
            throw new ModelException('No relation model found for field ' . $name . ' in ' . $modelName);
        }

        $relation = new RelationMeta();
        $relation->setIsRelationWithSelf($relationModelName == $modelName);

        $this->relations[$name] = $relation;
        $this->belongsTo[$name] = $field;

        $relationFields = $relationModel->getMeta()->getRelation($modelName);

        if (!$relationFields[ModelTable::BELONGS_TO] && !$relationFields[ModelTable::HAS_MANY] && !$relationFields[ModelTable::HAS_ONE]) {
            return;
        }

        if ($relationFields[ModelTable::HAS_MANY]) {
            $relationType = ModelTable::HAS_MANY;
        } else if ($relationFields[ModelTable::HAS_ONE]) {
            $relationType = ModelTable::HAS_ONE;
        } else {
            return;
        }

        $relationField = array_shift($relationFields[$relationType]);
        if ($relationField->isLocalized()) {
            $linkModelName = $field->getLinkModelName();
            if (empty($linkModelName)) {
            	throw new ModelException('No link model found for field ' . $name . ' in ' . $modelName);
            }

            $relation->setLinkModelName($linkModelName);
        }

    }

    /**
     * Parses a property field in the meta
     * @param zibo\library\orm\definition\field\ModelField $field
     * @return null
     */
    private function parsePropertyField(ModelField $field) {
        $this->properties[$field->getName()] = $field;
    }

    /**
     * Gets the foreign key from the provided model table for the provided relation model
     * @param zibo\library\orm\definition\ModelTable $modelTable Table definition of the model
     * @param string $relationModelName Model name to get the foreign keys of
     * @param string $foreignKey Name of the foreign key
     * @return array Array with ModelField objects
     * @throws zibo\library\orm\exception\ModelException when the provided foreign key is not found in the model table
     * @throws zibo\library\orm\exception\ModelException when there are multiple foreign keys
     */
    private function getForeignKey(ModelTable $modelTable, $relationModelName, $foreignKey = null) {
        $foreignKeys = $this->getForeignKeys($modelTable, $relationModelName);

        if ($foreignKey) {
            if (array_key_exists($foreignKey, $foreignKeys)) {
                return $foreignKeys[$foreignKey];
            }

            throw new ModelException('Foreign key ' . $foreignKey . ' not found in ' . $relationModelName);
        }

        if (count($foreignKeys) == 1) {
            return array_pop($foreignKeys);
        }

        throw new ModelException('There are multiple relations with ' . $relationModelName . '. Please define a foreign key.');
    }

    /**
     * Gets the foreign keys from the provided model table for the provided relation model. When no foreign keys are found and the relation
     * model is a localized model, the unlocalized model will be queried for the foreign keys.
     * @param zibo\library\orm\definition\ModelTable $modelTable Table definition of the model
     * @param string $relationModelName Model name to get the foreign keys of
     * @return array Array with ModelField objects
     * @throws zibo\library\orm\exception\ModelException when there are no foreign keys found the provided model
     */
    private function getForeignKeys(ModelTable $modelTable, $relationModelName) {
        if (!$relationModelName) {
            throw new ModelException('Provided relation model name is empty');
        }

        $foreignKeys = $modelTable->getRelationFields($relationModelName, ModelTable::BELONGS_TO);

        if (!$foreignKeys) {
            if (preg_match('/' . LocalizedModel::MODEL_SUFFIX . '$/', $relationModelName)) {
                $relationModelName = substr($relationModelName, 0, strlen(LocalizedModel::MODEL_SUFFIX) * -1);
                $foreignKeys = $modelTable->getRelationFields($relationModelName, ModelTable::BELONGS_TO);
            }

            if (!$foreignKeys) {
                throw new ModelException('No foreign key found for ' . $relationModelName . ' found in ' . $modelTable->getName());
            }
        }

        return $foreignKeys;
    }

}