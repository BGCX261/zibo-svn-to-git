<?php

namespace zibo\library\orm\definition\field;

use zibo\library\orm\exception\OrmException;
use zibo\library\String;

/**
 * Definition of a hasMany relation field
 */
class HasManyField extends HasField {

    /**
     * Order statement string for the relation
     * @var string
     */
    protected $relationOrder;

    /**
     * Name of the field to index the values on
     * @var string
     */
    protected $indexOn;

    /**
     * Returns the fields to serialize
     * @return array Array with field names
     */
    public function __sleep() {
        $fields = parent::__sleep();

        if ($this->relationOrder) {
            $fields[] = 'relationOrder';
        }

        if ($this->indexOn) {
            $fields[] = 'indexOn';
        }

        return $fields;
    }

    /**
     * Sets the field to index the values on
     * @param string $indexOn Name of the field
     * @return null
     */
    public function setIndexOn($indexOn) {
        if ($indexOn !== null && String::isEmpty($indexOn)) {
            throw new OrmException('Provided index field is empty');
        }

        $this->indexOn = $indexOn;
    }

    /**
     * Gets the field to index the values on
     * @return string Name of the field
     */
    public function getIndexOn() {
        return $this->indexOn;
    }

    /**
     * Sets the order statement string for the relation
     * @param string $order
     * @return null
     */
    public function setRelationOrder($order) {
        if ($order !== null && String::isEmpty($order)) {
            throw new OrmException('Provided order statement is empty');
        }
        $this->relationOrder = $order;
    }

    /**
     * Gets the order statement string for the relation
     * @return string
     */
    public function getRelationOrder() {
        return $this->relationOrder;
    }

}