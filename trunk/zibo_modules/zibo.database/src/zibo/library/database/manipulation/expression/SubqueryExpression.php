<?php

namespace zibo\library\database\manipulation\expression\condition;

use zibo\library\database\manipulation\statement\manipulation\SelectStatement;

/**
 * Subquery expression
 */
class SubqueryExpression extends Expression {

    /**
     * The select statement of this subquery expression
     * @var zibo\library\database\manipulation\statement\manipulation\SelectStatement
     */
    private $statement;

    /**
     * Construct a new subquery expression
     * @param zibo\library\database\manipulation\statement\manipulation\SelectStatement $selectStatement
     * @return null
     */
    public function __construct(SelectStatement $selectStatement) {
    	$this->statement = $selectStatement;
    }

    /**
     * Get the select statement of this subquery
     * @return zibo\library\database\manipulation\statement\manipulation\SelectStatement
     */
    public function getStatement() {
        return $this->statement;
    }

}