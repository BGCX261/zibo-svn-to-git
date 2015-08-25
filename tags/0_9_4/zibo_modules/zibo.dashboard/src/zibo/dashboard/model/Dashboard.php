<?php

namespace zibo\dashboard\model;

use zibo\core\Request;
use zibo\core\Response;

use zibo\admin\view\FileView;

use zibo\library\i18n\I18n;
use zibo\library\widget\model\WidgetModel;
use zibo\library\widget\WidgetDispatcher;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Data container of a dashboard
 */
class Dashboard {

    /**
     * Default column
     * @var integer
     */
    const DEFAULT_COLUMN = 1;

    /**
     * Name of the dashboard
     * @var string
     */
    private $name;

    /**
     * Array with the widgetId as key and a DashboardWidget as value
     * @var array
     */
    private $widgets;

    /**
     * Array with the widget order per column
     * @var array
     */
    private $order;

    /**
     *
     * @var unknown_type
     */
    private $dispatchedViews;

    /**
     * Constructs a new dashboard
     * @param string $name Name for the dashboard
     * @param integer $columns Number of columns
     * @return null
     */
    public function __construct($name, $columns = 3) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        $this->name = $name;
        $this->widgets = array();
        $this->order = array();
        for ($i = 1; $i <= $columns; $i++) {
            $this->order[$i] = array();
        }
    }

    /**
     * Gets the fields to serialize
     * @return array
     */
    public function __sleep() {
        return array('name', 'widgets', 'order');
    }

    /**
     * Gets the name of the dashboard
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function getWidget($widgetId) {
        if (isset($this->widgets[$widgetId])) {
            return $this->widgets[$widgetId];
        }

        throw new ZiboException('Could not find widget with id ' . $widgetId);
    }

    public function addWidget(DashboardWidget $widget) {
        $widgetId = $this->getNewWidgetId();

        $this->widgets[$widgetId] = $widget;
        $this->order[self::DEFAULT_COLUMN][] = $widgetId;

        return $widgetId;
    }

    public function removeWidget($widgetId) {
        if (!isset($this->widgets[$widgetId])) {
            throw new ZiboException('Could not find widget with id ' . $widgetId);
        }

        unset($this->widgets[$widgetId]);

        foreach ($this->order as $columnNumber => $column) {
            foreach ($column as $index => $id) {
                if ($id === $widgetId) {
                    unset($this->order[$columnNumber][$index]);
                    break 2;
                }
            }
        }
    }

    public function toggleWidgetWindowState($widgetId) {
        $widget = $this->getWidget($widgetId);
        $widget->toggleWindowState();
    }

    public function setWidgetOrder(array $order) {
        $this->order = $order;
    }

    public function getDispatchedViews() {
        return $this->dispatchedViews;
    }

    public function dispatch(Request $request, Response $response) {
        $dispatcher = new WidgetDispatcher();
        $this->dispatchedViews = $this->dispatchColumns($request, $response, $dispatcher);
    }

    private function dispatchColumns(Request $request, Response $response, WidgetDispatcher $dispatcher) {
        $locale = I18n::getInstance()->getLocale();
        $locale = $locale->getCode();

        $widgetModel = WidgetModel::getInstance();
        $views = array();

        foreach ($this->order as $columnNumber => $widgetIds) {
            $views[$columnNumber] = array();
            foreach ($widgetIds as $widgetId) {
                $widget = $this->getWidget($widgetId);

                $instance = $widgetModel->getWidget($widget->getNamespace(), $widget->getName());
                $instance->setIdentifier($widgetId);
                $instance->setProperties($widget->getWidgetProperties());
                $instance->setLocale($locale);

                $widget->setTitle($instance->getName());
                $widget->setHasProperties($instance->hasProperties());

                $dispatcher->setWidget($instance);
                $dispatcher->dispatch($request, $response);

                if ($response->willRedirect()) {
                    break 2;
                }

                $view = $response->getView();
                $response->setView(null);
                if ($view instanceof FileView) {
                    return $view;
                }
                $views[$columnNumber][$widgetId] = $view;
            }
        }

        return $views;
    }

    /**
     * Gets a new widget id
     * @return integer
     */
    private function getNewWidgetId() {
        $newId = 1;

        foreach ($this->widgets as $widgetId => $widget) {
            if ($widgetId >= $newId) {
                $newId = $widgetId + 1;
            }
        }

        return $newId;
    }

}