<?php

namespace zibo\library\database\sqlite\exception;

/**
 * Exception thrown by the Sqlite implementation of the database layer when a Sqlite error occurs
 */
class SqliteErrorException extends SqliteException {

    /**
     * Constructs a new Sqlite error exception
     * @param string $errorNumber Code of the error
     * @param string $errorMessage Message of the error
     * @return null
     */
    public function __construct($errorNumber, $errorMessage) {
        parent::__construct($errorNumber . ': ' . $errorMessage);
    }

}