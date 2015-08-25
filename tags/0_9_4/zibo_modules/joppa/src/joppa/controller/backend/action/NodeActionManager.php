<?php

namespace joppa\controller\backend\action;

use zibo\ZiboException;

/**
 * Manager of the node actions
 */
class NodeActionManager {

    /**
     * Instance for the singleton pattern
     * @var NodeActionManager
     */
    private static $instance;

    /**
     * Array with NodeAction objects as value and their route as key
     * @var array
     */
    private $actions;

    /**
     * Construct this manager
     * @return null
     */
    private function __construct() {
        $this->actions = array();
        $this->registerAction(new NodeGoAction());
        $this->registerAction(new NodeContentAction());
        $this->registerAction(new NodeVisibilityAction());
        $this->registerAction(new NodeAdvancedAction());
    }

    /**
     * Get the manager
     * @return NodeActionManager
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Checks whether an action is registered
     * @param string $action route of the action
     * @return boolean true if the action is registered, false otherwise
     */
    public function hasAction($action) {
        if (array_key_exists($action, $this->actions)) {
            return true;
        }

        return false;
    }

    /**
     * Get a action
     * @param string $action route of the action
     * @return NodeAction
     * @throws zibo\ZiboException when the action is not registered
     */
    public function getAction($action) {
        if (!array_key_exists($action, $this->actions)) {
            throw new ZiboException('No node action registered with route ' . $action);
        }

        return $this->actions[$action];
    }

    /**
     * Get the registered actions
     * @return array array with NodeAction objects
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * Register a node action
     * @param NodeAction $action
     * @return null;
     */
    public function registerAction(NodeAction $action) {
        $this->actions[$action->getRoute()] = $action;
    }

    /**
     * Unregister a node action
     * @param string $action route of the action
     * @return null
     */
    public function unregisterAction($action) {
        if ($this->hasAction($action)) {
            unset($this->actions[$action]);
        }
    }

}