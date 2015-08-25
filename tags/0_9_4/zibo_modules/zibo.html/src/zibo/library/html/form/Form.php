<?php

namespace zibo\library\html\form;

use zibo\library\html\form\field\Field;
use zibo\library\html\form\field\FileField;
use zibo\library\html\form\field\HiddenField;
use zibo\library\html\AbstractElement;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\Validator;
use zibo\library\Structure;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Form HTML element
 */
class Form extends AbstractElement {

    /**
     * Name of the action attribute
     * @var string
     */
    const ATTRIBUTE_ACTION = 'action';

    /**
     * Name of the enctype attribute
     * @var string
     */
    const ATTRIBUTE_ENCTYPE = 'enctype';

    /**
     * Name of the method attribute
     * @var string
     */
    const ATTRIBUTE_METHOD = 'method';

    /**
     * Name of the name attribute
     * @var unknown_type
     */
    const ATTRIBUTE_NAME = 'name';

    /**
     * Encoding type for a multipart form (needed for file uploads)
     * @var string
     */
    const ENCTYPE_MULTIPART_FORMDATA = 'multipart/form-data';

    /**
     * Post method
     * @var string
     */
    const METHOD_POST = 'post';

    /**
     * Get method
     * @var string
     */
    const METHOD_GET = 'get';

    /**
     * Style class of the form
     * @var string
     */
    const STYLE_FORM = 'data';

    /**
     * Name of the hidden submit value
     * @var string
     */
    const SUBMIT_NAME = '__submit';

    /**
     * Value for the hidden submit value
     * @var string
     */
    const SUBMIT_VALUE = '1';

    /**
     * Action of the form
     * @var string
     */
    protected $action;

    /**
     * Name of the form
     * @var string
     */
    protected $name;

    /**
     * Method of the form
     * @var string
     */
    private $method;

    /**
     * Array for the fields of this form
     * @var array
     */
    protected $fields;

    /**
     * Array for the form validators
     * @var array
     */
    protected $validators;

    /**
     *
     * @var zibo\library\validation\exception\ValidationException
     */
    protected $validationException;

    /**
     * Flag to set if this form is submitted
     * @var boolean
     */
    protected $isSubmitted;

    /**
     * Flag to set if this form is processed
     * @var boolean
     */
    private $isProcessed;

    /**
     * Construct a new form
     * @param string $action URL where the form will point to
     * @param string $name Name of the form
     * @return null
     */
    public function __construct($action, $name) {
        $this->setAction($action);
        $this->setName($name);
        $this->setId($name);
        $this->setMethod(self::METHOD_POST);

        $this->isSubmitted = null;
        $this->isProcessed = false;

        $this->fields = new Structure();
        $this->validators = array();

        $this->appendToClass(self::STYLE_FORM);
        $this->addField(new HiddenField(self::SUBMIT_NAME . $name, self::SUBMIT_VALUE));
    }

    /**
     * Sets a attribute to this form element
     * @param string $attribute name of the attribute
     * @param mixed $value value for the attribute
     * @return null
     */
    public function setAttribute($attribute, $value) {
        if ($attribute == self::ATTRIBUTE_ACTION) {
            $this->setAction($value);
            return;
        }
        if ($attribute == self::ATTRIBUTE_METHOD) {
            $this->setMethod($value);
            return;
        }
        if ($attribute == self::ATTRIBUTE_NAME) {
            $this->setName($value);
            return;
        }
        parent::setAttribute($attribute, $value);
    }

    /**
     * Gets a attribute of this form element
     * @param string $attribute name of the attribute
     * @param mixed $default value to return when the attribute is not set
     * @return mixed
     */
    public function getAttribute($attribute, $default = null) {
        if ($attribute == self::ATTRIBUTE_ACTION) {
            return $this->getAction();
        }
        if ($attribute == self::ATTRIBUTE_METHOD) {
            return $this->getMethod();
        }
        if ($attribute == self::ATTRIBUTE_NAME) {
            return $this->getName();
        }
        return parent::getAttribute($attribute, $default);
    }

    /**
     * Sets the disabled flag of the full form or of a field
     * @param boolean $isDisabled true to disable, false to enable
     * @param string $fieldName Name of the field to enable/disable (optional)
     * @return null
     */
    public function setIsDisabled($isDisabled, $fieldName = null) {
        if ($fieldName != null) {
            $field = $this->getField($fieldName);
            $field->setIsDisabled($isDisabled);
            return;
        }

        $iterator = $this->fields->getIterator();
        foreach ($iterator as $field) {
            $field->setIsDisabled($isDisabled);
        }
    }

    /**
     * Sets the action attribute of this form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * Gets the action attribute of this form
     * @return string URL where this form will point to
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Gets the HTML of the action attribute
     * @return string
     */
    protected function getActionHtml() {
        return parent::getAttributeHtml(self::ATTRIBUTE_ACTION, $this->action);
    }

    /**
     * Sets the name attribute of this form
     * @param string $name
     * @return null
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the name attribute of this form
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the HTML of the name attribute
     * @return string
     */
    protected function getNameHtml() {
        if (!empty($this->name)) {
            return parent::getAttributeHtml(self::ATTRIBUTE_NAME, $this->name);
        }
        return '';
    }

