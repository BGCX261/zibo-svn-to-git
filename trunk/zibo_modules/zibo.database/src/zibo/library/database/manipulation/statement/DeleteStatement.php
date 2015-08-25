<?php

namespace zibo\library\database\manipulation\statement;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\expression\TableExpression;

/**
 * Statement to delete records from a table
 */
class DeleteStatement extends ConditionalStatement {

    /**
     * Adds a table this delete statement (only 1 table allowed)
     * @param zibo\library\database\manipulation\expression\TableExpression $table
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when a table has already been added
     */
    public function addTable(TableExpression $table) {
        if (count($this->tables)) {
            throw new DatabaseException('Only deletes on 1 table allowed');
        }

        parent::addTable($table);
    }

}