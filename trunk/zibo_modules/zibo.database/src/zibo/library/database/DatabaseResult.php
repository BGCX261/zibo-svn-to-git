<?php

namespace zibo\library\database;

use zibo\library\database\exception\DatabaseException;

use \Iterator;

/**
 * Generic database result
 */
class DatabaseResult implements Iterator {

    /**
     * The SQL which generated this result
     * @var string
     */
    protected $sql;

    /**
     * Array with the column names
     * @var array
     */
    protected $columns;

    /**
     * Number of columns in this result
     * @var integer
     */
    protected $columnCount;

    /**
     * Array with the rows of this result
     * @var array
     */
    protected $rows;

    /**
     * Total number of records
     * @var integer
     */
    protected $rowCount;

    /**
     * Internal row pointer
     * @var integer
     */
    protected $rowPointer = 0;

    /**
     * Constructs a new result
     * @param string $sql SQL which generated this result
     * @param array $columns Array with the column names
     * @param array $rows Array filles with row arrays which have the column name as key
     * @return null
     */
    public function __construct($sql, array $columns = array(), array $rows = array()) {
        $this->sql = $sql;
        $this->columns = $columns;
        $this->rows = $rows;

        $this->columnCount = count($columns);
        $this->rowCount = count($rows);
    }

    /**
     * Gets the SQL which generated this result
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }

    /**
     * Gets the column names of the records
     * @return array Array with the column names
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Get the number of columns
     * @return integer Number of columns
     */
    public function getColumnCount() {
        return $this->columnCount;
    }

    /**
     * Gets all the rows
     * @return array Array filled with row arrays which have the column name as key
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Gets the number of rows
     * @return integer Number of rows
     */
    public function getRowCount() {
        return $this->rowCount;
    }

    /**
     * Gets the first row
     * @return array Array with the data of the first row
     */
    public function getFirst() {
    	$this->checkRowCount();
        return $this->rows[0];
    }

    /**
     * Gets the last row
     * @return array Array with the data of the last row
     */
    public function getLast() {
    	$this->checkRowCount();
        return $this->rows[$this->rowCount - 1];
    }

    /**
     * Iterator implementation: resets the internal row pointer
     * @return null
     */
    public function rewind() {
        $this->rowPointer = 0;
    }

    /**
     * Iterator implementation: gets the current row
     * @return array Array with the columns as key and the column values as value
     */
    public function current() {
        return $this->rows[$this->rowPointer];
    }

    /**
     * Iterator implementation: gets the internal row pointer
     * @return integer Pointer to the current row
     */
    public function key() {
        return $this->rowPointer;
    }

    /**
     * Iterator implementation: increases the internal row pointer to the next row
     * @return null
     */
    public function next() {
        $this->rowPointer++;
    }

    /**
     * Iterator implementation: checks whether the internal row pointer is on a valid row
     * @return boolean True if the internal row pointer is on a valid row, false otherwise
     */
    public function valid() {
        return isset($this->rows[$this->rowPointer]);
    }

    /**
     * Checks whether there are rows in this result
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when there are no rows in this result
     */
    protected function checkRowCount() {
        if (!$this->rowCount) {
            throw new DatabaseException('No rows in this result');
        }
    }

}