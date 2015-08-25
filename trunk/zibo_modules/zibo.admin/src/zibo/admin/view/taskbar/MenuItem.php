<?php

namespace zibo\admin\view\taskbar;

use zibo\library\html\AbstractElement;
use zibo\library\String;

/**
 * Data container for a menu item
 */
class MenuItem extends AbstractElement {

    /**
     * Label of the menu item
     * @var string
     */
    private $label;

    /**
     * Route of the action behind the menu item
     * @var string
     */
    private $route;

    /**
     * The base url, needed for the getHtml method
     * @var string
     */
    private $baseUrl;

    /**
     * Construct a new menu item
     * @param string $label
     * @param string $route
     * @return null
     */
    public function __construct($label, $route) {
        $this->setLabel($label);
        $this->setRoute($route);
    }

    /**
     * Set the base url, needed for the getHtml method
     * @param string $baseUrl
     * @return null
     */
    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get the html for this menu item
     * @return string
     */
    public function getHtml() {
        if (String::looksLikeUrl($this->route)) {
            $url = $this->route;
        } else {
            $url = $this->baseUrl . $this->route;
        }

        return
            '<li' . $this->getAttributesHtml() . '>' .
                '<a href="' . $url . '">' . $this->label . '</a>' .
            '</li>';
    }

    /**
     * Set the label of this menu item
     * @param string $label
     * @return null
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Get the label of this menu item
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Set the route of this menu item
     * @param string $route
     * @return null
     */
    public function setRoute($route) {
        $this->route = $route;
    }

    /**
     * Get the route of this menu item
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

}