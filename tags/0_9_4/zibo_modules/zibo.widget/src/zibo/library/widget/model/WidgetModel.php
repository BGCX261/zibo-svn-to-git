<?php

namespace zibo\library\widget\model;

use zibo\core\Zibo;

use zibo\library\widget\controller\Widget;
use zibo\library\ObjectFactory;
use zibo\library\String;

use zibo\ZiboException;

/**
 * The model of the available widgets
 */
class WidgetModel {

    /**
     * Configuration key for the widget definition
     * @var string
     */
    const CONFIG_WIDGETS = 'widget';

    /**
     * Interface of a widget
     * @var string
     */
    const INTERFACE_WIDGET = 'zibo\\library\widget\\controller\\Widget';

    /**
     * Instance of the widget model
     * @var WidgetModel
     */
    private static $instance;

    /**
     * Multidimensional array with the available widgets.
     *
     * $widgets[namespace][name] = class name
     * @var array
     */
    private $widgets;

    /**
     * Object factory to create instances of the widgets
     * @var zibo\library\ObjectFactory
     */
    private $objectFactory;

    /**
     * Constructs a new widget model
     * @return null
     */
    private function __construct() {
        $this->objectFactory = new ObjectFactory();
        $this->readWidgets();
    }

    /**
     * Gets the instance of the widget model
     * @return WidgetModel
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets a new instance of a widget
     * @param string $namespace Namespace of the widget
     * @param string $name Name of the widget in it's namespace
     * @return zibo\library\widget\controller\Widget
     * @throws zibo\ZiboException when the name or the namespace are empty
     * @throws zibo\ZiboException when the widget is not installed
     */
    public function getWidget($namespace, $name) {
        if (String::isEmpty($namespace)) {
            throw new ZiboException('Namespace is empty');
        }
        if (String::isEmpty($name)) {
            throw new ZiboException('Name is empty');
        }


        if (!array_key_exists($namespace, $this->widgets)) {
            throw new ZiboException('There are no widgets installed of namespace ' . $namespace);
        } elseif (!array_key_exists($name, $this->widgets[$namespace])) {
            throw new ZiboException('Widget ' . $name . ' in namespace ' . $namespace . ' is not installed');
        }

        return $this->objectFactory->create($this->widgets[$namespace][$name], self::INTERFACE_WIDGET);
    }

    /**
     * Gets all the available widgets
     * @return array Array with the namespace as key and an array as value. The array as value has
     *               the name of the widget as key and an instance of the widget as value
     */
    public function getWidgets() {
        $widgets = array();

        foreach ($this->widgets as $namespace => $widgetClasses) {
            $widgets[$namespace] = array();

            foreach ($widgetClasses as $name => $widgetClass) {
                $widgets[$namespace][$name] = $this->objectFactory->create($widgetClass, self::INTERFACE_WIDGET);
            }
        }

        return $this->orderWidgets($widgets);
    }

    /**
     * Orders the widgets by namespace and their name
     * @param array $widgets Widget array
     * @return array Ordered widgets
     */
    private function orderWidgets(array $widgets) {
        ksort($widgets);

        foreach ($widgets as $namespace => $namespaceWidgets) {
            uasort($widgets[$namespace], array($this, 'widgetCompare'));
        }

        return $widgets;
    }

    /**
     * Compares the names 2 widgets
     * @return integer 0 when the widgets have the same name, -1 when $widgetA comes before $widgetB and 1 otherwise
     */
    public function widgetCompare(Widget $widgetA, Widget $widgetB) {
        $a = $widgetA->getName();
        $b = $widgetB->getName();

        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    /**
     * Reads the widgets from the Zibo configuration and creates an instance
     * @return null
     */
    private function readWidgets() {
        $this->widgets = array();

        $widgets = Zibo::getInstance()->getConfigValue(self::CONFIG_WIDGETS);
        foreach ($widgets as $namespace => $widgetClasses) {
            if (!array_key_exists($namespace, $this->widgets)) {
                $this->widgets[$namespace] = array();
            }

            if (!is_array($widgetClasses)) {
                throw new ZiboException('Invalid widget configuration for namespace ' . $namespace . '. A widget class is defined by the widget.<namespace>.<name> configuration key.');
            }

            foreach ($widgetClasses as $name => $widgetClass) {
                $this->widgets[$namespace][$name] = $widgetClass;
            }
        }
    }

}