<?php

namespace joppa\model;

use joppa\Module;

use zibo\admin\controller\LocalizeController;

use zibo\library\orm\ModelManager;
use zibo\library\widget\model\WidgetModel as ZiboWidgetModel;

use zibo\ZiboException;

/**
 * Main node object
 */
class Node {

    /**
     * Id of the node
     * @var int
     */
	public $id;

	/**
	 * Name of the node
	 * @var string
	 */
	public $name;

	/**
	 * Type of the node as registered in the NodeTypeFacade
	 * @var string
	 */
	public $type;

	/**
	 * Route in the frontend to the node
	 * @var string
	 */
	public $route;

	/**
	 * The settings of this node
	 * @var array|NodeSettings
	 */
	public $settings;

	/**
	 * Materialized path of the parent node
	 * @var string
	 */
	public $parent;

	/**
	 * Order index within the parent
	 * @var int
	 */
	public $orderIndex;

	/**
	 * Internal version number of this node
	 * @var int
	 */
	public $version;

	/**
	 * Code of the locale in which the current data is loaded
	 * @var string
	 */
	public $dataLocale;

	/**
	 * @var array
	 */
	public $dataLocales;

	/**
	 * Variable to attach the children of this node to
	 * @var array
	 */
	public $children;

	/**
	 * Id of the widget for which this node yielded as search result
	 * @var int
	 */
	public $widgetId;

	/**
	 * Theme of this node
	 * @var joppa\model\Theme
	 */
	private $theme;

	/**
	 * Get a string representation of the node
	 * @return string
	 */
	public function __toString() {
	    return $this->getPath() . ': ' . $this->name . ' (' . $this->type . ')';
	}

    /**
     * Get the route of this node. The route is used in the frontend as an url alias.
     * @return string
     * @throws zibo\ZiboException when the node has not been saved
     */
    public function getRoute() {
        if (!$this->id) {
            throw new ZiboException('This is a new node, a new node does not have a route');
        }

        if ($this->route && $this->dataLocale == LocalizeController::getLocale()) {
            return $this->route;
        }

        return Module::ROUTE_NODE . '/' . $this->id;
    }

    /**
     * Get the full path of the node. The path is used for the parent field of a node.
     * @return string
     */
    public function getPath() {
        if (!$this->parent) {
        	return $this->id;
        }

        return $this->parent . NodeModel::PATH_SEPARATOR . $this->id;
    }

    /**
     * Get the node id of the root of this node
     * @return integer
     */
    public function getRootNodeId() {
        if (!$this->parent) {
        	return $this->id;
        }

        $tokens = explode(NodeModel::PATH_SEPARATOR, $this->parent);
        return array_shift($tokens);
    }

    /**
     * Gets the root node of this node
     * @return Node
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    public function getRootNode() {
    	$this->checkSettings();

    	$root = $this;
    	do {
    		$node = $root->getParentNode();
    		if (!$node) {
    			return $root;
    		}

    		$root = $node;
    	} while ($root != null);
    }

    /**
     * Get the node id of the parent
     * @return integer
     */
    public function getParentNodeId() {
    	if (!$this->parent) {
    		return null;
    	}

        $ids = explode(NodeModel::PATH_SEPARATOR, $this->parent);
        return array_pop($ids);
    }

    /**
     * Gets the parent node of this node
     * @return Node
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    public function getParentNode() {
    	$this->checkSettings();

    	$inheritedNodeSettings = $this->settings->getInheritedNodeSettings();

    	if (!$inheritedNodeSettings) {
    		return null;
    	}

    	return $inheritedNodeSettings->getNode();
    }

    /**
     * Checks if the provided node is a parent node of this node
     * @param Node $node The node to check as a parent
     * @return boolean True if the provided node is a parent, false otherwise
     */
    public function hasParentNode(Node $node) {
    	$ids = explode(NodeModel::PATH_SEPARATOR, $this->parent);
    	return in_array($node->id, $ids);
    }

