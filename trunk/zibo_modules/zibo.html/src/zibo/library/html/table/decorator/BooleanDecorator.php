<?php

namespace zibo\library\html\table\decorator;

use zibo\library\i18n\I18n;

/**
 * Decorator for a boolean value
 */
class BooleanDecorator extends ValueDecorator {

    /**
     * Default label for a null value
     * @var string
     */
    const DEFAULT_NULL_VALUE = '---';

    /**
     * Translation key for the default true value
     * @var string
     */
    const TRANSLATION_TRUE = 'label.yes';

    /**
     * Translation key for the default false value
     * @var string
     */
    const TRANSLATION_FALSE = 'label.no';

    /**
     * Value to use for true values
     * @var string
     */
    private $trueValue;

    /**
     * Value to use for false values
     * @var string
     */
    private $falseValue;

    /**
     * Value to use for null values
     * @var string
     */
    private $nullValue;

    /**
     * Constructs a new boolean decorator
     * @param string $fieldName field name for objects or arrays passed to this decorator (optional)
     * @param string $trueValue Value to decorate true values into
     * @param string $falseValue Value to decorate false values into
     * @param string $nullValue Value to decorate null values into
     * @return null
     */
    public function __construct($fieldName = null, $trueValue = null, $falseValue = null, $nullValue = null) {
        parent::__construct($fieldName);

        $translator = I18n::getInstance()->getTranslator();

        if ($trueValue === null) {
            $trueValue = $translator->translate(self::TRANSLATION_TRUE);
        }
        if ($falseValue === null) {
            $falseValue = $translator->translate(self::TRANSLATION_FALSE);
        }
        if ($nullValue === null) {
            $nullValue = self::DEFAULT_NULL_VALUE;
        }

        $this->trueValue = $trueValue;
        $this->falseValue = $falseValue;
        $this->nullValue = $nullValue;
    }

    /**
     * Performs the actual decorating on the provided value.
     * @param mixed $value The value to decorate
     * @return mixed The decorated value
     */
    protected function decorateValue($value) {
        if ($value === null) {
            $value = $this->nullValue;
        } elseif ($value) {
            $value = $this->trueValue;
        } else {
            $value = $this->falseValue;
        }

        return $value;
    }

}