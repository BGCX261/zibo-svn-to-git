<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

use zibo\ZiboException;


/**
 * Table definition for a statement
 */
class TableExpression extends AliasExpression {

    /**
     * Name of the table
     * @var string
     */
    private $name;

    /**
     * Array containing the joins with this table
     * @var array
     */
    private $joins;

    /**
     * Construct the table definition
     * @param string $name name of the table
     * @param string $alias alias of the table (optional)
     */
    public function __construct($name, $alias = null) {
        $this->setName($name);
        $this->setAlias($alias);
        $this->joins = array();
    }

    /**
     * Set the name of this table
     * @param string $name
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the name is empty or not a string
     */
    private function setName($name) {
        try {
            if (String::isEmpty($name)) {
                throw new DatabaseException('Provided name is empty');
            }
        } catch (ZiboException $e) {
            throw new DatabaseException('Provided name is not a string');
        }

        $this->name = $name;
    }

    /**
     * Get the name of this table
     * @return string name of this table
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add a join to this table
     * @param JoinExpression $join
     * @return null
     */
    public function addJoin(JoinExpression $join) {
        $table = $join->getTable();

        $alias = $table->getAlias();
        if (empty($alias)) {
            $alias = $table->getName();
        }

        $this->joins[$alias] = $join;
    }

    /**
     * Get the joins of this table
     * @return array array of joins with the alias as key and the JoinExpression object as value
     */
    public function getJoins() {
        return $this->joins;
    }

}