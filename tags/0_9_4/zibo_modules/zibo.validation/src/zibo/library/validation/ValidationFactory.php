<?php

namespace zibo\library\validation;

use zibo\core\Zibo;

use zibo\library\String;

use zibo\ZiboException;

use \ReflectionClass;
use \ReflectionException;

/**
 * Factory for filters and validators
 */
class ValidationFactory {

    /**
     * Configuration section of the filter definitions
     * @var string
     */
    const CONFIG_FILTER = 'filter';

    /**
     * Configuration section of the validator definitions
     * @var string
     */
    const CONFIG_VALIDATOR = 'validator';

    /**
     * Class name of the filter interface
     * @var string
     */
    const INTERFACE_FILTER = 'zibo\\library\\validation\\filter\\Filter';

    /**
     * Class name of the validator interface
     * @var string
     */
    const INTERFACE_VALIDATOR = 'zibo\\library\\validation\\validator\\Validator';

    /**
     * Instance of the factory
     * @var ValidationFactory
     */
    private static $instance;

    /**
     * Array with the filter name as key and the class name as value
     * @var array
     */
    private $filters;

    /**
     * Array with the validator name as key and the class name as value
     * @var array
     */
    private $validators;

    /**
     * Construct a new validator factory
     * @return null
     */
    private function __construct() {
        $this->filters = array();
        $this->validators = array();
    }

    /**
     * Get the instance of the validation factory
     * @return ValidationFactory
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create a new filter
     * @param string $filterName name of the filter
     * @param array $options options for the filter
     * @return zibo\library\validation\filter\Filter
     * @throws zibo\ZiboException when the filter does not exist
     */
    public function createFilter($filterName, array $options = array()) {
        if (!array_key_exists($filterName, $this->filters)) {
            $filterClass = Zibo::getInstance()->getConfigValue(self::CONFIG_FILTER . '.' . $filterName);
            if (!$filterClass) {
                throw new ZiboException('Unsupported filter: ' . $filterName);
            }

            $this->registerFilter($filterName, $filterClass);
        }

        $className = $this->filters[$filterName];

        return new $className($options);
    }

    /**
     * Create a new validator
     * @param string $validatorName name of the validator
     * @param array $options options for the validator
     * @return zibo\library\validation\validator\Validator
     * @throws zibo\ZiboException when the validator does not exist
     */
    public function createValidator($validatorName, array $options = array()) {
        if (!array_key_exists($validatorName, $this->validators)) {
            $validatorClass = Zibo::getInstance()->getConfigValue(self::CONFIG_VALIDATOR . '.' . $validatorName);
            if (!$validatorClass) {
                throw new ZiboException('Unsupported validator: ' . $validatorName);
            }

            $this->registerValidator($validatorName, $validatorClass);
        }

        $className = $this->validators[$validatorName];

        return new $className($options);
    }

    /**
     * Register a new filter class
     * @param string $filterName name of the filter
     * @param string $className name of the filter class
     * @return null
     * @throws zibo\ZiboException when the filter name or class name is empty or not a string
     * @throws zibo\ZiboException when the filter class does not exist or when it is not a valid class
     */
    public function registerFilter($filterName, $className) {
        if (String::isEmpty($filterName)) {
            throw new ZiboException('Provided filter name is empty');
        }

        $this->checkClass($className, self::INTERFACE_FILTER);

        $this->filters[$filterName] = $className;
    }

    /**
     * Register a new validator class
     * @param string $validatorName name of the validator
     * @param string $className name of the validator class
     * @return null
     * @throws zibo\ZiboException when the validator name or class name is empty or not a string
     * @throws zibo\ZiboException when the validator class does not exist or when it is not a valid class
     */
    public function registerValidator($validatorName, $className) {
        if (String::isEmpty($validatorName)) {
            throw new ZiboException('Provided validator name is empty');
        }

        $this->checkClass($className, self::INTERFACE_VALIDATOR);

        $this->validators[$validatorName] = $className;
    }

    /**
     * Checks if the provided class exists and implements the provided interface
     * @param string $className name of the class to check
     * @param string $interfaceName name of the interface which should be implemented by the class
     * @return null
     * @throws zibo\ZiboException when the name of the class is empty or not a string
     * @throws zibo\ZiboException when the class does not exist or does not implement the provided interface
     */
    private function checkClass($className, $interfaceName) {
        if (String::isEmpty($className)) {
            throw new ZiboException('Provided class name is empty');
        }

        try {
            $reflection = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            throw new ZiboException('Provided class does not exist');
        }

        if (!$reflection->implementsInterface($interfaceName)) {
            throw new ZiboException('Provided class does not implement the interface ' . $interfaceName);
        }
    }

}