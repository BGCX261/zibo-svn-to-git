<?php

namespace joppa\controller\frontend;

use joppa\controller\JoppaWidget;

use joppa\model\Node;
use joppa\model\WidgetSettings;

use joppa\view\frontend\NodeView;

use joppa\Module;

use zibo\admin\view\FileView;

use zibo\core\Request;
use zibo\core\Response;

use zibo\library\html\Breadcrumbs;
use zibo\library\widget\controller\Widget;
use zibo\library\widget\WidgetDispatcher;

use zibo\ZiboException;

/**
 * Dispatcher for the frontend of a node
 */
class NodeDispatcher {

    /**
     * The node which is to be dispatched
     * @var joppa\model\Node
     */
    private $node;

    /**
     * The view of the node
     * @var joppa\view\frontend\NodeView
     */
    private $nodeView;

    /**
     * Breadcrumbs for the Joppa widgets
     * @var zibo\library\html\Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * Array with region name as key and a widget array as value. The widget array has the widget id as key and the widget instance as value.
     * @var array
     */
    private $regions;

    /**
     * Dispatcher for the widgets
     * @var zibo\library\widget\controller\WidgetDispatcher
     */
    private $widgetDispatcher;

    /**
     * Construct the dispatcher
     * @param joppa\model\Node $node
     * @param joppa\view\frontend\NodeView $nodeView
     * @return null
     */
    public function __construct(Node $node, NodeView $nodeView) {
        $this->node = $node;
        $this->nodeView = $nodeView;

        $this->widgetDispatcher = new WidgetDispatcher();

        $this->regions = $node->getTheme()->getRegions();
        foreach ($this->regions as $regionName => $region) {
            $this->regions[$regionName] = $node->getWidgets($regionName);
        }
    }

    /**
     * Get the node which is to be dispatched
     * @return joppa\model\Node
     */
    public function getNode() {
        return $this->node;
    }

    /**
     * Get the view of the node
     * @return joppa\view\frontend\NodeView
     */
    public function getNodeView() {
        return $this->nodeView;
    }

    /**
     * Set the breadcrumbs to set to the Joppa widgets while dispatching
     * @param zibo\library\html\Breadcrumbs $breadcrumbs
     * @return null
     */
    public function setBreadcrumbs(Breadcrumbs $breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Dispatch the node
     * @param zibo\core\Request $request
     * @param zibo\core\Response $response
     * @return array Array with the region name as key and a view array as value. The view array has the widget id as key and the dispatched widget view as value
     */
    public function dispatch(Request $request, Response $response) {
        $cache = Module::getCache();

        if (!$this->breadcrumbs) {
            $this->breadcrumbs = new Breadcrumbs();
        }

        $parameters = str_replace(Request::QUERY_SEPARATOR, '-', $request->getParametersAsString());

        $views = array();

        foreach ($this->regions as $regionName => $widgets) {
            foreach ($widgets as $widgetId => $widget) {
                $isJoppaWidget = $widget instanceof JoppaWidget;

                $cacheKey = $this->node->id . '#' . $regionName . '#' . $widgetId . '#' . $this->node->dataLocale . '#' . $parameters;

                if ($isJoppaWidget) {
                	if (!$_POST) {
		                $view = $cache->get(Module::CACHE_TYPE_NODE_WIDGET_VIEW, $cacheKey);
		                if ($view) {
		                    $views[$regionName][$widgetId] = $view;
		                    continue;
		                }
                	}

	                $widget->setBreadcrumbs($this->breadcrumbs);
	                $widget->setNode($this->node);
                }

                $this->dispatchWidget($request, $response, $widgetId, $widget);

                if ($response->willRedirect()) {
                    return;
                }

                $view = $response->getView();
                $response->setView(null);

                if ($view instanceof FileView) {
                    return $view;
                }

                if ($isJoppaWidget && $widget->isContent()) {
                	if ($request->isXmlHttpRequest()) {
                		return $view;
                	}

                    $views[$regionName] = array($widgetId => $view);
                    break;
                }

                if ($isJoppaWidget && $widget->isCacheable()) {
                    $cache->set(Module::CACHE_TYPE_NODE_WIDGET_VIEW, $cacheKey, $view);
                }

                $views[$regionName][$widgetId] = $view;
            }
        }

        return $views;
    }

    /**
     * Dispatch a widget
     * @param zibo\core\Request $request
     * @param zibo\core\Response $response
     * @param int $widgetId id of the widget
     * @param zibo\library\widget\controller\Widget $widget instance of the widget
     * @return null
     */
    private function dispatchWidget(Request $request, Response $response, $widgetId, Widget $widget) {
        $widgetSettings = new WidgetSettings($widgetId, $this->node->settings);

        $widget->setIdentifier($widgetId);
        $widget->setProperties($widgetSettings);
        $widget->setLocale($this->node->dataLocale);

        $this->widgetDispatcher->setWidget($widget);
        $this->widgetDispatcher->dispatch($request, $response);
    }

}