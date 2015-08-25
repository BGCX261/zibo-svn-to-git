<?php

namespace zibo\library\database\sqlite;

use zibo\library\database\sqlite\exception\SqliteException;
use zibo\library\database\DatabaseResult;

use \SQLite3Result;

/**
 * Sqlite implementation of a database result
 */
class SqliteResult extends DatabaseResult {

    /**
     * Constructs a new Sqlite result
     * @param string $sql SQL which generated this result
     * @param boolean\SQLite3Result $result Boolean or a Sqlite result
     * @return null
     * @throws zibo\library\database\sqlite\exception\SqliteException when $resource is not a valid resource
     */
    public function __construct($sql, $result) {
        parent::__construct($sql);

        if ($result === true || $result->numColumns() == 0) {
            return;
        }

        $this->initializeResult($result);
    }

    /**
     * Initializes this result from the resource of a Sqlite result
     * @param SQLite3Result $result Resource of the Sqlite result
     * @return null
     */
    private function initializeResult(SQLite3Result $result) {
        $isColumnsSet = false;

        while ($row = $result->fetchArray(SQLITE_ASSOC)) {
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