    /**
     * Gets the level of this node
     * @return integer
     */
    public function getLevel() {
    	if (!$this->parent) {
    		return 0;
    	}

    	return substr_count($this->parent, NodeModel::PATH_SEPARATOR) + 1;
    }

	/**
	 * Check whether this node is published
	 * @return boolean true if this node is published, false if not
	 * @throws zibo\ZiboException when the NodeSettings are not set to this node
	 */
	public function isPublished() {
		$this->checkSettings();

		return $this->settings->isPublished();
	}

    /**
     * Check whether this node is secured in any way
     * @return boolean true if this node is secured, false if not
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
	public function isSecured() {
		$this->checkSettings();

		return $this->settings->isSecured();
	}

	/**
	 * Checks whether this node is allowed for the current user
     * @return boolean true if this node is allowed for the current user, false if not
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
	 */
	public function isAllowed() {
        $this->checkSettings();

        return $this->settings->isAllowed();
	}

	/**
	 * Checks whether this node is allowed in the current locale
	 * @return boolean true if the node is allowed for the current locale, false otherwise
	 * @throws zibo\ZiboException when the NodeSettings are not set to this node
	 */
	public function isAvailableInLocale() {
		$this->checkSettings();

		return $this->settings->isAvailableInLocale($this->dataLocale);
	}

	/**
	 * Get the theme of this node
	 * @return Theme
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
	 */
	public function getTheme() {
	    if ($this->theme) {
	        return $this->theme;
	    }

    	$this->checkSettings();

		$theme = $this->settings->get(NodeSettingModel::SETTING_THEME);
		$this->theme = new Theme($theme);

		return $this->theme;
	}

	/**
	 * Gets a widget from this node
	 * @param integer $widgetId
	 * @return zibo\library\widget\controller\Widget
	 */
	public function getWidget($widgetId) {
        $widgetIdModel = ModelManager::getInstance()->getModel(WidgetModel::NAME);
        $widgetObjectModel = ZiboWidgetModel::getInstance();

        $widget = $widgetIdModel->findById($widgetId);
        if (!$widget) {
            throw new ZiboException('No widget found for id ' . $widgetId);
        }

        return $widgetObjectModel->getWidget($widget->namespace, $widget->name);
	}

	/**
     * Get the widgets for a region
     * @param string $region name of the region
     * @return array Array with zibo\library\widget\controller\Widget objects
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     * @throws zibo\ZiboException when a widget could not be found
	 */
    public function getWidgets($region) {
        $cache = Module::getCache();
        $cacheKey = $this->id . '-' . $region;

        $widgets = $cache->get(Module::CACHE_TYPE_NODE_WIDGETS, $cacheKey);
        if ($widgets !== null) {
            return $widgets;
        }

    	$this->checkSettings();

        $widgets = array();

        $widgetString = $this->settings->get(NodeSettingModel::SETTING_WIDGETS . '.' . $region);
        if (!$widgetString) {
            $cache->set(Module::CACHE_TYPE_NODE_WIDGETS, $cacheKey, $widgets);
        	return $widgets;
        }

        $widgetIdModel = ModelManager::getInstance()->getModel(WidgetModel::NAME);
        $widgetObjectModel = ZiboWidgetModel::getInstance();

        $widgetIds = explode(NodeSettingModel::WIDGETS_SEPARATOR, $widgetString);
        foreach ($widgetIds as $widgetId) {
            $widgetId = trim($widgetId);

            $widget = $widgetIdModel->findById($widgetId);
            if (!$widget) {
            	throw new ZiboException('No widget found for id ' . $widgetId);
            }

            $widget = $widgetObjectModel->getWidget($widget->namespace, $widget->name);
			$widgets[$widgetId] = $widget;
        }

        $cache->set(Module::CACHE_TYPE_NODE_WIDGETS, $cacheKey, $widgets);

        return $widgets;
    }

