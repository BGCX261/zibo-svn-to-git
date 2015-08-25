<?php

namespace zibo\database\admin\model;

use zibo\core\Zibo;

use zibo\library\config\Config;
use zibo\library\database\DatabaseManager;
use zibo\library\database\Dsn;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

use zibo\ZiboException;

use \Exception;

/**
 * Model of the database connections
 */
class ConnectionModel {

    /**
     * Translation key for an error
     * @var string
     */
	const TRANSLATION_ERROR = 'error';

	/**
	 * Instance of the database manager
	 * @var zibo\library\database\DatabaseManager
	 */
	private $manager;

	/**
	 * Constructs a new database connection model
	 * @return null
	 */
	public function __construct() {
		$this->manager = DatabaseManager::getInstance();
	}

	/**
	 * Gets all the connections
	 * @return array Array with the name of the connection as key and the Connection object as value
	 */
	public function getConnections() {
		$managerConnections = $this->manager->getConnections();

        $connections = array();
        foreach ($managerConnections as $name => $driver) {
            $connections[$name] = new Connection($name, $driver);
        }

        return $connections;
	}

	/**
	 * Gets a connection by name
	 * @param string $name Name of the connection
	 * @return Connection|null An instance of Connection if found, null otherwise
	 */
	public function getConnection($name) {
        $connections = $this->getConnections();
        if (!isset($connections[$name])) {
        	return null;
        }
        return $connections[$name];
	}

	/**
	 * Gets the available protocols
	 * @return array Array with the name of the protocol as key and the name of the driver class as value
	 */
	public function getProtocols() {
		return $this->manager->getDrivers();
	}

	/**
	 * Gets the default connection
	 * @return Connection
	 */
	public function getDefaultConnection() {
		$connections = $this->getConnections();
		if (!$connections) {
			return null;
		}

		$defaultConnectionName = $this->manager->getDefaultConnectionName();
		return $connections[$defaultConnectionName];
	}

	/**
	 * Sets the default connection
	 * @param string $name Name of the new default connection
	 * @return null
	 * @throws zibo\ZiboException when there is no connection with the provided name
	 */
	public function setDefaultConnection($name = null) {
		$connection = $this->getConnection($name);
		if ($connection == null) {
			throw new ZiboException('Connection ' . $name . ' is not registered');
		}

        $defaultKey = DatabaseManager::CONFIG_CONNECTION . Config::TOKEN_SEPARATOR . DatabaseManager::NAME_DEFAULT;
        Zibo::getInstance()->setConfigValue($defaultKey, $name);
	}

	/**
	 * Saves a connection
	 * @param string $name Name of the connection
	 * @param string $dsn DSN string for the connection
	 * @param string $oldName Old name if we are doing a rename action
	 * @return null
	 * @throws Exception when an error occurs
	 */
    public function saveConnection($name, $dsn, $oldName = null) {
        $zibo = Zibo::getInstance();
        $updateDefault = false;

        $baseKey = DatabaseManager::CONFIG_CONNECTION . Config::TOKEN_SEPARATOR;
        if (!empty($oldName) && $name != $oldName) {
        	$defaultConnection = $this->getDefaultConnection();
        	if ($defaultConnection->getName() == $oldName) {
        		$updateDefault = true;
        	}
            $zibo->setConfigValue($baseKey . $oldName, null);
        }

        $dsn = new Dsn($dsn);
        try {
            $this->manager->registerConnection($name, $dsn);
            $zibo->setConfigValue($baseKey . $name, $dsn->__toString());
            if ($updateDefault) {
                $this->setDefaultConnection($name);
            }
        } catch (Exception $exception) {
            $error = new ValidationError(self::TRANSLATION_ERROR, '%error%', array('error' => $exception->getMessage()));

            $validationException = new ValidationException();
            $validationException->addErrors('dsn', array($error));

            throw $validationException;
        }
    }

    /**
     * Deletes connections from the model
     * @param array $connections Array with connection names or Connection instances
     * @return null
     */
    public function deleteConnections(array $connections) {
        $zibo = Zibo::getInstance();

        $defaultConnection = $this->getDefaultConnection();
        $defaultConnectionName = $defaultConnection->getName();

        $baseKey = DatabaseManager::CONFIG_CONNECTION . Config::TOKEN_SEPARATOR;
        foreach ($connections as $connection) {
        	if ($connection instanceof Connection) {
        		$name = $connection->getName();
        	} else {
        		$name = $connection;
        	}
            $zibo->setConfigValue($baseKey . $name, null);
            if ($name == $defaultConnectionName) {
            	$zibo->setConfigValue($baseKey . DatabaseManager::NAME_DEFAULT, null);
            }
        }
    }

}