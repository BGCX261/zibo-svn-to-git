<?php

namespace zibo\library\database\manipulation\expression;

use zibo\library\database\exception\DatabaseException;
use zibo\library\Number;

/**
 * Limit clause of a statement
 */
class LimitExpression extends Expression {

    /**
     * Number of rows to show
     * @var int
     */
    private $rowCount;

    /**
     * Offset of the limitation, starting position
     * @var int
     */
    private $offset;

    /**
     * Construct the limit clause
     * @return null
     */
    public function __construct($rowCount, $offset = null) {
        $this->setRowCount($rowCount);
        $this->setOffset($offset);
    }

    /**
     * Set the row count of this limit clause
     * @param int $rowCount
     * @return null
     * @throws zibo\ZiboException when $rowCount is not numeric
     * @throws zibo\library\database\exception\DatabaseException when $rowCount is negative
     */
    private function setRowCount($rowCount) {
        if (!Number::isNumeric($rowCount, Number::NOT_NEGATIVE | Number::NOT_FLOAT)) {
            throw new DatabaseException('Provided row count is negative');
        }

    	$this->rowCount = $rowCount;
    }

    /**
     * Get the row count of this limit clause
     * @return int rowCount
     */
    public function getRowCount() {
        return $this->rowCount;
    }

    /**
     * Set the offset of this limit clause
     * @param int $offset
     * @return null
     * @throws zibo\ZiboException when $offset is not numeric
     * @throws zibo\library\database\exception\DatabaseException when $offset is negative
     */
    private function setOffset($offset = null) {
        if ($offset !== null && !Number::isNumeric($offset, Number::NOT_NEGATIVE | Number::NOT_FLOAT)) {
            throw new DatabaseException('Provided offset is negative');
        }

        $this->offset = $offset;
    }

    /**
     * Get the offset of the limitation
     * @return int offset
     */
    public function getOffset() {
        return $this->offset;
    }

}