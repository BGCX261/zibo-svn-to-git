<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\conditino\Condition;
use zibo\library\database\manipulation\expression\Expression;
use zibo\library\database\manipulation\expression\LimitExpression;
use zibo\library\Boolean;

/**
 * Statement to fetch records from the database
 */
class SelectStatement extends ConditionalStatement {

    /**
     * Flag for distinctive selection
     * @var boolean
     */
    protected $distinct = false;

    /**
     * Array containing the expressions to select
     * @var array
     */
    protected $fields = array();

    /**
     * Array containing the having conditions
     * @var array
     */
    protected $having = array();

    /**
     * Array containing the group expressions
     * @var array
     */
    protected $group = array();

    /**
     * Array containing order expressions for this statement
     * @var array
     */
    protected $order = array();

    /**
     * Limitation of this statement
     * @var zibo\library\database\manipulation\expression\LimitExpression
     */
    protected $limit = null;

    /**
     * Set whether this query is distinctive
     * @param boolean flag true if distinctive, false otherwise
     * @return null
     */
    public function setDistinct($flag) {
        $this->distinct = Boolean::getBoolean($flag);
    }

    /**
     * Checks whether this query is distinctive
     * @return boolean true if distinctive, false otherwiser
     */
    public function isDistinct() {
        return $this->distinct;
    }

    /**
     * Add a query field
     * @param zibo\library\database\manipulation\expression\Expression $field
     * @return null
     */
    public function addField(Expression $field) {
        $this->fields[] = $field;
    }

    /**
     * Get the query fields
     * @return array array with Expression objects
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Removes all the fields from this statement
     * @return null
     */
    public function clearFields() {
        $this->fields = array();
    }

    /**
     * Add a having condition to this statement
     * @param zibo\library\database\manipulation\expression\condition\Condition $condition
     * @return null
     */
    public function addHaving(Condition $condition) {
        $this->having[] = $condition;
    }

    /**
     * Get the having conditions of this statement
     * @return array array with Condition objects
     */
    public function getHaving() {
        return $this->having;
    }

    /**
     * Add a group by field to the query
     * @param zibo\library\database\manipulation\expression\Expression $field
     * @return null
     */
    public function addGroupBy(Expression $field) {
        $this->group[] = $field;
    }

    /**
     * Get the group by fields
     * @return array array with Field objects or fieldnames
     */
    public function getGroupBy() {
        return $this->group;
    }

    /**
     * Removes all the order definitions from this statement
     * @return null
     */
    public function clearOrderBy() {
        $this->order = array();
    }

    /**
     * Add an order definition
     * @param zibo\library\database\manipulation\expression\Expression $order
     * @return null
     */
    public function addOrderBy(Expression $order) {
        $this->order[] = $order;
    }

    /**
     * Get the order by's
     * @return array array with Expression objects
     */
    public function getOrderBy() {
        return $this->order;
    }

    /**
     * Set the limitation
     * @param int $rowCount number of rows
     * @param int $offset starting row (optional)
     * @return null
     */
    public function setLimit($rowCount, $offset = 0) {
        $this->limit = new LimitExpression($rowCount, $offset);
    }

    /**
     * Clear the limitation
     * @return null
     */
    public function clearLimit() {
        $this->limit = null;
    }

    /**
     * Check if this statement has limitation
     * @return boolean true if a limitation has been set, false otherwise
     */
    public function hasLimit() {
        return $this->limit === null;
    }

    /**
     * Get the limitation
     * @return zibo\library\database\manipulation\expression\LimitExpression
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * Get the number of rows of the limitation
     * @return int number of rows to fetch
     */
    public function getLimitCount() {
        return $this->limit->getRowCount();
    }

    /**
     * Get the offset of the limitation
     * @return int offset
     */
    public function getLimitOffset() {
        return $this->limit->getOffset();
    }

}