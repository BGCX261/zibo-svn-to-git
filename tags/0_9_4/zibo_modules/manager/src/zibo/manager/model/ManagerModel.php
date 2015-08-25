<?php

namespace zibo\manager\model;

use zibo\core\Zibo;

use zibo\library\ObjectFactory;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Model of the data managers
 */
class ManagerModel {

    /**
     * Configuration key for the managers
     * @var string
     */
    const CONFIG_MANAGERS = 'manager';

    /**
     * Class name of the manager interface
     * @var string
     */
    const INTERFACE_MANAGER = 'zibo\\manager\\model\\Manager';

    /**
     * Instance of the manager model
     * @var ManagerModel
     */
    private static $instance;

    /**
     * Array with the managers
     * @var array
     */
    private $managers;

    /**
     * Constructs a new manager model
     * @return null
     */
    private function __construct() {
        $this->loadManagers();
    }

    /**
     * Gets the instance of the manager model
     * @return ManagerModel
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Checks if a manager exists
     * @param string $name name of the manager
     * @return boolean
     * @throws zibo\ZiboException when the name is empty or not a string
     */
    public function hasManager($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Name is empty');
        }
        return array_key_exists($name, $this->managers);
    }

    /**
     * Gets a manager
     * @param string $name name of the manager
     * @return zibo\manager\controller\Manager
     * @throws zibo\ZiboException when the name is empty or not a string
     * @throws zibo\ZiboException when there is no manager with the provided name
     */
    public function getManager($name) {
        if (!$this->hasManager($name)) {
            throw new ZiboException('No manager found for ' . $name);
        }

        return $this->managers[$name];
    }

    /**
     * Gets all the managers
     * @return array Array with the name of the manager as key and the manager as value
     */
    public function getManagers() {
        return $this->managers;
    }

    /**
     * Gets an array with the actions of all the managers
     * @param string $basePath URL to the manager controller
     * @return array Array with the label of the action as key and the URL or another action array as value
     */
    public function getManagerMenuActions($basePath) {
        $managers = $this->getManagers();

        $menu = array();
        foreach ($managers as $name => $manager) {
            $actions = $manager->getActions();
            if ($actions) {
                $menuActions = array();
                foreach ($actions as $actionRoute => $actionName) {
                    $menuActions[$actionName] = $basePath . $name . '/' . $actionRoute;
                }
                $menu[$manager->getName()] = $menuActions;
            } else {
                $menu[$manager->getName()] = $basePath . $name;
            }
        }

        return $menu;
    }

    /**
     * Loads the data managers from the Zibo configuration
     * @return null
     */
    private function loadManagers() {
        $this->managers = array();

        $objectFactory = new ObjectFactory();
        $managers = Zibo::getInstance()->getConfigValue(self::CONFIG_MANAGERS);
        foreach ($managers as $name => $managerClass) {
            try {
                $manager = $objectFactory->create($managerClass, self::INTERFACE_MANAGER);
                $this->managers[$name] = $manager;
            } catch (Exception $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
            }
        }
    }

}