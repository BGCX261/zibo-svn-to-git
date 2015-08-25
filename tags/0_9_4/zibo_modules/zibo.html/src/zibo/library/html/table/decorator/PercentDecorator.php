<?php

namespace zibo\library\html\table\decorator;

use zibo\library\Number;

/**
 * Decorator for a percent value
 */
class PercentDecorator extends ValueDecorator {

    /**
     * Constructs a new percent decorator
     * @param string $fieldName field name for objects or arrays passed to this decorator (optional)
     * @param integer $precision Number of decimal digits to round to
     * @return null
     * @throws zibo\ZiboException when the precision is not a number or smaller then 0
     */
    public function __construct($fieldName = null, $precision = 0) {
        parent::__construct($fieldName);

        if (Number::isNegative($precision)) {
            throw new ZiboException('Provided precision cannot be smaller then 0');
        }

        $this->precision = $precision;
    }

    /**
     * Decorators the provided value into a formatted percent value
     * @param mixed $value The value to decorate
     * @return string Empty string if no numeric value provided, a percent string otherwise
     */
    protected function decorateValue($value) {
        if (!is_numeric($value)) {
            return;
        }

        $value = round($value, $this->precision);

        return $value . ' %';
    }

}