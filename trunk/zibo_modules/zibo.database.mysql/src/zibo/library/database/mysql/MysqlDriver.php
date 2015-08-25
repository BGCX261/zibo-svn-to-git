<?php

namespace zibo\library\database\mysql;

use zibo\core\Zibo;

use zibo\library\database\driver\AbstractTransactionDriver;
use zibo\library\database\mysql\exception\MysqlErrorException;
use zibo\library\database\mysql\exception\MysqlException;
use zibo\library\database\DatabaseManager;
use zibo\library\database\Dsn;
use zibo\library\filesystem\File;
use zibo\library\String;
use zibo\library\System;

/**
 * MySQL implementation of the database driver
 */
class MysqlDriver extends AbstractTransactionDriver {

    /**
     * Protocol of this driver
     * @var string
     */
    const PROTOCOL = 'mysql';

    /**
     * Quote of an identifier
     * @var string
     */
    const QUOTE_IDENTIFIER = '`';

    /**
     * Quote of aa value
     * @var string
     */
    const QUOTE_VALUE = "'";

    /**
     * The resource of the MySQL connection
     * @var resource
     */
    private $connection;

    /**
     * Time out before performing a ping on the database connection
     * @var integer
     */
    private $timeOut;

    /**
     * Date of the last activity to the server
     * @var integer
     */
    private $timeLastActivity;

    /**
     * Definer for the database
     * @var MysqlDefiner
     */
    private $definer;

    /**
     * Constructs a new connection
     * @param zibo\library\database\Dsn $dsn connection parameters
     * @return null
     */
    public function __construct(Dsn $dsn) {
        if ($dsn->getProtocol() != self::PROTOCOL) {
            throw new MysqlException('Provided dsn does not have the mysql protocol');
        }

        if (!function_exists('mysql_connect')) {
            throw new MysqlException('Could not create a MysqlDriver instance. Your PHP installation does not support MySQL, please install the MySQL extension.');
        }

        parent::__construct($dsn);

        $this->isTransactionStarted = false;
    }

    /**
     * Checks whether this connection is connected
     * @return boolean true if connected, false otherwise
     */
    public function isConnected() {
        return is_resource($this->connection);
    }

    /**
     * Connects this connection
     * @return null
     * @throws zibo\library\database\mysql\exception\MysqlException when no connection could be made with the host
     * @throws zibo\library\database\mysql\exception\MysqlException when the database could not be selected
     */
    public function connect() {
        $host = $this->dsn->getHost();
        $port = $this->dsn->getPort();
        if ($port) {
            $host .= ':' . $port;
        }
        $username = $this->dsn->getUsername();
        $password = $this->dsn->getPassword();
        $database = $this->dsn->getDatabase();

        $this->connection = @mysql_connect($host, $username, $password, true);
        if (!$this->isConnected()) {
            $error = error_get_last();
            throw new MysqlException('Could not connect to ' . $host . ': ' . $error['message']);
        }

        if (!mysql_set_charset('utf8', $this->connection)) {
            $this->execute('SET CHARACTER SET utf8');
            $this->execute('SET NAMES utf8');
        }

        if (@mysql_select_db($database, $this->connection) === false) {
            $this->disconnect();
            throw new MysqlException('Could not select database ' . $database);
        }

        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Connected to database mysql://' . ($username ? $username . ($password ? ':*****' : '') . '@' : '') . $host . '/' . $database, '', 0, DatabaseManager::LOG_NAME);

        $this->timeOut = ini_get('mysql.connect_timeout') - 1;
        $this->timeLastActivity = time();
    }

    /**
     * Disconnects this connection
     * @return null
     */
    public function disconnect() {
        if (!$this->isConnected()) {
            return;
        }

        @mysql_close($this->connection);

        $this->connection = null;
    }

    /**
     * Pings the connection and make sure it's connected, usefull for long idled scripts.
     * @return null
     */
    private function ping() {
        if (!$this->connection) {
            $this->connect();
        } elseif (!@mysql_ping($this->connection)) {
            $this->connect();
        }
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
            throw new MysqlException('Provided SQL is empty');
        }

        $timeNow = time();
        if ($this->timeLastActivity && $timeNow - $this->timeOut > $this->timeLastActivity) {
            $this->ping();
        }
        $this->timeLastActivity = $timeNow;

        if (!$this->isConnected()) {
            $exception = new MysqlException('Not connected to the database');
            Zibo::getInstance()->runEvent('log', 'Execute ' . $sql, $exception->getMessage(), 1, DatabaseManager::LOG_NAME);
            throw $exception;
        }

        $resultResource = @mysql_query($sql, $this->connection);

        if ($resultResource === false) {
            $exception = new MysqlErrorException($sql);
            Zibo::getInstance()->runEvent('log', 'Execute ' . $sql, $exception->getMessage(), 1, DatabaseManager::LOG_NAME);
            throw $exception;
        }

        Zibo::getInstance()->runEvent('log', 'Execute ' . $sql, '', 0, DatabaseManager::LOG_NAME);

        $result = new MysqlResult($sql, $resultResource);

        if ($resultResource !== true) {
            @mysql_free_result($resultResource);
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
            $id = mysql_insert_id($this->connection);
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

        $quotedValue = mysql_real_escape_string($value, $this->connection);
        if ($quotedValue === false) {
            throw new DatabaseException('Could not quote ' . $value);
        }

        return self::QUOTE_VALUE . $quotedValue . self::QUOTE_VALUE;
    }

    /**
     * Quotes a database identifier
     * @param string $identifier
     * @return string quoted identifier
     * @throws zibo\library\database\exception\DatabaseException when $identifier is not a scalar value or when $identifier is empty
     */
    public function quoteIdentifier($identifier) {
        parent::quoteIdentifier($identifier);

        $identifier = str_replace(self::QUOTE_IDENTIFIER, self::QUOTE_IDENTIFIER . self::QUOTE_IDENTIFIER, $identifier);

        return self::QUOTE_IDENTIFIER . $identifier . self::QUOTE_IDENTIFIER;
    }

    /**
     * Gets the database definer
     * @return zibo\library\database\mysql\MysqlDefiner
     */
    public function getDefiner() {
        if ($this->definer == null) {
            $this->definer = new MysqlDefiner($this, $this->dsn->getDatabase());
        }

        return $this->definer;
    }

    /**
     * Exports the database to a file
     * @param zibo\library\filesystem\File $file
     * @return null
     * @throws zibo\ZiboException when an error eoccured
     */
    public function export(File $file) {
        $extension = $file->getExtension();
        if ($extension != 'sql') {
            throw new MysqlException('Provided file needs to have an sql extension');
        }

        $dsn = $this->getDsn();
        $username = $dsn->getUsername();
        $password = $dsn->getPassword();
        $database = $dsn->getDatabase();

        $command = 'mysqldump --user=' . $username;
        if ($password) {
            $command .= ' --password=' . $password;
        }
        $command .= ' ' . $database;
        $command .= ' > ' . $file->getAbsolutePath();

        System::execute($command);
    }

}