<?php

namespace zibo\manager\model;

use zibo\admin\controller\AbstractController;

use zibo\core\Response;
use zibo\core\Request;

/**
 * Abstract implementation of a manager
 */
class AbstractManager extends AbstractController implements Manager {

    /**
     * Name of this manager
     * @var string
     */
    private $name;

    /**
     * Path to the icon of this manager
     * @var string
     */
    private $icon;

    /**
     * Constructs a new manager
     * @param string $nameTranslationKey translation key for the name of this manager
     * @param string $icon path to the icon of this manager (optional)
     * @return null
     */
    public function __construct($nameTranslationKey, $icon = null) {
        $translator = $this->getTranslator();
        $this->name = $translator->translate($nameTranslationKey);
        $this->icon = $icon;
    }

    /**
     * Empty action, override for the manager's start page
     * @return null
     */
    public function indexAction() {

    }

    /**
     * Gets the name of this manager
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the pâth of the icon of this manager
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Gets the menu actions for this manager
     * @return array Array with the route of the action as key and the label of the action as value
     */
    public function getActions() {
        return array();
    }

}