    /**
     * Sets the method attribute of this form
     * @param string $method
     * @return null
     * @throws zibo\ZiboException when the method is not POST or GET
     */
    public function setMethod($method) {
        if ($method != self::METHOD_GET && $method != self::METHOD_POST) {
            throw new ZiboException('Invalid method, try get or post');
        }
        $this->method = $method;
    }

    /**
     * Gets the method attribute of this form
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Gets the HTML of the method attribute
     * @return string
     */
    protected function getMethodHtml() {
        return parent::getAttributeHtml(self::ATTRIBUTE_METHOD, $this->method);
    }

    /**
     * Adds a field to this form
     * @param zibo\library\html\form\field\Field $field
     * @return null
     */
    public function addField(Field $field) {
        $name = $field->getName();

        if ($field instanceof FileField) {
            $this->setAttribute(self::ATTRIBUTE_ENCTYPE, self::ENCTYPE_MULTIPART_FORMDATA);
        }

        if ($field->getId() == null) {
            $field->setId($this->getId() . ucfirst($name));
        }

        $this->fields->set($name, $field);
    }

    /**
     * Checks whether this form has a certain field
     * @param string $name Name of the field
     * @return boolean true if this form has the field, false otherwise
     */
    public function hasField($name) {
        return $this->fields->has($name);
    }

    /**
     * Gets a field
     * @param string $name Name of the field
     * @return zibo\library\html\form\field\Field
     * @throws zibo\ZiboException when the field is not found
     */
    public function getField($name = null) {
        if ($name == null) {
            return $this->fields->get(self::SUBMIT_NAME . $this->name);
        }

        if (!$this->fields->has($name)) {
            throw new ZiboException('Field ' . $name . ' not found in this form');
        }

        return $this->fields->get($name);
    }

    /**
     * Gets all the fields
     * @return zibo\library\Structure
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Adds a validator to a field
     * @param string $name Name of the field
     * @param zibo\library\validation\validator\Validator $validator
     * @return null
     * @throws zibo\ZiboException when the field is not found
     */
    public function addValidator($name, Validator $validator) {
        $this->getField($name)->addValidator($validator);
    }

    /**
     * Adds a validator for the form
     * @param FormValidator $validator
     * @return null
     */
    public function addFormValidator(FormValidator $validator) {
        $this->validators[] = $validator;
    }

    /**
     * Gets the value of a field
     * @param string $name Name of the field
     * @return mixed Value of the field
     * @throws zibo\ZiboException when the field is not found
     */
    public function getValue($name) {
        return $this->getField($name)->getValue();
    }

    /**
     * Sets a value to a field
     * @param string $name Name of the field
     * @param mixed $value Value for the field
     * @return null
     * @throws zibo\ZiboException when the field is not found
     */
    public function setValue($name, $value) {
        $this->getField($name)->setValue($value);
    }

    /**
     * Gets the HTML of the attributes for the form tag
     * @return string
     */
    public function getHtml() {
        return $this->getNameHtml() .
            $this->getIdHtml() .
            $this->getActionHtml() .
            $this->getMethodHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml();
    }

    /**
     * Validates this form
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when one of the fields or
     * the form itself is not validated
     */
    public function validate() {
        if (!$this->isSubmitted()) {
            throw new ZiboException('Form is not submitted');
        }

        $this->validationException = new ValidationException();

        $iterator = $this->fields->getIterator();
        foreach ($iterator as $field) {
            $field->validate($this->validationException);
        }

        foreach ($this->validators as $validator) {
            $validator->isValid($this);
        }

        if ($this->validationException->hasErrors()) {
            throw $this->validationException;
        }
    }

    /**
     * Sets a validation exception to this form
     * @param zibo\library\validation\exception\ValidationException $exception
     * @return null
     */
    public function setValidationException(ValidationException $exception) {
        $this->validationException = $exception;

        foreach ($this->fields as $fieldName => $field) {
            if ($exception->hasErrors($fieldName)) {
                $field->appendToClass(Field::CLASS_VALIDATION_ERROR);
            }
        }
    }

    /**
     * Gets the validation exception which is set to this form
     * @return null|zibo\library\validation\exception\ValidationException
     */
    public function getValidationException() {
        return $this->validationException;
    }

    /**
     * Checks whether this form has been submitted. First time this method is called, it
     * will process this form.
     * @return boolean true if the form has been submitted, false otherwise
     */
    public function isSubmitted() {
        if ($this->isSubmitted !== null) {
            return $this->isSubmitted;
        }

        $this->isSubmitted = false;

        $submitField = $this->getField(self::SUBMIT_NAME . $this->name);
        $submitField->processRequest();
        if ($submitField->getValue() == self::SUBMIT_VALUE) {
            $this->processRequest();
            $this->isSubmitted = true;
        }

        return $this->isSubmitted;
    }

    /**
     * Processes the HTTP request into this form
     * @return boolean True if the request has been processed, false if the request was already processed
     */
    public function processRequest() {
        if ($this->isProcessed) {
            return false;
        }

        $iterator = $this->fields->getIterator();
        foreach ($iterator as $field) {
            if ($field->isDisabled()) {
                $field->setValue($field->getDefaultValue());
                continue;
            }

            $field->processRequest();
        }

        return $this->isProcessed = true;
    }

}