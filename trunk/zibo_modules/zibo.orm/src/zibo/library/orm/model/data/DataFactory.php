<?php

namespace zibo\library\orm\model\data;

use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\ObjectFactory;

/**
 * Factory for model data
 */
class DataFactory {

    /**
     * Default object for a empty data object
     * @var mixed
     */
    private $emptyData;

    /**
     * Default object for an initialized data object
     * @var mixed
     */
    private $initializedData;

    /**
     * Construct this data container factory
     * @param zibo\library\orm\model\meta\ModelMeta $meta the meta of the model
     * @return null;
     */
    public function __construct(ModelMeta $meta) {
        $objectFactory = new ObjectFactory();
        $this->emptyData = $this->initData($meta, $objectFactory, false);
        $this->initializedData = $this->initData($meta, $objectFactory, true);
    }

    /**
     * Get a new data object for the model
     * @param boolean $initialized true to get a initialized data object with default values, false to get an empty object
     * @return mixed a new data container for the model
     */
    public function createData($initialized = true) {
        if ($initialized) {
            return clone($this->initializedData);
        }

        return clone($this->emptyData);
    }

    /**
     * Create a data container for the model
     * @param zibo\library\orm\model\meta\ModelMeta $meta the meta of the model
     * @param zibo\library\ObjectFactory $objectFactory factory for new objects
     * @param boolean $initialize set to true to get an object with default values, false to get an empty object
     * @return mixed a data container for the model
     */
    private function initData(ModelMeta $meta, ObjectFactory $objectFactory, $initialize) {
        $data = $objectFactory->create($meta->getDataClassName());
        if (!$initialize) {
            return $data;
        }

        $fields = $meta->getFields();
        foreach ($fields as $field) {
            $name = $field->getName();

            if ($field instanceof BelongsToField || $field instanceof HasOneField) {
                $data->$name = null;
                continue;
            }

            if ($field instanceof HasManyField) {
                $data->$name = array();
                continue;
            }

            $data->$name = $field->getDefaultValue();
        }

        return $data;
    }

}