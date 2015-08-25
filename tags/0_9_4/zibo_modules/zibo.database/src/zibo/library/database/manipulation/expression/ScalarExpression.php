<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;

/**
 * Expression for a scalar value
 */
class ScalarExpression extends AliasExpression {

    /**
     * The value
     * @var mixed
     */
    private $value;

    /**
     * Construct a new scalar expression
     * @param mixed $value Scalar value
     * @param string $alias Alias for the value
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the value if not a scalar value
     */
    public function __construct($value = null, $alias = null) {
        $this->setValue($value);
        $this->setAlias($alias);
    }

    /**
     * Set the value
     * @param mixed $value
     * @return null
     */
    private function setValue($value) {
        if ($value !== null && !is_scalar($value)) {
            if (is_object($value)) {
                $value = get_class($value);
            } elseif (is_array($value)) {
                $value = 'Array';
            } else {
                $value = 'unknown';
            }

            throw new DatabaseException('Provided value is not a scalar value: ' . $value);
        }

        $this->value = $value;
    }

    /**
     * Get the value
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

}