<?php

namespace zibo\library\database;

use zibo\core\Zibo;

use zibo\library\database\exception\DatabaseException;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;
use \ReflectionClass;

/**
 * Manager of the database connections and drivers.
 */
class DatabaseManager {

    /**
     * Class name of the abstract database driver
     * @var string
     */
    const CLASS_DRIVER = 'zibo\\library\\database\\driver\\Driver';

    /**
     * Configuration key for the available drivers
     * @var string
     */
    const CONFIG_DRIVER = 'database.driver';

    /**
     * Configuration key for the available connections
     * @var string
     */
    const CONFIG_CONNECTION = 'database.connection';

    /**
     * Name of the default connection
     * @var string
     */
    const NAME_DEFAULT = 'default';

    /**
     * Name for the log messages
     * @var string
     */
    const LOG_NAME = 'database';

    /**
     * Instance of the manager, singleton pattern
     * @var DatabaseManager
     */
    private static $instance;

    /**
     * Array with all the registered drivers; the protocol of the driver as key and the driver class name as value
     * @var array
     */
    private $drivers;

    /**
     * Array with all the registered connections; the name as key and the driver instance as value
     * @var array
     */
    private $connections;

    /**
     * Name of the default connection
     * @var string
     */
    private $defaultConnectionName;

    /**
     * Constructs a new database manager: loads the drivers and the connections from the configuration
     * @return null
     */
    private function __construct() {
        $this->connections = array();
        $this->drivers = array();
        $this->defaultConnectionName = null;

        $zibo = Zibo::getInstance();

        $this->loadDriversFromConfig($zibo);
        $this->loadConnectionsFromConfig($zibo);
    }

    /**
     * Disconnects all connections on destruction of the manager
     * @return null
     */
    public function __destruct() {
        foreach ($this->connections as $connection) {
            $connection->disconnect();
        }
    }

    /**
     * Gets the instance of the database manager
     * @return DatabaseManager Instance of the database manager
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets the available drivers
     * @return array Array with the protocol as key and the driver class name as value
     */
    public function getDrivers() {
    	return $this->drivers;
    }

    /**
     * Gets a registered database connection
     * @param string $name Name of the connection, skip this argument to get the default connection
     * @return zibo\library\database\driver\Driver Instance of the database connection
     * @throws zibo\library\database\exception\DatabaseException when the database connection could not be found
     */
    public function getConnection($name = null) {
        if (!$name) {
            $name = $this->getDefaultConnectionName();
            if (!$name) {
                throw new DatabaseException('No database connections set');
            }
        }

        if (!$this->hasConnection($name)) {
            throw new DatabaseException('Database connection ' . $name . ' not found');
        }

        if (!$this->connections[$name]->isConnected()) {
            $this->connections[$name]->connect();
        }

        return $this->connections[$name];
    }

    /**
     * Gets all the database connections
     * @return array Array with the name of the connection as key and a instance of Driver as value
     * @see zibo\library\database\driver\Driver
     */
    public function getConnections() {
    	return $this->connections;
    }

    /**
     * Checks if a connection has been registered
     * @param string $name Name of the connection
     * @return boolean True if the connection exists, false otherwise
     */
    public function hasConnection($name) {
        return array_key_exists($name, $this->connections);
    }

    /**
     * Sets the default connection
     * @param string $defaultConnectionName Name of the new default connection
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the connection name is invalid or when the connection does not exist
     */
    public function setDefaultConnectionName($defaultConnectionName) {
        if (String::isEmpty($defaultConnectionName)) {
            throw new DatabaseException('Provided database name is empty');
        }

        if (!$this->hasConnection($defaultConnectionName)) {
            throw new DatabaseException('Database connection ' . $defaultConnectionName . ' does not exist');
        }

        $this->defaultConnectionName = $defaultConnectionName;
    }

    /**
     * Gets the name of the default connection
     * @return string Name of the default connection
     */
    public function getDefaultConnectionName() {
        return $this->defaultConnectionName;
    }

    /**
     * Registers a database driver with it's protocol in the manager
     * @param string $protocol Database protocol of this driver
     * @param string $className Class name of the driver
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the protocol or class name is empty or invalid
     * @throws zibo\library\database\exception\DatabaseException when the database driver does not exist or is not a valid driver class
     */
    public function registerDriver($protocol, $className) {
        if (String::isEmpty($protocol)) {
            throw new DatabaseException('Provided database protocol is empty');
        }

        if (String::isEmpty($className)) {
            throw new DatabaseException('Provided database driver class name is empty');
        }

        try {
            $reflection = new ReflectionClass($className);
        } catch (Exception $e) {
            throw new DatabaseException('Provided database driver class does not exist');
        }

        if (!$reflection->isSubclassOf(self::CLASS_DRIVER)) {
            throw new DatabaseException('Provided database driver class is not a subclass of ' . self::CLASS_DRIVER);
        }

        $this->drivers[$protocol] = $className;
    }

    /**
     * Registers a connection in the manager
     * @param string $name Name of the connection
     * @param Dsn $dsn DSN connection properties
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the name is invalid or already registered and connected
     * @throws zibo\library\database\exception\DatabaseException when the protocol has no driver available
     */
    public function registerConnection($name, Dsn $dsn) {
        if (String::isEmpty($name)) {
            throw new DatabaseException('Provided database name is empty');
        }

        $protocol = $dsn->getProtocol();
        if (!isset($this->drivers[$protocol])) {
            throw new DatabaseException('Protocol ' . $protocol . ' has no database driver available');
        }

        if (isset($this->connections[$name]) && $this->connections[$name]->isConnected()) {
            throw new DatabaseException('Database ' . $name . ' is already registered and connected. Disconnect the connection first');
        }

        $this->connections[$name] = new $this->drivers[$protocol]($dsn);

        if ($this->defaultConnectionName == null) {
            $this->setDefaultConnectionName($name);
        }
    }

    /**
     * Loads the database drivers from the Zibo configuration
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return null
     */
    private function loadDriversFromConfig(Zibo $zibo) {
        $zibo->runEvent(Zibo::EVENT_LOG, 'Loading database drivers', '', 0, self::LOG_NAME);

        $drivers = $zibo->getConfigValue(self::CONFIG_DRIVER, array());

        foreach ($drivers as $protocol => $className) {
            $this->registerDriver($protocol, $className);
        }
    }

    /**
     * Loads the database connections from the Zibo configuration
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when the configuration contains an invalid connection
     * @throws zibo\library\database\exception\DatabaseException when the default connection does not exist
     */
    private function loadConnectionsFromConfig(Zibo $zibo) {
        $zibo->runEvent(Zibo::EVENT_LOG, 'Loading database connections', '', 0, self::LOG_NAME);

        $connections = $zibo->getConfigValue(self::CONFIG_CONNECTION, array());

        if (!is_array($connections)) {
            $connections = array(self::NAME_DEFAULT => $connections);
        }

        $defaultConnectionName = null;
        foreach ($connections as $name => $dsn) {
            if ($name == self::NAME_DEFAULT) {
                $defaultConnectionName = $name;
            }

            try {
                $dsn = new Dsn($dsn);
                $this->registerConnection($name, $dsn);
            } catch (DatabaseException $e) {
                if ($name == self::NAME_DEFAULT) {
                    $defaultConnectionName = $dsn;
                } else {
                    throw $e;
                }
            }
        }

        if ($defaultConnectionName != null) {
            $this->setDefaultConnectionName($defaultConnectionName);
        }
    }

}