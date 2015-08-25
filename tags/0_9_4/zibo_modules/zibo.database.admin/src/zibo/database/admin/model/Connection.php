<?php

namespace zibo\database\admin\model;

use zibo\library\database\driver\Driver;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Data container for a database connection
 */
class Connection {

    /**
     * Name of the connection
     * @var string
     */
	private $name;

	/**
	 * Instance of the driver for this connection
	 * @var zibo\library\database\AbstractDriver
	 */
	private $driver;

	/**
	 * Flag to see if this connection is connectable
	 * @var boolean
	 */
	private $isConnectable;

	/**
	 * Exception which occured when trying to connect
	 * @var Exception
	 */
	private $connectException;

	/**
	 * Cache for the table list
	 * @var array
	 */
	private $tableList;

	/**
	 * Cache for the table definitions
	 * @var array
	 */
	private $tables = array();

	/**
     * Construct a new connection
     * @param string $name
     * @param zibo\library\database\driver\Driver $driver
     * @return null
	 */
	public function __construct($name, Driver $driver) {
		$this->setName($name);
		$this->driver = $driver;
	}

	/**
	 * Sets the name of the connection
	 * @param string $name
	 * @return null
	 * @throws zibo\ZiboException when an invalid name is provided
	 */
	private function setName($name) {
		if (String::isEmpty($name)) {
			throw new ZiboException('Provided name is empty');
		}
		$this->name = $name;
	}

	/**
     * Gets the name of the connection
     * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets the driver of this connection
	 * @param boolean $connect Set to true to make sure the driver is connected
	 * @return zibo\library\database\driver\Driver
	 */
	public function getDriver($connect = false) {
	    if ($connect && !$this->driver->isConnected()) {
	        $this->driver->connect();
	    }

		return $this->driver;
	}

	/**
	 * Checks if this connection is connectable
	 * @return boolean True when the connection is connectable, false otherwise
	 * @see getConnectException
	 */
	public function isConnectable() {
	    if ($this->isConnectable !== null) {
	        return $this->isConnectable;
	    }

        $driver = $this->getDriver();
        if ($driver->isConnected()) {
            $this->isConnectable = true;
        } else {
            try {
                $driver->connect();
                $driver->disconnect();

                $this->isConnectable = true;
            } catch (Exception $exception) {
                $this->connectException = $exception;

                $this->isConnectable = false;
            }
        }

        return $this->isConnectable;
	}

	/**
     * Gets the exception which occured when trying to connect
     * @return Exception
	 */
	public function getConnectException() {
	    return $this->connectException;
	}

	/**
	 * Gets a list of the tables in the database of this connection
	 * @return array Array with the names of the tables
	 */
    public function getTableList() {
        if ($this->tableList) {
            return $this->tableList;
        }

        $driver = $this->getDriver(true);
        $definer = $driver->getDefiner();

        $this->tableList = $definer->getTableList();

        return $this->tableList;
    }

    /**
     * Gets the definition of a table
     * @param string $name The name of the table
     * @return zibo\library\database\definition\Table The table definition
     */
    public function getTableDefinition($name) {
        if (array_key_exists($name, $this->tables)) {
            return $this->tables[$name];
        }

        $driver = $this->getDriver(true);
        $definer = $driver->getDefiner();
        $table = $definer->getTable($name);

        $this->tables[$name] = $table;

        return $table;
    }

}