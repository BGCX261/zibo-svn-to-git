<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Plain SQL expression
 */
class SqlExpression extends Expression {

    /**
     * The SQL of this expression
     * @var string
     */
    private $sql;

    /**
     * Construct a new SQL expression
     * @param string $sql
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the sql is empty or not a string
     */
    public function __construct($sql) {
    	$this->setSql($sql);
    }

    /**
     * Set the SQL for this expression
     * @param string $sql
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the sql is empty or not a string
     */
    private function setSql($sql) {
        if ($sql === null) {
            $this->sql = 'NULL';
            return;
        }

        if (is_bool($sql)) {
            $this->sql = $sql ? '1' : '0';
            return;
        }

    	try {
    		if (String::isEmpty($sql)) {
    			throw new DatabaseException('Provided sql is empty');
    		}
    	} catch (ZiboException $e) {
    		throw new DatabaseException('Provided sql is invalid', 0, $e);
    	}

        $this->sql = $sql;
    }

    /**
     * Get the SQL of this expression
     * @return string the SQL
     */
    public function getSql() {
        return $this->sql;
    }

}