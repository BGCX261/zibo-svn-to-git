<?php

namespace zibo\database\admin\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;

/**
 * Decorator for the definition of a database connection
 */
class ConnectionDecorator implements Decorator {

    /**
     * URL where the name of the connection will point to
     * @var string
     */
    private $action;

    /**
     * Name of the default connection
     * @var string
     */
    private $defaultConnection;

    /**
     * Constructs a new connection decorator
     * @param $action
     * @param $defaultConnection
     */
    public function __construct($action = null, $defaultConnection = null) {
    	$this->action = $action;
    	$this->defaultConnection = $defaultConnection;
    }

    /**
     * Decorates a connection with it's definition
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
	public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
		$connection = $cell->getValue();

		$dsn = $connection->getDriver()->getDsn()->__toString();

		$value = $connection->getName();
		if ($value == $this->defaultConnection) {
			$value = '<strong>' . $value . '</strong>';
		}

		if ($this->action) {
			$anchor = new Anchor($value, $this->action . $connection->getName());
			$value = $anchor->getHtml();
		}

		$value .= '<div class="info">' . $dsn . '</div>';

		$cell->setValue($value);
	}

}