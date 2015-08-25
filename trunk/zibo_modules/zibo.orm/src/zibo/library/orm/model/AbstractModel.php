<?php

namespace zibo\library\orm\model;

use zibo\core\Zibo;

use zibo\library\database\manipulation\statement\Statement;
use zibo\library\i18n\I18n;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\model\data\validator\SimpleDataValidator;
use zibo\library\orm\model\data\DataFactory;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\query\parser\ResultParser;
use zibo\library\orm\query\CachedModelQuery;
use zibo\library\orm\query\ModelQuery;
use zibo\library\orm\ModelManager;
use zibo\library\validation\exception\ValidationException;

use zibo\orm\Module;

use \Exception;
use \Serializable;

/**
 * Abstract implementation of a data model
 */
abstract class AbstractModel implements Model, Serializable {

    /**
     * Meta of this model
     * @var ModelMeta
     */
    protected $meta;

    /**
     * Factory for data objects
     * @var zibo\library\orm\model\data\DataFactory
     */
    protected $dataFactory;

    /**
     * Validator for data objects
     * @var zibo\library\orm\model\data\validator\DataValidator
     */
    protected $dataValidator;

    /**
     * Parser for database results
     * @var zibo\library\orm\query\parser\ResultParser
     */
    protected $resultParser;

    /**
     * Constructs a new data model
     * @param ModelMeta $modelMeta Meta data of the model
     * @return null
     */
    public function __construct(ModelMeta $meta) {
        $this->meta = $meta;

        $this->dataFactory = new DataFactory($this->meta);
        $this->dataValidator = new SimpleDataValidator($this->meta);
        $this->resultParser = new ResultParser($this);

        $this->initialize();
    }

    /**
     * Hook performed at the end of the constructor
     * @return null
     */
    protected function initialize() {

    }

    /**
     * Serializes this model
     * @return string Serialized model
     */
    public function serialize() {
        return serialize($this->meta);
    }

    /**
     * Unserializes the provided string into a model
     * @param string $serialized Serialized string of a model
     * @return null
     */
    public function unserialize($serialized) {
        $this->meta = unserialize($serialized);

        $this->dataFactory = new DataFactory($this->meta);
        $this->dataValidator = new SimpleDataValidator($this->meta);
        $this->resultParser = new ResultParser($this);
    }

    /**
     * Gets the name of this model
     * @return string
     */
    public function getName() {
        return $this->meta->getName();
    }

    /**
     * Gets the meta data of this model
     * @return ModelMeta
     */
    public function getMeta() {
        return $this->meta;
    }

    /**
     * Gets the database result parser of this model
     * @return zibo\library\orm\query\parser\ResultParser
     */
    public function getResultParser() {
        return $this->resultParser;
    }

    /**
     * Creates a new data object for this model
     * @param boolean $initialize True to create a data object with default values (default), false to create an empty data object
     * @return mixed A new data object for this model
     */
    public function createData($initialize = true) {
        return $this->dataFactory->createData($initialize);
    }

    /**
     * Creates a model query for this model
     * @param int $recursiveDepth Number of relation levels to fetch
     * @param string $locale The locale of the data
     * @param boolean $includeUnlocalized True to include data which is not localized, false otherwise
     * @return zibo\library\orm\query\ModelQuery
     */
    public function createQuery($recursiveDepth = 1, $locale = null, $includeUnlocalized = false) {
//        $query = new ModelQuery($this);
        $query = new CachedModelQuery($this);
        $query->setRecursiveDepth($recursiveDepth);
        $query->setLocale($locale);
        $query->setWillIncludeUnlocalizedData($includeUnlocalized);

        return $query;
    }

    /**
     * Validates a data object of this model
     * @param mixed $data Data object of the model
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when one of the fields is not valid
     */
    public function validate($data) {
        $exception = new ValidationException('Validation errors occured in ' . $this->getName());

        $this->dataValidator->validateData($exception, $data);

        if ($exception->hasErrors()) {
            throw $exception;
        }
    }

    /**
     * Validates a value for a certain field of this model
     * @param string $fieldName Name of the field
     * @param mixed $value Value to validate
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when the field is not valid
     */
    protected function validateField($fieldName, $value) {
        $exception = new ValidationException('Validation errors occured in ' . $this->getName());

        $this->dataValidator->validateField($exception, $fieldName, $value);

        if ($exception->hasErrors()) {
            throw $exception;
        }
    }

