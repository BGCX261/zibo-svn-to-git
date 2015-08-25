<?php

namespace zibo\library\database\manipulation\expression;

/**
 * Value definition for a insert or update statement
 */
class ValueExpression extends Expression {

    /**
     * Field of the value
     * @var FieldExpression
     */
    private $field;

    /**
     * The value
     * @var Expression
     */
    private $value;

    /**
     * Construct the value expression
     * @param FieldExpression $field
     * @param mixed|Expression $value
     * @return null
     */
    public function __construct(FieldExpression $field, $value = null) {
        if (!($value instanceof Expression)) {
            $value = new SqlExpression($value);
        }

        $this->field = $field;
        $this->value = $value;
    }

    /**
     * Get the field of this value
     * @return FieldExpression
     */
    public function getField() {
        return $this->field;
    }

    /**
     * Get the value
     * @return Expression
     */
    public function getValue() {
        return $this->value;
    }

}