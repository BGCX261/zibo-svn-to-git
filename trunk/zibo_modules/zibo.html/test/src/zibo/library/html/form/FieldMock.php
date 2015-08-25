<?php

namespace zibo\library\html\form;

use zibo\library\html\form\field\Field;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\filter\Filter;
use zibo\library\validation\validator\Validator;

class FieldMock implements Field {

    private $name;
    private $isDisabled;
    private $id;

    public function __construct($name, $defaultValue = null, $isDisabled = false) {
        $this->setName($name);
        $this->setIsDisabled($isDisabled);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setValue($value) {

    }

    public function getValue() {

    }

    public function setDefaultValue($defaultValue) {

    }

    public function getDefaultValue() {

    }

    public function setIsDisabled($isDisabled) {
        $this->isDisabled = $isDisabled;
    }

    public function isDisabled() {
        return $this->isDisabled;
    }

    public function addFilter(Filter $filter) {

    }

    public function processRequest() {

    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setClass($class) {

    }

    public function appendToClass($class) {

    }

    public function removeFromClass($class) {

    }

    public function getClass() {

    }

    public function setAttribute($attribute, $value) {

    }

    public function getAttributes() {

    }

    public function getAttribute($attribute, $default = null) {

    }

    public function getHtml() {

    }

    public function addValidator(Validator $validator) {

    }

    public function validate(ValidationException $e = null) {
        if ($e === null) {
            $e = new ValidationException();
        }

        return $e;
    }

}