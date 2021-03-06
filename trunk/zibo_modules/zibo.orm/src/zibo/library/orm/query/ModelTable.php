<?php

namespace zibo\library\orm\query;

use zibo\library\orm\exception\OrmException;
use zibo\library\orm\ModelManager;
use zibo\library\String;

/**
 * Definition of a table for a ModelQuery
 */
class ModelTable {

    /**
     * Name of the model
     * @var string
     */
    private $modelName;

    /**
     * Alias for this table
     * @var string
     */
    private $alias;

    /**
     * Array with the joins on this table
     * @var array
     */
    private $joins;

    /**
     * Constructs a new model table
     * @param string $modelName Name of the model
     * @param string $alias Alias for this table
     * @param zibo\library\orm\model\ModelMeta $meta Meta of the model of the query, to check if the alias is not a field name
     * @return null
     */
    public function __construct($modelName, $alias, ModelMeta $meta = null) {
        $this->setModelName($modelName);
        $this->setAlias($alias, $meta);
        $this->joins = array();
    }

    /**
     * Sets the name of the model for this table
     * @param string $modelName
     * @return null
     * @throws zibo\ZiboException when the name of the model is empty or invalid
     * @throws zibo\library\orm\exception\OrmException when the model does not exist
     */
    private function setModelName($modelName) {
        if (String::isEmpty($modelName)) {
            throw new OrmException('Provided model name is empty');
        }

        ModelManager::getInstance()->getModel($modelName);

        $this->modelName = $modelName;
    }

    /**
     * Gets the name of the model of this table
     * @return string
     */
    public function getModelName() {
        return $this->modelName;
    }

    /**
     * Sets the alias for this table
     * @param string $alias Alias for this table
     * @param zibo\library\orm\model\ModelMeta $meta Meta of the model of the query, to check if the alias is not a field name
     * @return null
     */
    private function setAlias($alias, ModelMeta $meta = null) {
        if (String::isEmpty($alias)) {
            throw new OrmException('Provided alias is empty');
        }

        if ($meta && $meta->hasField($alias)) {
            throw new OrmException('Provided alias is a field name of model ' . $meta->getName());
        }

        $this->alias = $alias;
    }

    /**
     * Gets the alias of this table
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Adds a join to this table
     * @param ModelJoin $join
     * @return null
     */
    public function addJoin(ModelJoin $join) {
        $this->joins[$join->getTable()->getAlias()] = $join;
    }

    /**
     * Gets the joins with this table
     * @return array Array with ModelJoin objects
     */
    public function getJoins() {
        return $this->joins;
    }

}