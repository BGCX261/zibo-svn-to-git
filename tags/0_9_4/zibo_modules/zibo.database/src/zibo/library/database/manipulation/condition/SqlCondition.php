<?php

namespace zibo\library\database\manipulation\condition;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Plain SQL condition
 */
class SqlCondition extends Condition {

    /**
     * The SQL of this condition
     * @var string
     */
    private $sql;

    /**
     * Constructs a new SQL condition
     * @param string $sql The SQL
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the provided SQL is empty or not a string
     */
    public function __construct($sql) {
    	$this->setSql($sql);
    }

    /**
     * Sets the SQL for this condition
     * @param string $sql The SQL
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the provided SQL is empty or not a string
     */
    private function setSql($sql) {
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
     * Gets the SQL of this condition
     * @return string The SQL
     */
    public function getSql() {
        return $this->sql;
    }

}