<?php

namespace zibo\library\database\mysql\exception;

/**
 * Exception thrown by the MySQL implementation of the database layer when a MySQL error occurs
 */
class MysqlErrorException extends MysqlException {

    /**
     * Constructs a new MySQL error exception
     * @param string message (optional)
     * @return null
     */
    public function __construct($message = null) {
        $errorNumber = mysql_errno();
        $errorMessage = mysql_error();

        if ($errorNumber) {
            $message .= ($message == '' ? "\n" : '') . $errorNumber . ': ' . $errorMessage;
        } else {
            $errorNumber = 0;
        }

        parent::__construct($message, $errorNumber);
    }

}