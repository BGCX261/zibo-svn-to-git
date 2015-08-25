<?php

namespace zibo\library\orm\model\data\validator;

use zibo\library\validation\validator\Validator;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Abstract validator for model data of a model
 */
abstract class AbstractDataValidator implements DataValidator {

    /**
     * The validators needed for the model
     * @var array
     */
    protected $validators;

    /**
     * Cosntruct this data validator
     * @return null
     */
    public function __construct() {
        $this->validators = array();
    }

    /**
     * Adds a custom validator for a specified field
     * @param string $fieldName Name of the field
     * @param zibo\library\validation\validator\Validator $validator The validator for the field
     * @return null
     */
    public function addValidator($fieldName, Validator $validator) {
        if (String::isEmpty($fieldName)) {
            throw new ZiboException('Cannot add the validator: provided fieldname is empty');
        }

        $this->validators[$fieldName][] = $validator;
    }

}