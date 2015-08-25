<?php

namespace zibo\dashboard\view;

use zibo\admin\view\BaseView;

use zibo\dashboard\model\Dashboard;
use zibo\dashboard\Module;

use zibo\jquery\Module as JQueryModule;

/**
 * Main dashboard view
 */
class DashboardView extends BaseView {

    private $dashboard;
    private $propertiesAction;

    public function __construct(Dashboard $dashboard, $orderAction, $minimizeMaximizeAction, $closeAction, $propertiesAction = null, $addAction = null) {
        $this->dashboard = $dashboard;
        $this->propertiesAction = $propertiesAction;

        parent::__construct('dashboard/dashboard');

        $this->set('addAction', $addAction);

        $this->addJavascript(JQueryModule::SCRIPT_JQUERY_UI);
        $this->addJavascript(Module::SCRIPT_DASHBOARD);
        $this->addInlineJavascript('dashboardCloseAction = "' . $closeAction . '";');
        $this->addInlineJavascript('dashboardMinimizeMaximizeAction = "' . $minimizeMaximizeAction . '";');
        $this->addInlineJavascript('dashboardOrderAction = "' . $orderAction . '";');
        $this->addInlineJavascript('dashboardInitialize();');

        $this->addStyle(JQueryModule::STYLE_JQUERY_UI);
        $this->addStyle(Module::STYLE_DASHBOARD);
    }

    public function render($return = true) {
        $views = $this->dashboard->getDispatchedViews();
        if (!$views) {
            $this->set('columns', array());
            return parent::render();
        }

        $columns = array();
        foreach ($views as $columnNumber => $widgetViews) {
            $columns[$columnNumber] = array();
            foreach ($widgetViews as $widgetId => $widgetView) {
                if (!$widgetView) {
                    continue;
                }

                $widget = $this->dashboard->getWidget($widgetId);
                $title = $widget->getTitle();
                $content = $this->renderView($widgetView);

                $propertiesAction = null;
                if ($this->propertiesAction && $widget->hasProperties()) {
                    $propertiesAction = $this->propertiesAction . $widgetId;
                }

                $widgetView = new WidgetView($widgetId, $title, $content, $propertiesAction, $widget->isMinimized());
                $columns[$columnNumber][$widgetId] = $this->renderView($widgetView);
            }
        }
        $this->set('columns', $columns);

        return parent::render($return);
    }

}