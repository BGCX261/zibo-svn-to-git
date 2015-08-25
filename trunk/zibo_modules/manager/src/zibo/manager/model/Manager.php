<?php

namespace zibo\manager\model;

use zibo\core\Controller;
use zibo\core\Response;
use zibo\core\Request;

/**
 * Interface for a manager: a controller to manage shared data
 */
interface Manager extends Controller {

    /**
     * Method name of the index action
     * @var string
     */
    const ACTION_INDEX = 'indexAction';

    /**
     * Gets the name of this manager
     * @return string
     */
    public function getName();

    /**
     * Gets the icon of this manager
     * @return string
     */
    public function getIcon();

    /**
     * Gets the menu actions for this manager
     * @return array Array with the route of the action as key and the label of the action as value
     */
    public function getActions();

}