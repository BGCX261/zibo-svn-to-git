<?php

namespace joppa\controller\backend\action;

use joppa\form\backend\RegionSelectForm;

use joppa\model\NodeSettingModel;
use joppa\model\NodeTypeFacade;
use joppa\model\SiteModel;
use joppa\model\Theme;
use joppa\model\WidgetSettings;

use joppa\view\backend\NodeContentView;
use joppa\view\backend\WidgetContentView;
use joppa\view\backend\WidgetPropertiesView;

use zibo\admin\controller\LocalizeController;

use zibo\core\Dispatcher;
use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\widget\controller\Widget;
use zibo\library\widget\model\WidgetModel;
use zibo\library\widget\WidgetDispatcher;

use zibo\ZiboException;

/**
 * Controller to manage the content of a node
 */
class NodeContentAction extends AbstractNodeAction {

    /**
     * Route of this action
     * @var string
     */
    const ROUTE = 'content';

    /**
     * Prefix of the session key for the current region
     * @var string
     */
    const SESSION_REGION = 'joppa.region.';

    /**
     * Prefix of the session key for the last used region
     * @var string
     */
    const SESSION_REGION_LAST = 'joppa.region.last';

    /**
     * Translation key of the label
     * @var string
     */
    const TRANSLATION_LABEL = 'joppa.button.content';

    /**
     * Translation key of the widget delete confirmation message
     * @var string
     */
    const TRANSLATION_WIDGET_DELETE_CONFIRM = 'joppa.label.widget.delete';

    /**
     * Name of the current region
     * @var string
     */
    private $region;

    /**
     * Name of the last used region
     * @var string
     */
    private $lastRegion;

    /**
     * Construct this node action
     * @return null
     */
    public function __construct() {
        parent::__construct(self::ROUTE, self::TRANSLATION_LABEL, true);
    }

    /**
     * Sets the current region to this controller
     * @return null
     */
    public function preAction() {
        parent::preAction();

        $this->region = $this->session->get(self::SESSION_REGION . $this->node->id);
        $this->lastRegion = $this->session->get(self::SESSION_REGION_LAST);
    }

    /**
     * Stores the current region to the session
     * @return null
     */
    public function postAction() {
        if ($this->region) {
            $this->session->set(self::SESSION_REGION . $this->node->id, $this->region);
            $this->session->set(self::SESSION_REGION_LAST, $this->region);
        } else {
            $this->session->set(self::SESSION_REGION . $this->node->id, 0);
            $this->session->set(self::SESSION_REGION_LAST, 0);
        }

        parent::postAction();
    }

    /**
     * Handle region selection and show an overview of the content of the selected region
     * @return null
     */
	public function indexAction() {
	    $url = $this->request->getBasePath();
	    $theme = $this->node->getTheme();

        if ($theme->hasRegion($this->lastRegion)) {
            $this->region = $this->lastRegion;
        }

        $regionSelectForm = new RegionSelectForm($url, $theme, $this->region);
        if ($regionSelectForm->isSubmitted()) {
            $this->region = $regionSelectForm->getRegion();
            $this->response->setRedirect($url);
            return;
        }

        $regions = $theme->getRegions();
        if (!$this->region && count($regions) == 1) {
            $region = each($regions);
            $this->region = $region['key'];
            $this->response->setRedirect($url);
            return;
        }

        $availableWidgets = WidgetModel::getInstance()->getWidgets();

        if ($this->region) {
            $zibo = Zibo::getInstance();
	        $regionWidgets = $this->node->getWidgets($this->region);
	        foreach ($regionWidgets as $widgetId => $widget) {
	            $widget->setProperties(new WidgetSettings($widgetId, $this->node->settings));
	            $widget->setLocale(LocalizeController::getLocale());
                $zibo->runEvent(Dispatcher::EVENT_PRE_DISPATCH, $widget, 'propertiesView', array());
	        }
        } else {
        	$regionWidgets = array();
        }

        $translator = $this->getTranslator();
        $widgetDeleteMessage = $translator->translate(self::TRANSLATION_WIDGET_DELETE_CONFIRM);

        $view = new NodeContentView($this->getSiteSelectForm(), $regionSelectForm, $this->site, $this->node, $this->region, $availableWidgets, $regionWidgets, $widgetDeleteMessage);
        $this->response->setView($view);
	}

	/**
     * Action to add a widget to the current region
     * @param string $namespace namespace of the widget
     * @param string $name name of the widget
     * @return null
     * @throws zibo\ZiboException when no region is selected
	 */
	public function addAction($namespace, $name) {
	    $this->checkRegion();

	    $widgetId = $this->node->addWidget($this->region, $namespace, $name);

	    $this->models['NodeSetting']->setNodeSettings($this->node->settings);

	    $this->clearCache();

	    $widget = $this->node->getWidget($widgetId);
        $widget->setProperties(new WidgetSettings($widgetId, $this->node->settings));
        $widget->setLocale(LocalizeController::getLocale());
        Zibo::getInstance()->runEvent(Dispatcher::EVENT_PRE_DISPATCH, $widget, 'propertiesView', array());

        $view = new WidgetContentView($widget, $widgetId, $this->request->getBasePath() . '/');
        $this->response->setView($view);
	}

	/**
     * Action to remove a widget from the current region
     * @param string $id id of the widget
     * @return null
     * @throws zibo\ZiboException when no region is selected
	 */
    public function deleteAction($id) {
        $this->checkRegion();

        $this->node->deleteWidget($this->region, $id);

        $this->models['NodeSetting']->setNodeSettings($this->node->settings);

        $this->clearCache();

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Action to reorder the widgets of the current region
     * @param string $widgetsValue widget ids separated by a comma
     * @return null
     * @throws zibo\ZiboException when no region is selected
     * @throws zibo\ZiboException when the $widgetsValue is invalid
     */
    public function orderAction($widgetsValue) {
        $this->checkRegion();

        $widgetsValue = rtrim(trim($widgetsValue),',');
        $this->node->orderWidgets($this->region, $widgetsValue);

        $this->models['NodeSetting']->setNodeSettings($this->node->settings);

        $this->clearCache();
    }

    /**
     * Action to dispatch to the properties of a widget
     * @param string $id id of the widget
     * @return null
     */
	public function propertiesAction($id) {
	    $widgetSettings = new WidgetSettings($id, $this->node->settings);

	    $widget = $this->models['Widget']->getWidget($id);
        $widget->setProperties($widgetSettings);
        $widget->setLocale(LocalizeController::getLocale());

        $baseUrl = $this->request->getBasePath();
        $basePath = $baseUrl . '/properties/' . $id;

        $controller = get_class($widget);
        $action = Widget::METHOD_PROPERTIES;

        $parameters = func_get_args();
        array_shift($parameters);

        $request = new Request($baseUrl, $basePath, $controller, $action, $parameters);

        $widgetDispatcher = new WidgetDispatcher();
        $widgetDispatcher->setWidget($widget);
	    if ($widgetDispatcher->dispatch($request, $this->response, false)) {
            $this->models['NodeSetting']->setNodeSettings($widgetSettings);
            $this->clearCache();
        }
        $propertiesView = $this->response->getView();

        $view = new WidgetPropertiesView($this->getSiteSelectForm(), $this->site, $this->node, $widget, $propertiesView);
        $this->response->setView($view);
	}

	/**
	 * Checks whether a region is selected
	 * @return null
	 * @throws zibo\ZiboException when no region is selected
	 */
	private function checkRegion() {
        if (!$this->region) {
            throw new ZiboException('No region selected');
        }
	}

}