<?php

namespace zibo\library\database\driver;

use zibo\library\database\manipulation\statement\Statement;
use zibo\library\database\Dsn;
use zibo\library\filesystem\File;

/**
 * Interface for a driver of a database connection
 */
interface Driver {

    /**
     * Null value
     * @var string
     */
    const VALUE_NULL = 'NULL';

    /**
     * Constructs a new connection with a DSN
     * @param zibo\library\database\Dsn $dsn DSN with the connection parameters
     * @return null
     */
    public function __construct(Dsn $dsn);

    /**
     * Gets the DSN of this connection
     * @return zibo\library\database\Dsn DSN of this connection
     */
    public function getDsn();

    /**
     * Checks whether this driver is connected
     * @return boolean True if the driver is connected, false otherwise
     */
    public function isConnected();

    /**
     * Connects this driver
     * @return null
     */
    public function connect();

    /**
     * Disconnects this driver
     * @return null
     */
    public function disconnect();

    /**
     * Executes an SQL script on this connection
     * @param string $sql SQL script to execute
     * @return zibo\library\database\DatabaseResult Instance of a database result
     */
    public function execute($sql);

    /**
     * Executes a statement on this connection
     * @param zibo\library\database\manipulation\statement\Statement $statement Definition of the statement
     * @return zibo\library\database\DatabaseResult Instance of a database result
     */
    public function executeStatement(Statement $statement);

    /**
     * Gets the primary key of the last inserted record
     * @return string Primary key of the last inserted record or null if no record has been inserted yet
     */
    public function getLastInsertId();

    /**
     * Quotes a database identifier
     * @param string $identifier Identifier to quote
     * @return string Quoted identifier
     * @throws zibo\library\database\exception\DatabaseException when the provided identifier is empty or not a scalar value
     */
    public function quoteIdentifier($identifier);

    /**
     * Quotes a database value
     * @param string $value Value to quote
     * @return string Quoted value
     * @throws zibo\library\database\exception\DatabaseException when the provided value is not a scalar value
     */
    public function quoteValue($value);

    /**
     * Gets the database definer
     * @return zibo\library\database\definition\definer\Definer
     */
    public function getDefiner();

    /**
     * Gets the statement parser, a parser which parses statement objects into SQL.
     * @return zibo\library\database\manipulation\StatementParser
     */
    public function getStatementParser();

    /**
     * Starts a new transaction
     * @return boolean True is a new transaction is started, false if a transaction is already in progress
     */
    public function startTransaction();

    /**
     * Commits the transaction in progress
     * @return null
     */
    public function commitTransaction();

    /**
     * Performs a rollback of the transaction in progress
     * @return null
     */
    public function rollbackTransaction();

    /**
     * Checks whether a transaction is in progress
     * @return boolean True if a transaction is in progress, false otherwise
     */
    public function isTransactionStarted();

    /**
     * Imports a SQL file on this connection
     * @param zibo\library\filesystem\File $file SQL file
     * @return null
     */
    public function import(File $file);

    /**
     * Exports the database to a file
     * @param zibo\library\filesystem\File $file File to write the export to
     * @return null
     * @throws zibo\ZiboException when not available or when an error occured while exporting
     */
    public function export(File $file);

}