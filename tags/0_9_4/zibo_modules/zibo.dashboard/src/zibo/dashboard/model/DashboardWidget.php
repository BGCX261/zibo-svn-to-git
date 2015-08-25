<?php

namespace zibo\dashboard\model;

use zibo\library\widget\model\GenericWidgetProperties;

/**
 * Data container of a widget on the dashboard
 */
class DashboardWidget {

    /**
     * State for a minimized view
     * @var string
     */
    const MINIMIZED = 'minimized';

    /**
     * State for a maximized view
     * @var string
     */
    const MAXIMIZED = 'maximized';

    /**
     * Namespace of the widget
     * @var string
     */
    private $namespace;

    /**
     * Name of the widget in it's namespace
     * @var string
     */
    private $name;

    /**
     * Title for the window of the widget
     * @var string
     */
    private $title;

    /**
     * Flag to see if the widget has properties
     * @var boolean
     */
    private $hasProperties;

    /**
     * The properties of the widget
     * @var zibo\library\widget\model\WidgetProperties
     */
    private $properties;

    /**
     * The window state of the widget
     * @var string
     */
    private $windowState;

    /**
     * Constructs a new dashboard widget
     * @param string $namespace Namespace of the widget
     * @param string $name Name of the widget
     * @return null
     */
    public function __construct($namespace, $name) {
        $this->setNamespace($namespace);
        $this->setName($name);

        $this->properties = new GenericWidgetProperties();

        $this->windowState = self::MAXIMIZED;
    }

    /**
     * Sets the namespace of the Widget instance
     * @param string $namespace
     * @return null
     */
    private function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    /**
     * Gets the namespace of the Widget instance
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Sets the name of the Widget instance
     * @param string $name
     * @return null
     */
    private function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the name of the Widget instance
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the title of the widget box
     * @param string $title
     * @return null
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Gets the title of the widget box
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    public function setHasProperties($flag) {
        $this->hasProperties = $flag;
    }

    public function hasProperties() {
        return $this->hasProperties;
    }

    /**
     * Checks if the widget box is minimized
     * @return boolean True if the widget box is minimized, false otherwise
     */
    public function isMinimized() {
        return $this->windowState == self::MINIMIZED;
    }

    /**
     * Checks if the widget box is maximized
     * @return boolean True if the widget box is maximized, false otherwise
     */
    public function isMaximized() {
        return $this->windowState == self::MAXIMIZED;
    }

    /**
     * Toggles the state of the window box
     * @return null
     */
    public function toggleWindowState() {
        if ($this->windowState == self::MINIMIZED) {
            $this->windowState = self::MAXIMIZED;
        } else {
            $this->windowState = self::MINIMIZED;
        }
    }

    /**
     * Gets the properties of the widget
     * @return zibo\library\widget\model\WidgetProperties
     */
    public function getWidgetProperties() {
        return $this->properties;
    }

}