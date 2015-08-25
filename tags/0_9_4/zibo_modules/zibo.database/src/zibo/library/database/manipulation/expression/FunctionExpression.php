<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Function expression for a statement
 */
class FunctionExpression extends AliasExpression {

    /**
     * Name of the count function
     * @var string
     */
    const FUNCTION_COUNT = 'COUNT';

    /**
     * Name of the min function
     * @var string
     */
    const FUNCTION_MIN = 'MIN';

    /**
     * Name of the max function
     * @var string
     */
    const FUNCTION_MAX = 'MAX';

    /**
     * Name of the function
     * @var string
     */
    private $name;

    /**
     * Array with Expression objects as arguments for the function
     * @var array
     */
    private $arguments;

    /**
     * Flag for the distinct argument
     * @var boolean
     */
    private $distinct;

    /**
     * Construct a new function expression
     * @param string $name name of the function
     * @param string $alias alias for the result field
     */
    public function __construct($name, $alias = null) {
        $this->setName($name);
        $this->setAlias($alias);
        $this->arguments = array();
        $this->distinct = false;
    }

    /**
     * Set the name of this function
     * @param string $name
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the name is empty or not a string
     */
    private function setName($name = null) {
        try {
            if (String::isEmpty($name)) {
                throw new DatabaseException('Provided name is empty');
            }
        } catch (ZiboException $e) {
            throw new DatabaseException('Provided name is not a string');
        }

        $this->name = $name;
    }

    /**
     * Get the name of the function
     * @return string name of the function
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the function distinctive
     * @param boolean flag true if distinctive, false otherwise
     * @return null
     */
    public function setDistinct($flag) {
        $this->distinct = $flag;
    }

    /**
     * Check whether the function is distinctive
     * @return boolean true if distinctive, false otherwise
     */
    public function isDistinct() {
        return $this->distinct;
    }

    /**
     * Add an argument to the function
     * @param Expression $argument
     * @return null
     */
    public function addArgument(Expression $argument) {
        $this->arguments[] = $argument;
    }

    /**
     * Get the arguments for the function
     * @return array array with Expression objects
     */
    public function getArguments() {
        return $this->arguments;
    }

}