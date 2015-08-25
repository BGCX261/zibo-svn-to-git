<?php

namespace zibo\library\database\driver;

class DriverMock extends AbstractTransactionDriver {

    const QUOTE_IDENTIFIER = "`";

    const QUOTE_VALUE = "'";

	private $isConnected = false;

	private $sqls = array();

    public function isConnected() {
    	return $this->isConnected;
    }

    public function connect() {
    	$this->isConnected = true;
    }

    public function disconnect() {
    	$this->isConnected = false;
    }

	public function execute($sql) {
        $this->sqls[] = $sql;
	}

	public function getSqls() {
	    return $this->sqls;
	}

	public function ping() {

	}

	public function getLastInsertId() {
		return 1;
	}

    /**
     * Quotes a database identifier
     * @param string $identifier Identifier to quote
     * @return string Quoted identifier
     * @throws zibo\library\database\exception\DatabaseException when the provided identifier is empty or not a scalar value
     */
    public function quoteIdentifier($identifier) {
        parent::quoteIdentifier($identifier);

        return self::QUOTE_IDENTIFIER . $identifier . self::QUOTE_IDENTIFIER;
    }

    /**
     * Quotes a database value
     * @param string $value Value to quote
     * @return string Quoted value
     * @throws zibo\library\database\exception\DatabaseException when the provided value is not a scalar value
     */
    public function quoteValue($value) {
        parent::quoteValue($value);

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_numeric($value)) {
            return $value;
        }

        if (is_null($value) || $value === self::VALUE_NULL) {
            return self::VALUE_NULL;
        }

        return self::QUOTE_VALUE . $value . self::QUOTE_VALUE;
    }

}