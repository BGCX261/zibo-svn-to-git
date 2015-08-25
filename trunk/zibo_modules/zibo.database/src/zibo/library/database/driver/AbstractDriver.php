<?php

namespace zibo\library\database\driver;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\statement\Statement;
use zibo\library\database\manipulation\GenericStatementParser;
use zibo\library\database\Dsn;
use zibo\library\filesystem\File;
use zibo\library\String;

/**
 * Abstract driver for a database connection
 */
abstract class AbstractDriver implements Driver {

    /**
     * The DSN of the connection
     * @var zibo\library\database\Dsn
     */
    protected $dsn;

    /**
     * Parser for statement objects
     * @var zibo\library\database\manipulation\StatementParser
     */
    protected $statementParser;

    /**
     * Constructs a new connection with a DSN
     * @param zibo\library\database\Dsn $dsn DSN with the connection parameters
     * @return null
     */
    public function __construct(Dsn $dsn) {
        $this->dsn = $dsn;
    }

    /**
     * Gets the DSN of this connection
     * @return zibo\library\database\Dsn DSN of this connection
     */
    public function getDsn() {
        return $this->dsn;
    }

    /**
     * Executes a statement on this connection
     * @param Statement $statement Definition of the statement
     * @return zibo\library\database\DatabaseResult Instance of a database result
     */
    public function executeStatement(Statement $statement) {
        $parser = $this->getStatementParser();

        $sql = $parser->parseStatement($statement);

        return $this->execute($sql);
    }

    /**
     * Quotes a database identifier
     * @param string $identifier Identifier to quote
     * @return string Quoted identifier
     * @throws zibo\library\database\exception\DatabaseException when the provided identifier is empty or not a scalar value
     */
    public function quoteIdentifier($identifier) {
        if (!String::isString($identifier, String::NOT_EMPTY)) {
            throw new DatabaseException('Provided identifier is empty');
        }

        return $identifier;
    }

    /**
     * Quotes a database value
     * @param string $value Value to quote
     * @return string Quoted value
     * @throws zibo\library\database\exception\DatabaseException when the provided value is not a scalar value
     */
    public function quoteValue($value) {
        if ($value !== null && !is_scalar($value)) {
            throw new DatabaseException('Provided value should be scalar');
        }

        return $value;
    }

    /**
     * Gets the database definer
     * @return zibo\library\database\definition\Definer Database definer
     */
    public function getDefiner() {
        throw new DatabaseException('Definer is not supported by this driver');
    }

    /**
     * Gets the statement parser, a parser which parses statement objects into SQL.
     * @return zibo\library\database\manipulation\StatementParser
     */
    public function getStatementParser() {
        if ($this->statementParser == null) {
            $this->statementParser = new GenericStatementParser($this);
        }

        return $this->statementParser;
    }

    /**
     * Starts a new transaction
     * @return boolean True is a new transaction is started, false if a transaction is already in progress
     */
    public function startTransaction() {
        throw new DatabaseException('Transactions are not supported by this driver');
    }

    /**
     * Commits the transaction in progress
     * @return null
     */
    public function commitTransaction() {
        throw new DatabaseException('Transactions are not supported by this driver');
    }

    /**
     * Performs a rollback on the transaction in progress
     * @return null
     */
    public function rollbackTransaction() {
        throw new DatabaseException('Transactions are not supported by this driver');
    }

    /**
     * Checks whether a transaction is in progress
     * @return boolean true if a transaction is in progress, false otherwise
     */
    public function isTransactionStarted() {
        throw new DatabaseException('Transactions are not supported by this driver');
    }

    /**
     * Imports a SQL file on this connection
     * @param zibo\library\filesystem\File $file SQL file
     * @return null
     * @throws zibo\ZiboException when not available or when an error occured while importing
     */
    public function import(File $file) {
        throw new DatabaseException('Import is not supported by this driver');
    }

    /**
     * Exports the database to a file
     * @return null
     * @throws zibo\ZiboException when not available or when an error occured while exporting
     */
    public function export(File $file) {
        throw new DatabaseException('Export is not supported by this driver');
    }

}