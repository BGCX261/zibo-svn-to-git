<?php

namespace zibo\dashboard\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\Request;

use zibo\dashboard\model\Dashboard;
use zibo\dashboard\model\DashboardModel;
use zibo\dashboard\model\DashboardWidget;
use zibo\dashboard\view\DashboardView;
use zibo\dashboard\view\StaticView;
use zibo\dashboard\view\WidgetAddView;
use zibo\dashboard\view\WidgetPropertiesView;

use zibo\library\i18n\I18n;
use zibo\library\widget\controller\Widget;
use zibo\library\widget\model\WidgetModel;
use zibo\library\widget\WidgetDispatcher;

/**
 * Controller for the dashboard
 */
class DashboardController extends AbstractController {

    /**
     * The model of the dashboards
     * @var zibo\dashboard\model\DashboardModel
     */
    private $dashboardModel;

    /**
     * The current dashboard
     * @var zibo\dashboard\model\Dashboard
     */
    private $dashboard;

    /**
     * Constructs a new dashboard controller
     * @return null
     */
    public function __construct() {
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Initializes the dashboard
     * @return null
     */
    public function preAction() {
        $dashboardName = $this->getDashboardName();

        $this->dashboard = $this->dashboardModel->getDashboard($dashboardName);

        if ($this->dashboard) {
            return;
        }

        $this->dashboard = new Dashboard($dashboardName);
    }

    /**
     * Updates the current dashboard to the model
     * @return null
     */
    public function postAction() {
        $this->dashboardModel->setDashboard($this->dashboard);
    }

    /**
     * Action to show the dashboard
     * @return null
     */
    public function indexAction() {
        $baseUrl = $this->request->getBaseUrl();
        $basePath = $this->request->getBasePath();
        $parameters = $this->request->getParameters();
        $request = new Request($baseUrl, $basePath, null, '*', $parameters);

        $this->dashboard->dispatch($request, $this->response);

        $views = $this->dashboard->getDispatchedViews();

        if ($views instanceof View) {
            $view = $views;
        } else {
            $closeAction = $basePath . '/close/';
            $orderAction = $basePath . '/order/';
            $propertiesAction = $basePath . '/properties/';
            $addAction = $basePath . '/add';
            $minimizeMaximizeAction = $basePath . '/minimizeMaximize/';

            $view = new DashboardView($this->dashboard, $orderAction, $minimizeMaximizeAction, $closeAction, $propertiesAction, $addAction);
        }

        $this->response->setView($view);
    }

    /**
     * Action to show the add widget view
     * @return null
     */
    public function addAction() {
        $baseAction = $this->request->getBasePath();
        $saveAction = $baseAction . '/put/';

        $widgetModel = WidgetModel::getInstance();
        $widgets = $widgetModel->getWidgets();

        $view = new WidgetAddView($widgets, $baseAction, $saveAction);
        $this->response->setView($view);
    }

    /**
     * Action to edit the properties of a widget
     * @param string $widgetId Id of the widget
     * @return null
     */
    public function propertiesAction($widgetId) {
        $locale = I18n::getInstance()->getLocale();
        $locale = $locale->getCode();

        $widgetModel = WidgetModel::getInstance();
        $widget = $this->dashboard->getWidget($widgetId);
        $instance = $widgetModel->getWidget($widget->getNamespace(), $widget->getName());
        $instance->setProperties($widget->getWidgetProperties());
        $instance->setLocale($locale);

        $baseUrl = $this->request->getBasePath();
        $basePath = $this->request->getBasePath() . '/properties/' . $widgetId;
        $controller = get_class($instance);
        $action = Widget::METHOD_PROPERTIES;
        $parameters = array_slice(func_get_args(), 2);
        $request = new Request($baseUrl, $basePath, $controller, $action, $parameters);

        $widgetDispatcher = new WidgetDispatcher();
        $widgetDispatcher->setWidget($instance);
        $widgetDispatcher->dispatch($request, $this->response, false);

        if ($this->response->willRedirect()) {
            $this->response->setView(null);
            return;
        }

        $propertiesView = $this->response->getView();
        $view = new WidgetPropertiesView($instance->getName(), $propertiesView);

        $this->response->setView($view);
    }

    /**
     * Action to put a new instance of a widget on the dashboard
     * @param string $namespace Namespace of the widget
     * @param string $name Name of the widget
     * @return null
     */
    public function putAction($namespace, $name) {
        $widget = new DashboardWidget($namespace, $name);

        $this->dashboard->addWidget($widget);

        $this->response->setView(new StaticView('OK'));
    }

    /**
     * Action to toggle the window state of a widget
     * @param string $widgetId Id of the widget in the dashboard
     * @return null
     */
    public function minimizeMaximizeAction($widgetId) {
        $this->dashboard->toggleWidgetWindowState($widgetId);
    }

    /**
     * Action to close (remove) a widget
     * @param string $widgetId Id of the widget in the dashboard
     * @return null
     */
    public function closeAction($widgetId) {
        $this->dashboard->removeWidget($widgetId);
    }

    /**
     * Action to store a new widget order
     * @return null
     */
    public function orderAction() {
        if (!$_POST) {
            $this->reponse->setRedirect($this->request->getBasePath());
            return;
        }

        $order = array();

        $columns = explode(';', $_POST['order']);
        foreach ($columns as $column) {
            if (empty($column)) {
                continue;
            }

            list($column, $widgets) = explode(':', $column);

            $columnNumber = str_replace('column', '', $column);

            $order[$columnNumber] = array();

            $widgets = explode(',', $widgets);
            foreach ($widgets as $index => $widget) {
                $widgetId = trim(str_replace('widget', '', $widget));
                if (!$widgetId) {
                    continue;
                }

                $order[$columnNumber][] = $widgetId;
            }
        }

        $this->dashboard->setWidgetOrder($order);
    }

    /**
     * Action to reset the dashboard to a new empty one
     * @return null
     */
    public function resetAction() {
        $this->dashboardModel->removeDashboard($this->dashboard);
        $this->dashboard = null;

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Gets the name of the dashboard for the current user
     * @return string
     */
    private function getDashboardName() {
        $user = $this->getUser();
        if ($user) {
            return $user->getUserId();
        }

        $session = $this->getSession();

        return $session->getId();
    }

}