<?php

namespace zibo\library\html\table\decorator;

/**
 * Decorator for a price value
 */
class PriceDecorator extends ValueDecorator {

    /**
     * Default currency symbol
     * @var string
     */
    const DEFAULT_CURRENCY =  '€';

    /**
     * Currency symbol
     * @var string
     */
    private $currency;

    /**
     * Constructs a new decorator
     * @param string $fieldName field name for objects or arrays passed to this decorator (optional)
     * @return null
     * @throws zibo\ZiboException when an invalid field name is provided
     */
    public function __construct($fieldName = null, $currency = null) {
        parent::__construct($fieldName);

        if (!$currency) {
            $currency = self::DEFAULT_CURRENCY;
        }

        $this->currency = $currency;
    }

    /**
     * Performs the actual decorating on the provided value.
     * @param mixed $value The value to decorate
     * @return string
     */
    public function decorateValue($value) {
        return $this->currency . ' ' . number_format($value, 2);
    }

}