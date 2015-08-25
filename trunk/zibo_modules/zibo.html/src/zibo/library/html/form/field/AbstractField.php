<?php

namespace zibo\library\html\form\field;

use zibo\library\html\AbstractElement;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\filter\Filter;
use zibo\library\validation\validator\NestedValidator;
use zibo\library\validation\validator\Validator;
use zibo\library\String;
use zibo\library\Structure;

use zibo\ZiboException;

/**
 * Abstract implementation of a form field
 */
abstract class AbstractField extends AbstractElement implements Field {

    /**
     * Name of the field
     * @var string
     */
    protected $name;

    /**
     * Value of the field
     * @var mixed
     */
    protected $value;

    /**
     * Initialization value of the field
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Flag to see if this field is enabled
     * @var boolean
     */
    protected $isDisabled;

    /**
     * Array with filters for the value of this field
     * @var array
     */
    protected $filters;

    /**
     * Nested validator with validators for the value of this field
     * @var zibo\library\validation\validator\NestedValidator
     */
    protected $validator;

    /**
     * Construct a new field
     * @param string $name name of the field
     * @param mixed $defaultValue initialization value
     * @param boolean $isDisabled flag to see if this field is enabled
     * @return null
     */
    public function __construct($name, $defaultValue = null, $isDisabled = false) {
        $this->setName($name);
        $this->setDefaultValue($defaultValue);
        $this->setIsDisabled($isDisabled);
        $this->filters = array();
        $this->init();
    }

    /**
     * Hook to perform extra initialization for this field when constructing a new instance
     * @return null
     */
    protected function init() {

    }

    /**
     * Sets an attribute for this element
     * @param string $attribute name of the attribute
     * @param string $value value of the attribute
     * @return null
     * @throws zibo\ZiboException when the name of attribute is empty or not a string
     */
    public function setAttribute($attribute, $value) {
        if ($attribute == Field::ATTRIBUTE_NAME) {
            return $this->setName($value);
        }
        if ($attribute == Field::ATTRIBUTE_VALUE) {
            return $this->setValue($value);
        }

        return parent::setAttribute($attribute, $value);
    }

    /**
     * Gets a attribute of this element
     * @param string $attribute name of the attribute
     * @param mixed $default value to return when the attribute is not set
     * @return string the value of the attribute
     */
    public function getAttribute($attribute, $default = null) {
        if ($attribute == Field::ATTRIBUTE_NAME) {
            return $this->getName();
        }
        if ($attribute == Field::ATTRIBUTE_VALUE) {
            return $this->getValue();
        }

        return parent::getAttribute($attribute, $default);
    }

    /**
     * Sets the name of this field
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the name is empty or not a string
     */
    public function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }
        $this->name = $name;
    }

    /**
     * Gets the name of this field
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the HTML of the attribute of the name
     * @return string
     */
    protected function getNameHtml() {
        return $this->getAttributeHtml(Field::ATTRIBUTE_NAME, $this->name);
    }

    /**
     * Sets the value of this field
     * @param mixed $value
     * @return null
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Gets the value of this field
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the default value of this field
     * @param mixed $value
     * @return null
     */
    public function setDefaultValue($value) {
        $this->defaultValue = $value;
    }

    /**
     * Gets the default value of this field
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }

    /**
     * Gets the display value of this field.
     * @return mixed the value of this field if set, the default value otherwise
     */
    protected function getDisplayValue() {
        if (!is_null($this->value)) {
            return $this->value;
        }

        return $this->defaultValue;
    }

    /**
     * Gets the HTML of the attribute of the display value
     * @return string
     */
    protected function getDisplayValueHtml() {
        $value = $this->getDisplayValue();
        return $this->getAttributeHtml(Field::ATTRIBUTE_VALUE, $value);
    }

    /**
     * Sets whether this field is enabled
     * @param boolean $isDisabled
     * @return null
     */
    public function setIsDisabled($isDisabled) {
        $this->isDisabled = $isDisabled;
    }

    /**
     * Gets whether this field is enabled
     * @return boolean true if disabled, false otherwise
     */
    public function isDisabled() {
        return $this->isDisabled;
    }

    /**
     * Gets the HTML of the disabled attribute
     * @return string
     */
    protected function getIsDisabledHtml() {
        if ($this->isDisabled()) {
            return $this->getAttributeHtml(Field::ATTRIBUTE_DISABLED, Field::ATTRIBUTE_DISABLED);
        }

        return '';
    }

    /**
     * Adds a filter to this field.
     * @param zibo\library\html\form\field\filter\Filter $filter
     * @return null
     */
    public function addFilter(Filter $filter) {
        $this->filters[] = $filter;
    }

    /**
     * Adds a validator to this field
     * @param zibo\library\validation\validator\Validator $validator
     * @return null
     */
    public function addValidator(Validator $validator) {
        if (!$this->validator) {
            $this->validator = new NestedValidator();
        }

        $this->validator->addValidator($validator);
    }

    /**
     * Perform the validation on this field through the added validators
     * @param zibo\library\validation\exception\ValidationException $exception
     * @return zibo\library\validation\exception\ValidationException
     */
    public function validate(ValidationException $exception = null) {
        if (!$exception) {
            $exception = new ValidationException();
        }

        if (!$this->validator || $this->isDisabled()) {
            return $exception;
        }

        if (!$this->validator->isValid($this->getValue())) {
            $this->appendToClass(Field::CLASS_VALIDATION_ERROR);
            $exception->addErrors($this->getName(), $this->validator->getErrors());
        }

        return $exception;
    }

    /**
     * Process the request and update the value of this field if found in the request
     * @return null
     */
    public function processRequest() {
        $value = $this->getRequestValue();
        if (!is_null($value)) {
            $this->setValue($value);
            $this->applyFilters();
        }
    }

    /**
     * Apply the added filters on the value of this field
     * @return null
     */
    protected function applyFilters() {
        if (empty($this->filters)) {
            return;
        }

        $value = $this->getValue();
        foreach ($this->filters as $filter) {
            $value = $filter->filter($value);
        }

        $this->setValue($value);
    }

    /**
     * Get the value of this field from the request
     * @param string $name name of the request variable
     * @return mixed value of this field from the request
     */
    protected function getRequestValue($name = null) {
        if ($name == null) {
            $name = $this->name;
        }

        $request = new Structure($_REQUEST);
        return $request->get($name);
    }

}