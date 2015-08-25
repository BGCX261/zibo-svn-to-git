<?php

namespace zibo\library\database\mysql;

use zibo\library\database\mysql\exception\MysqlException;
use zibo\library\database\DatabaseResult;

/**
 * MySQL implementation of a database result
 */
class MysqlResult extends DatabaseResult {

    /**
     * Constructs a new MySQL result
     * @param string $sql SQL which generated this result
     * @param resource $result Resource of a MySQL result
     * @return null
     * @throws zibo\library\database\mysql\exception\MysqlException when $resource is not a valid resource
     */
    public function __construct($sql, $result) {
        parent::__construct($sql);

        if ($result === true) {
            return;
        }

        $this->initializeResult($result);
    }

    /**
     * Initializes this result from the resource of a MySQL result
     * @param resource $result Resource of the MySQL result
     * @return null
     * @throws zibo\library\database\mysql\exception\MysqlException when $resource is not a valid resource
     */
    private function initializeResult($result) {
        if (!is_resource($result)) {
            throw new MysqlException('Provided result is not a resource');
        }

        $isColumnsSet = false;

        while ($row = mysql_fetch_assoc($result)) {
            if (!$isColumnsSet) {
                foreach ($row as $columnName => $value) {
                    $this->columns[] = $columnName;
                }
                $isColumnsSet = true;
            }

            $this->rows[] = $row;
        }

        $this->columnCount = count($this->columns);
        $this->rowCount = count($this->rows);
    }

}