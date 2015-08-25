<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\ValueExpression;

/**
 * Statement to update records in a table or tables
 */
class UpdateStatement extends ConditionalStatement {

    /**
     * Array containing ValueExpression objects
     * @var array
     */
    private $values = array();

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