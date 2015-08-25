<?php

namespace zibo\library\html\form\field;

use zibo\library\html\Element;
use zibo\library\validation\filter\Filter;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\Validator;

/**
 * Interface for a field of form
 */
interface Field extends Element {

    /**
     * Name of the disabled attribute
     * @var string
     */
    const ATTRIBUTE_DISABLED = 'disabled';

    /**
     * Name of the name attribute
     * @var string
     */
    const ATTRIBUTE_NAME = 'name';

    /**
     * Name of the value attribute
     * @var string
     */
    const ATTRIBUTE_VALUE = 'value';

    /**
     * Name of the style class for a field with validation errors
     * @var string
     */
    const CLASS_VALIDATION_ERROR = 'error';

    /**
     * Construct a new field
     * @param string $name name of the field
     * @param mixed $defaultValue value for the initialization of the field
     * @param boolean $isDisabled flag to enable or disable the field
     * @return null
     */
    public function __construct($name, $defaultValue = null, $isDisabled = false);

    /**
     * Set the name of the field
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the name is empty or not a string
     */
    public function setName($name);

    /**
     * Gets the name of the field
     * @return string
     */
    public function getName();

    /**
     * Sets the value of the field
     * @param mixed $value
     * @return null
     */
    public function setValue($value);

    /**
     * Get the set value of the field
     * @return mixed
     */
    public function getValue();

    /**
     * Sets the default value for the field. This is the initialization value
     * @param mixed $defaultValue
     * @return null
     */
    public function setDefaultValue($defaultValue);

    /**
     * Gets the default value of the field.
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Sets whether this field is disabled
     * @param boolean $isDisabled
     * @return null
     */
    public function setIsDisabled($isDisabled);

    /**
     * Gets whether this field is disabled
     * @return boolean true if disabled, false otherwise
     */
    public function isDisabled();

    /**
     * Adds a filter to this field. Filter are used to process the value of the field after processing the request.
     * @param zibo\library\html\form\field\filter\Filter $filter
     * @return null
     */
    public function addFilter(Filter $filter);

    /**
     * Adds a validator to this field
     * @param zibo\library\validation\validator\Validator $validator
     * @return null
     */
    public function addValidator(Validator $validator);

    /**
     * Perform the validation on this field through the added validators
     * @param zibo\library\validation\exception\ValidationException $exception
     * @return zibo\library\validation\exception\ValidationException
     */
    public function validate(ValidationException $exception = null);

    /**
     * Process the request and update the value of this field if found in the request
     * @return null
     */
    public function processRequest();

}