    /**
     * Saves data to the model
     * @param mixed $data A data object or an array of data objects when no id argument is provided, the value for the field otherwise
     * @param string $fieldName Name of the field to save
     * @param integer $id Primary key of the data to save, $data will be considered as the value for the provided field name
     * @param string $locale The locale of the data, only used when the id argument is provided
     * @return null
     * @throws Exception when the data could not be saved
     */
    public function save($data, $fieldName = null, $id = null, $locale = null) {
        $isTransactionStarted = $this->startTransaction();
        $isFieldNameProvided = !is_null($fieldName);

        try {
            if (is_array($data)) {
                if ($isFieldNameProvided) {
                    foreach ($data as $d) {
                        $this->saveField($d, $fieldName);
                    }
                } else {
                    foreach ($data as $d) {
                        $this->saveData($d);
                    }
                }
            } elseif ($isFieldNameProvided) {
                if (is_array($id)) {
                    foreach ($id as $pk) {
                        if (is_object($pk)) {
                            $this->saveField($data, $fieldName, $pk->id, $locale);
                        } else {
                            $this->saveField($data, $fieldName, $pk, $locale);
                        }
                    }
                } else {
                    $this->saveField($data, $fieldName, $id, $locale);
                }
            } else {
                $this->saveData($data);
            }

            $this->commitTransaction($isTransactionStarted);
        } catch (Exception $e) {
            $this->rollbackTransaction($isTransactionStarted);
            throw $e;
        }
    }

    /**
     * Saves a data object to the model
     * @param mixed $data A data object of this model
     * @return null
     * @throws Exception when the data could not be saved
     */
    abstract protected function saveData($data);

    /**
     * Saves a field from data to the model
     * @param mixed $data A data object or the value to save when the id argument is provided
     * @param string $fieldName Name of the field to save
     * @param integer $id Primary key of the data to save, $data will be considered as the value
     * @param string $locale The locale of the data, only used when the id argument is provided
     * @return null
     * @throws Exception when the field could not be saved
     */
    abstract protected function saveField($data, $fieldName, $id = null, $locale = null);

    /**
     * Deletes data from this model
     * @param mixed $data Primary key of the data, a data object or an array with the previous as value
     * @return null
     * @throws Exception when the data could not be deleted
     */
    public function delete($data) {
        $isTransactionStarted = $this->startTransaction();

        try {
            if (is_array($data)) {
                foreach ($data as $d) {
                    $this->deleteData($d);
                }
            } else {
                $this->deleteData($data);
            }

            $this->commitTransaction($isTransactionStarted);
        } catch (Exception $e) {
            $this->rollbackTransaction($isTransactionStarted);
            throw $e;
        }
    }

    /**
     * Deletes data from this model
     * @param mixed $data Primary key of the data or a data object of this model
     * @return mixed The full data which has been deleted
     */
    abstract protected function deleteData($data);

    /**
     * Clears the result cache of this model
     * @return null
     */
    public function clearCache() {
        ModelManager::getInstance()->getModelCache()->clearResults($this->getName());
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Clearing result cache for model ' . $this->getName(), '', 0, Module::LOG_NAME);
    }

    /**
     * Gets the primary key of data
     * @param mixed $data Primary key or a data object
     * @return integer The primary key of the data
     * @throws zibo\library\orm\exception\ModelException when no primary key could be retrieved from the data
     */
    protected function getPrimaryKey($data) {
        if (is_numeric($data)) {
            return $data;
        }

        $this->meta->isValidData($data);

        if (!empty($data->id)) {
            return $data->id;
        }

        throw new ModelException('No primary key found in the provided data');
    }

    /**
     * Gets the locale for the data
     * @param string $locale when no locale passed, the current locale will be used
     * @return string Code of the locale
     */
    protected function getLocale($locale) {
    	$i18n = I18n::getInstance();

    	if ($locale === null) {
        	$locale = $i18n->getLocale()->getCode();
        } else {
            $i18n->getLocale($locale);
        }

        return $locale;
    }

    /**
     * Gets another model
     * @param string $modelName Name of the model
     * @return Model
     * @throws zibo\library\orm\exception\OrmException when the provided model could not be retrieved
     */
    protected function getModel($modelName) {
        return ModelManager::getInstance()->getModel($modelName);
    }

    /**
     * Gets the model to log model actions
     * @return zibo\library\orm\model\LogModel
     */
    protected function getLogModel() {
        return $this->getModel(LogModel::NAME);
    }

    /**
     * Executes a statement on the database connection of this model
     * @param zibo\library\database\manipulation\statement\Statement
     * @return zibo\library\database\DatabaseResult
     */
    protected function executeStatement(Statement $statement) {
        $connection = $this->meta->getConnection();
        return $connection->executeStatement($statement);
    }

    /**
     * Starts a new transaction on the database connection of this model
     * @return boolean True if a new transaction is started, false when a transaction is already in progress
     */
    protected function startTransaction() {
        $connection = $this->meta->getConnection();
        return $connection->startTransaction();
    }

    /**
     * Performs a commit on the transaction on the database connection of this model
     * @param boolean $isTransactionStarted The commit is only performed when true is provided
     * @return null
     */
    protected function commitTransaction($isTransactionStarted) {
        if ($isTransactionStarted) {
            $connection = $this->meta->getConnection();
            $connection->commitTransaction();
        }
    }

    /**
     * Performs a rollback on the transaction on the database connection of this model
     * @param boolean $isTransactionStarted The rollback is only performed when true is provided
     * @return null
     */
    protected function rollbackTransaction($isTransactionStarted) {
        if (!$isTransactionStarted) {
            return;
        }

        $connection = $this->meta->getConnection();
        $connection->rollbackTransaction();
    }

}