    /**
     * Add a widget to a region
     * @param string $region name of the region
     * @param string $namespace namespace of the widget
     * @param string $name name of the widget
     * @return int id of the new widget
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    public function addWidget($region, $namespace, $name) {
    	$this->checkSettings();

    	$widgetModel = ModelManager::getInstance()->getModel(WidgetModel::NAME);
        $widget = $widgetModel->addWidget($namespace, $name);

        $nodeWidgets = $this->settings->get(NodeSettingModel::SETTING_WIDGETS . '.' . $region);
        if (!$nodeWidgets) {
            $nodeWidgets = $widget->id;
        } else {
            $nodeWidgets .= NodeSettingModel::WIDGETS_SEPARATOR . $widget->id;
        }

        $this->settings->set(NodeSettingModel::SETTING_WIDGETS . '.' . $region, $nodeWidgets);

        return $widget->id;
    }

    /**
     * Delete a widget from a region
     * @param string $region name of the region
     * @param int $id id of the widget to delete
     * @return null
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     * @throws zibo\ZiboException when a widget could not be found
     */
    public function deleteWidget($region, $id) {
    	$this->checkSettings();

        $widgetsKey = NodeSettingModel::SETTING_WIDGETS . '.' . $region;
        $widgetsValue = $this->settings->get($widgetsKey);
        $widgetIds = explode(',', $widgetsValue);
        $widgetsValue = '';

        $foundWidget = false;
        foreach ($widgetIds as $widgetId) {
            if ($id == $widgetId) {
            	$foundWidget = true;
                continue;
            }
            $widgetsValue .= ($widgetsValue ? NodeSettingModel::WIDGETS_SEPARATOR : '') . $widgetId;
        }

        if (!$foundWidget) {
        	throw new ZiboException('Could not find the widget with id ' . $id);
        }

        $this->settings = new WidgetSettings($id, $this->settings);
        $this->settings->clearWidgetProperties();
        $this->settings->set($widgetsKey, $widgetsValue);
    }

    /**
     * Order the widgets of a region
     * @param string $region name of the region
     * @param string|array $widgets array with widget ids or a string with widget ids separated by a comma.
     * @return null
     * @throws zibo\ZiboException when the $widgets variable does not contain the same widgets as the current widgets
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    public function orderWidgets($region, $widgets) {
        $this->checkSettings();

        if (!is_array($widgets)) {
            $widgets = explode(',', $widgets);
        }

        $widgetsKey = NodeSettingModel::SETTING_WIDGETS . '.' . $region;
        $currentWidgets = explode(NodeSettingModel::WIDGETS_SEPARATOR, $this->settings->get($widgetsKey, '', false));

        $widgetsValue = '';
        foreach ($widgets as $widgetId) {
            $widgetId = trim($widgetId);

            $key = array_search($widgetId, $currentWidgets);
            if ($key === false) {
                throw new ZiboException('Widget ' . $widgetId . ' is not currently set to region ' . $region);
            }

            $widgetsValue .= ($widgetsValue ? NodeSettingModel::WIDGETS_SEPARATOR : '') . $widgetId;

            unset($currentWidgets[$key]);
        }

        $numCurrentWidgets = count($currentWidgets);
        if ($numCurrentWidgets) {
            if ($numCurrentWidgets > 1) {
                throw new ZiboException('Widgets ' . implode(NodeSettingModel::WIDGETS_SEPARATOR, $currentWidgets) . ' are not found in the new widget order');
            }
            $widget = array_pop($currentWidgets);
            throw new ZiboException('Widget ' . $widget . ' is not found in the new widget order');
        }

        $this->settings->set($widgetsKey, $widgetsValue);
    }

    /**
     * Check if the settings are set to this node
     * @return null
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    private function checkSettings() {
        if (!$this->settings || !($this->settings instanceof NodeSettings)) {
            throw new ZiboException('No NodeSettings set to node ' . $this->id);
        }
    }

}