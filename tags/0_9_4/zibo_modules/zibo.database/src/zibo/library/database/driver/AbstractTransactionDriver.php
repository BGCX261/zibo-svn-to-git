<?php

namespace zibo\library\database\driver;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\Dsn;
use zibo\library\filesystem\File;

/**
 * Abstract driver for a database connection with transaction support
 */
abstract class AbstractTransactionDriver extends AbstractDriver {

    /**
     * Flag to see if a transaction is started
     * @var boolean
     */
    protected $isTransactionStarted;

    /**
     * Constructs a new connection with a DSN
     * @param zibo\library\database\Dsn $dsn DSN with the connection parameters
     * @return null
     */
    public function __construct(Dsn $dsn) {
        parent::__construct($dsn);

        $this->isTransactionStarted = false;
    }

    /**
     * Starts a transaction
     * @return boolean True if a new transaction is started, false when a transaction is already in progress
     */
    public function startTransaction() {
        if ($this->isTransactionStarted()) {
            return false;
        }

        $this->execute('BEGIN');

        $this->isTransactionStarted = true;

        return $this->isTransactionStarted;
    }

    /**
     * Commits the transaction in progress
     * @return null
     * @throws zibo\library\database\mysql\exception\MysqlException when no transaction is started
     */
    public function commitTransaction() {
        if (!$this->isTransactionStarted()) {
            throw new DatabaseException('No transaction to commit, use startTransaction first');
        }

        $this->execute('COMMIT');

        $this->isTransactionStarted = false;
    }

    /**
     * Rolls back the transaction in progress
     * @return null
     * @throws zibo\library\database\mysql\exception\MysqlException when no transaction is started
     */
    public function rollbackTransaction() {
        if (!$this->isTransactionStarted()) {
            throw new DatabaseException('No transaction to rollback, use startTransaction first');
        }

        $this->execute('ROLLBACK');

        $this->isTransactionStarted = false;
    }

    /**
     * Checks whether a transaction is in progress
     * @return boolean True if a transaction is in progress, false otherwise
     */
    public function isTransactionStarted() {
        return $this->isTransactionStarted;
    }

    /**
     * Imports a SQL file on this connection
     * @param zibo\library\filesystem\File $file SQL file
     * @return null
     */
    public function import(File $file) {
        if (!$file->exists()) {
            throw new DatabaseException('Provided file does not exist');
        }

        $content = $file->read();
        $content = preg_replace('#/\*.*?\*/#s', '', $content);

        $sqls = array();

        $sql = '';

        $lines  = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || substr($line, 0, 2) == '--') {
                continue;
            }

            $sql .= $line;

            if (substr($sql, -1, 1) == ';') {
                $sql = substr($sql, 0, -1);

                $sqls[] = $sql;

                $sql = '';
            }
        }

        $sql[] = $sql;

        $transactionStarted = $this->startTransaction();
        try {

            foreach ($sqls as $sql) {
                $this->execute($sql);
            }

            if ($transactionStarted) {
                $this->commitTransaction();
            }
        } catch (Exception $exception) {
            if ($transactionStarted) {
                $this->rollbackTransaction();
            }
        }
    }

}