<?php

namespace zibo\library\orm\query;

use zibo\library\orm\exception\OrmException;
use zibo\library\String;

/**
 * Definition of a expression for a ModelQuery
 */
class ModelExpression {

    /**
     * Expression string
     * @var string
     */
    private $expression;

    /**
     * Array with variables to parse in the expression
     * @var array
     */
    private $variables;

    /**
     * Constructs a new model expression
     * @param string $expression Expression string
     * @param array $variables Array with variables to parse in the expression
     * @return null
     */
    public function __construct($expression, array $variables = null) {
        $this->setExpression($expression);
        $this->setVariables($variables);
    }

    /**
     * Sets the expression string
     * @param string $expression
     * @return null
     * @throws zibo\ZiboException when the expression is empty or invalid
     */
    private function setExpression($expression) {
        if (String::isEmpty($expression)) {
            throw new OrmException('Provided expression is empty');
        }

        $this->expression = $expression;
    }

    /**
     * Gets the expression string
     * @return string
     */
    public function getExpression() {
        return $this->expression;
    }

    /**
     * Sets the variables for this expression
     * @param null|array $variables Array with the variable name as key and the variable as value
     * @return null
     */
    private function setVariables(array $variables = null) {
        if ($variables === null) {
            $variables = array();
        }

        $this->variables = $variables;
    }

    /**
     * Gets the variables for this expression
     * @return array Array with the variable name as key and the variable as value
     */
    public function getVariables() {
        return $this->variables;
    }

}