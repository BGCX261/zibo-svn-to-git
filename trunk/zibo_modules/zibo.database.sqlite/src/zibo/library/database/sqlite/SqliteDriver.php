<?php

namespace zibo\library\database\sqlite;

use zibo\core\Zibo;

use zibo\library\database\driver\AbstractTransactionDriver;
use zibo\library\database\sqlite\exception\SqliteException;
use zibo\library\database\DatabaseManager;
use zibo\library\database\Dsn;
use zibo\library\filesystem\File;
use zibo\library\String;
use zibo\library\System;

use \Exception;
use \SQLite3;

/**
 * Sqlite implementation of the database driver
 */
class SqliteDriver extends AbstractTransactionDriver {

    /**
     * Protocol of this driver
     * @var string
     */
    const PROTOCOL = 'sqlite';

    /**
     * Quote of a value
     * @var string
     */
    const QUOTE_VALUE = "'";

    /**
     * The resource of the Sqlite connection
     * @var resource
     */
    private $connection;

    /**
     * Definer for the database
     * @var SqliteDefiner
     */
    private $definer;

    /**
     * Constructs a new connection
     * @param zibo\library\database\Dsn $dsn connection parameters
     * @return null
     */
    public function __construct(Dsn $dsn) {
        if ($dsn->getProtocol() != self::PROTOCOL) {
            throw new SqliteException('Provided dsn does not have the sqlite protocol');
        }

        parent::__construct($dsn);
    }

    /**
     * Checks whether this connection is connected
     * @return boolean true if connected, false otherwise
     */
    public function isConnected() {
        return $this->connection === null ? false : true;
    }

    /**
     * Connects this connection
     * @return null
     * @throws zibo\library\database\mysql\exception\MysqlException when no connection could be made with the host
     * @throws zibo\library\database\mysql\exception\MysqlException when the database could not be selected
     */
    public function connect() {
        $file = new File($this->dsn->getPath());

        if (!$file->exists()) {
            $parent = $file->getParent();
            $parent->create();
        }

        try {
            $this->connection = new SQLite3($file->getAbsolutePath());
        } catch (Exception $exception) {
            $this->connection = null;
            throw new SqliteException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * Disconnects this connection
     * @return null
     */
    public function disconnect() {
        if (!$this->isConnected()) {
            return;
        }

        $this->connection->close();
        $this->connection = null;
    }

    /**
     * Executes an SQL script on the connection
     * @param string $sql SQL script
     * @return zibo\library\database\mysql\MysqlResult Result object
     * @throws zibo\library\database\mysql\exception\MysqlException when the provided SQL is empty
     * @throws zibo\library\database\mysql\exception\MysqlException when not connected to the database
     * @throws zibo\library\database\mysql\exception\MysqlErrorException when the SQL could not be executed
     */
    public function execute($sql) {
        if (String::isEmpty($sql)) {
            throw new SqliteException('Provided SQL is empty');
        }

        if (!$this->isConnected()) {
            $exception = new SqliteException('Not connected to the database');
            Zibo::getInstance()->runEvent('log', 'Execute ' . $sql, $exception->getMessage(), 1, DatabaseManager::LOG_NAME);
            throw $exception;
        }

        try {
            $resultResource = $this->connection->query($sql);

            if ($resultResource === false) {
                $errorCode = $this->connection->lastErrorCode();
                $errorMessage = $this->connection->lastErrorMessage();

                $exception = new SqliteErrorException($errorCode, $errorMessage);

                Zibo::getInstance()->runEvent('log', 'Execute ' . $sql, $exception->getMessage(), 1, DatabaseManager::LOG_NAME);

                throw $exception;
            }
        } catch (Exception $exception) {
            Zibo::getInstance()->runEvent('log', 'Execute ' . $sql, $exception->getMessage(), 1, DatabaseManager::LOG_NAME);

            throw $exception;
        }

        Zibo::getInstance()->runEvent('log', 'Execute ' . $sql, '', 0, DatabaseManager::LOG_NAME);

        $result = new SqliteResult($sql, $resultResource);

        if ($resultResource !== true) {
            $resultResource->finalize();
        }

        return $result;
    }

    /**
     * Gets the primary key of the last inserted record
     * @return string primary key of the last inserted record or null if no record has been inserted yet
     */
    public function getLastInsertId() {
        $id = null;

        if ($this->isConnected()) {
            $id = $this->connection->lastInsertRowID();
        }

        return $id;
    }

    /**
     * Quotes a database value
     * @param string $value value to quote
     * @return string quoted value
     * @throws zibo\library\database\exception\DatabaseException when $value is not a scalar value
     */
    public function quoteValue($value) {
        parent::quoteValue($value);

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $valueLength = strlen($value);

        if (is_numeric($value)) {
            return $value;
        }

        if (is_null($value) || $value === self::VALUE_NULL) {
            return self::VALUE_NULL;
        }

        if (!$this->isConnected()) {
            $this->connect();
        }

        $quotedValue = $this->connection->escapeString($value);
        if ($quotedValue === false) {
            throw new DatabaseException('Could not quote ' . $value);
        }

        return self::QUOTE_VALUE . $quotedValue . self::QUOTE_VALUE;
    }

    /**
     * Gets the database definer
     * @return zibo\library\database\mysql\MysqlDefiner
     */
    public function getDefiner() {
        if ($this->definer == null) {
            $this->definer = new SqliteDefiner($this, $this->dsn->getDatabase());
        }

        return $this->definer;
    }

//    /**
//     * Exports the database to a file
//     * @param zibo\library\filesystem\File $file
//     * @return null
//     * @throws zibo\ZiboException when an error eoccured
//     */
//    public function export(File $file) {
//        throw new SqliteException('This driver does not support exports');
//    }

}