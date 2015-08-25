<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\expression\ValueExpression;

/**
 * Statement to insert records in a table or tables
 */
class InsertStatement extends TableStatement {

    /**
     * Array containing ValueExpression objects
     * @var array
     */
    private $values = array();

    /**
     * Add the table this insert statement (only 1 table allowed)
     * @param zibo\library\database\manipulation\expression\TableExpression $table
     * @return null;
     */
    public function addTable(TableExpression $table) {
        if (count($this->tables)) {
            throw new DatabaseException('Only 1 table allowed for this statement');
        }
        parent::addTable($table);
    }

    /**
     * Add a field with it's value to the statement
     * @param FieldExpression $field
     * @param mixed $value
     * @return null
     */
    public function addValue(FieldExpression $field, $value) {
        $expression = new ValueExpression($field, $value);
        $this->values[] = $expression;
    }

    /**
     * Get the values of the statement
     * @return array Array with ValueExpression objects
     */
    public function getValues() {
        return $this->values;
    }

}