<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Base expression class
 */
abstract class AliasExpression extends Expression {

    /**
     * Alias for this expression when used as a select expression
     * @var string
     */
    protected $alias;

    /**
     * Set the alias of this expression
     * @param string $alias
     * @return null
     * @throws zibo\library\database\exception\DatabaseException
     */
    public function setAlias($alias = null) {
        if ($alias === null) {
            $this->alias = null;
            return;
        }

        try {
            if (String::isEmpty($alias)) {
                throw new DatabaseException('Provided alias is empty');
            }
        } catch (ZiboException $e) {
            throw new DatabaseException('Provided alias is not a string');
        }

        $this->alias = $alias;
    }

    /**
     * Get the alias of this expression
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

}