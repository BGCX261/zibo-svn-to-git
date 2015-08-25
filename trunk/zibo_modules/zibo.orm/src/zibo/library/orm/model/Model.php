<?php

namespace zibo\library\orm\model;

use zibo\library\orm\model\meta\ModelMeta;

/**
 * Interface for a data model
 */
interface Model {

    /**
     * Constructs a new data model
     * @param ModelMeta $modelMeta Meta data of the model
     * @return null
     */
    public function __construct(ModelMeta $modelMeta);

    /**
     * Gets the name of this model
     * @return string
     */
    public function getName();

    /**
     * Gets the meta data of this model
     * @return ModelMeta
     */
    public function getMeta();

    /**
     * Gets the database result parser of this model
     * @return zibo\library\orm\query\parser\ResultParser
     */
    public function getResultParser();

    /**
     * Creates a new data object for this model
     * @param boolean $initialize True to create a data object with default values (default), false to create an empty data object
     * @return mixed A new data object for this model
     */
    public function createData($initialize = true);

    /**
     * Creates a model query for this model
     * @param int $recursiveDepth Number of relation levels to fetch
     * @param string $locale The locale of the data
     * @param boolean $includeUnlocalized True to include data which is not localized, false otherwise
     * @return zibo\library\orm\query\ModelQuery
     */
    public function createQuery($recursiveDepth = 1, $locale = null, $includeUnlocalized = false);

    /**
     * Validates a data object of this model
     * @param mixed $data Data object of the model
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when one of the fields is not validated
     */
    public function validate($data);

    /**
     * Saves data to the model
     * @param mixed $data A data object or an array of data objects when no id argument is provided, the value for the field otherwise
     * @param string $fieldName Name of the field to save
     * @param int $id Primary key of the data to save, $data will be considered as the value for the provided field name
     * @param string $locale The locale of the value
     * @return null
     * @throws Exception when the data could not be saved
     */
    public function save($data, $fieldName = null, $id = null, $locale = null);

    /**
     * Deletes data from the model
     * @param mixed $data Primary key of the data, a data object or an array with the previous as value
     * @return null
     * @throws Exception when the data could not be deleted
     */
    public function delete($data);

    /**
     * Clears the cache of this model
     * @return null
     */
    public function clearCache();

}