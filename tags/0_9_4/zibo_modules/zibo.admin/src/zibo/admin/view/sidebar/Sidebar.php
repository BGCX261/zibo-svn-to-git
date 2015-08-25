<?php

namespace zibo\admin\view\sidebar;

use zibo\core\View;

use zibo\library\i18n\I18n;

/**
 * The model for a sidebar
 */
class Sidebar {

    /**
     * Array with the actions of this sidebar
     * @var array
     */
    private $actions;

    /**
     * Array with the panels of this sidebar
     * @var array
     */
    private $panels;

    /**
     * The information message of this sidebar
     * @var string
     */
    private $information;

    /**
     * Constructs a new sidebar
     * @return null
     */
    public function __construct() {
        $this->actions = array();
        $this->panels = array();
        $this->information = null;
    }

    /**
     * Adds a action to this sidebar
     * @param string $url URL of the action
     * @param string $label Label of the action
     * @param boolean $translate Flag to translate the label
     * @param array $translateVariables Array with variables for the translator
     * @return null
     */
    public function addAction($url, $label, $translate = false, array $translateVariables = array()) {
        if ($translate) {
            $translator = I18n::getInstance()->getTranslator();
            $label = $translator->translate($label, $translateVariables);
        }
        $this->actions[$url] = $label;
    }

    /**
     * Removes a action from this sidebar
     * @param string $url URL of the action
     * @return null
     */
    public function removeAction($url) {
        if (array_key_exists($url, $this->actions)) {
            unset($this->actions[$url]);
        }
    }

    /**
     * Checks whether this sidebar has actions
     * @return boolean True whene this sidebar contains actions, false otherwise
     */
    public function hasActions() {
        return $this->actions ? true : false;
    }

    /**
     * Gets the actions of this sidebar
     * @return array Array with the URL of the action as key and the label as value
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * Adds a panel to the sidebar
     * @param zibo\core\View $panel View of the panel
     * @return null
     */
    public function addPanel(View $panel) {
        $this->panels[] = $panel;
    }

    /**
     * Checks whether this sidebar has panels
     * @return boolean True if this sidebar contains panels, false otherwise
     */
    public function hasPanels() {
        return $this->panels ? true : false;
    }

    /**
     * Gets the panels of the sidebar
     * @return array Array with View objects
     */
    public function getPanels() {
        return $this->panels;
    }

    /**
     * Sets the information message
     * @param string $information Information message or translation key for the information message
     * @param boolean $translate Flag to translate the information string
     * @param array $translateVariables Array with variables for the translator
     * @return null
     */
    public function setInformation($information, $translate = false, array $translateVariables = array()) {
        if ($translate) {
            $translator = I18n::getInstance()->getTranslator();
            $information = $translator->translate($information, $translateVariables);
        }

        $this->information = $information;
    }

    /**
     * Checks if the sidebar has a information message
     * @return boolean True when there is a information message, false otherwise
     */
    public function hasInformation() {
        return $this->information ? true : false;
    }

    /**
     * Gets the information message
     * @return string
     */
    public function getInformation() {
        return $this->information;
    }

}