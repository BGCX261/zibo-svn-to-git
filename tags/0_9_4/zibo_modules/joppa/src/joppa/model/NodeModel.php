<?php

namespace joppa\model;

use joppa\Module;

use zibo\admin\controller\LocalizeController;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\cache\Cache;
use zibo\library\database\manipulation\condition\Condition;
use zibo\library\database\manipulation\condition\NestedCondition;
use zibo\library\database\manipulation\condition\SimpleCondition;
use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\MathematicalExpression;
use zibo\library\database\manipulation\expression\ScalarExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\statement\UpdateStatement;
use zibo\library\html\Breadcrumbs;
use zibo\library\orm\query\ModelQuery;
use zibo\library\orm\model\SimpleModel;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;
use zibo\library\String;
use zibo\library\Structure;

use zibo\ZiboException;

use \Exception;

/**
 * Model to manage the nodes
 */
class NodeModel extends SimpleModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'Node';

    /**
     * Separator for the node path
     * @var string
     */
    const PATH_SEPARATOR = '-';

    /**
     * Code for the route validation error
     */
    const VALIDATION_ROUTE_ERROR_CODE = 'joppa.error.route.exists';

    /**
     * Default message for the route validation error
     */
    const VALIDATION_ROUTE_ERROR_MESSAGE = "Route '%route%' is already used by node %node%";

    /**
     * Array with the id mapping of a copied node tree
     * @var array
     */
    protected $copyTable = array();

    /**
     * Create a new node data object
     * @param string $type of the node
     * @param Node $parent initial parent of the node
     * @return Node
     */
    public function createNode($type, Node $parent = null) {
        $node = $this->createData();
        $node->type = $type;

        if ($parent) {
            $node->parent = $parent->getPath();
            $node->settings = new NodeSettings($node, $parent->settings);
        } else {
            $node->settings = new NodeSettings($node);
        }

        return $node;
    }

    /**
     * Get a node by it's id
     * @param int $id id of the node
     * @param integer $recursiveDepth set to false to skip the loading of the node settings
     * @param string $locale code of the locale
     * @return Node
     */
	public function getNode($id, $recursiveDepth = 1, $locale = null, $includeUnlocalized = null) {
		if ($locale === null) {
			$locale = LocalizeController::getLocale();
		}
		if ($includeUnlocalized === null) {
			$includeUnlocalized = ModelQuery::INCLUDE_UNLOCALIZED_FETCH;
		}

		$locale = $this->getLocale($locale);

	    $query = $this->createQuery($recursiveDepth, $locale, $includeUnlocalized);
	    $query->addCondition('{id} = %1%', $id);
	    $query->removeFields('{settings}');
	    $node = $query->queryFirst();

		if (!$node) {
			throw new ZiboException('Could not find node with id ' . $id);
		}

		if ($recursiveDepth !== 0) {
			$nodeSettingModel = $this->getModel(NodeSettingModel::NAME);
			$node->settings = $nodeSettingModel->getNodeSettings($node);
		}

		return $node;
	}

    /**
     * Get the node which matches the array routes
     *
     * If recieve the query home/info1/action1/parameter, the routes array should be like:
     * <ul>
     * <li>home/info1/action1/parameter</li>
     * <li>home/info1/action1</li>
     * <li>home/info1</li>
     * <li>home</li>
     * </ul>
     *
     * @param array $routes Array with routes to match.
     * @return Node
     */
    public function getNodeByRoutes(array $routes, $urlQuery, $site, $locale = null) {
        if (empty($routes) || (count($routes) == 1 && empty($routes[0]))) {
            $routes = null;
        }

        if ($locale === null) {
        	$locale = LocalizeController::getLocale();
        }

        $node = null;

        if ($routes) {
	        $localizedModel = $this->meta->getLocalizedModel();
	        $query = $localizedModel->createQuery(0);
	        $query->setFields('{dataId}, {dataLocale}, {route}');
	        $query->addCondition('{dataId.parent} = %1% OR {dataId.parent} LIKE %2%', $site->node, $site->node . self::PATH_SEPARATOR . '%');

	        $condition = '';
	        foreach ($routes as $index => $route) {
	        	$condition .= ($condition ? ' OR ' : '') . '{route} = %' . $index . '%';
	        }
            $query->addConditionWithVariables($condition, $routes);

	        $query->addOrderBy('{route} DESC');

	        $localizedNodes = $query->query();

	        if ($localizedNodes) {
		        $route = null;
		        foreach ($localizedNodes as $localizedNode) {
		        	if (!$route) {
		        		$route = $localizedNode->route;
		        	} elseif ($localizedNode->route != $route) {
		        		break;
		        	}

			        $node = $this->createData(false);
			        $node->id = $localizedNode->dataId;
			        $node->route = $localizedNode->route;
			        $node->dataLocale = $localizedNode->dataLocale;

			        if ($node->dataLocale === $locale) {
			        	break;
			        }
		        }

		        $node = $this->getNode($node->id, 1, $node->dataLocale);
	        } else {
	        	$tokens = explode(Request::QUERY_SEPARATOR, $urlQuery);
	        	if (!($tokens[0] == 'node' && array_key_exists(1, $tokens) && is_numeric($tokens[1]))) {
	        		return null;
	        	}

	        	$node = $this->getNode($tokens[1], 1, $locale);
                $node->dataLocale = $locale;
	        }
        } else {
        	$node = $this->getNode($site->defaultNode, 1, $locale);
        }

        if (!$node) {
        	return null;
        }

        $nodeTypeFacade = NodeTypeFacade::getInstance();
        if (!$nodeTypeFacade->isAvailableInFrontend($node->type) || !$node->settings->isAvailableInLocale($node->dataLocale) || !$node->settings->isPublished()) {
        	return null;
        }

        return $node;
    }

    /**
     * Get the nodes where a specified widget is set. Results are cached in the Joppa cache.
     * @param string $namespace namespace of the widget
     * @param string $name name of the widget
     * @param int $limit limit the result
     * @return Node|array if $limit is 1, a Node object will be returned, else an array with Node objects
     */
    public function getNodesForWidget($namespace, $name, $limit = null) {
        $cache = Module::getCache();
        $cacheKey = $namespace . '-' . $name . '-' . $limit;

        $nodes = $cache->get(Module::CACHE_TYPE_WIDGET_NODES, $cacheKey);
        if ($nodes !== null) {
            return $nodes;
        }

        $widgetModel = $this->getModel(WidgetModel::NAME);
        $nodeSettingModel = $this->getModel(NodeSettingModel::NAME);

        $nodes = array();

        $widgetIds = $widgetModel->getWidgetIds($namespace, $name);
        foreach ($widgetIds as $widgetId) {
            $widgetNodes = $nodeSettingModel->getNodesForWidgetId($widgetId);

            foreach ($widgetNodes as $widgetNode) {
                if (isset($nodes[$widgetNode->id])) {
                    continue;
                }

                $widgetNode->settings = $nodeSettingModel->getNodeSettings($widgetNode);
                $widgetNode->widgetId = $widgetId;
                $widgetNode->widgetProperties = new WidgetSettings($widgetId, $widgetNode->settings);

                if ($limit == 1) {
                    $nodes = $widgetNode;
                    break 2;
                }

                $nodes[$widgetNode->id] = $widgetNode;
            }
        }

        $cache->set(Module::CACHE_TYPE_WIDGET_NODES, $cacheKey, $nodes);

        return $nodes;
    }

    /**
     * Get all the routes of the nodes
     * @return array Array with the route as value
     *
     * @todo Get all the routes for the different locales
     */
    public function getNodeRoutes() {
    	$localizedModel = $this->meta->getLocalizedModel();

        $query = $localizedModel->createQuery(0);
        $query->setDistinct(true);
        $query->setFields('{route}');
        $query->addCondition('{route} IS NOT NULL AND {route} <> %1%', '');
        $nodes = $query->query();

        $routes = array();
        foreach ($nodes as $node) {
            $routes[$node->route] = $node->route;
        }

        return $routes;
    }

    /**
     * Get the root node of a node
     * @param int|Node $node id of the node or an instance of a Node
     * @param integer $recursiveDepth
     * @param string $locale code of the locale
     * @return Node
     */
    public function getRootNode($node, $recursiveDepth = 1, $locale = null) {
    	if ($locale === null) {
    		$locale = LocalizeController::getLocale();
    	}

        if (is_numeric($node)) {
            $query = $this->createQuery(0);
            $query->setFields('{id}, {parent}');
            $query->addCondition('{id} = %1%', $node);
            $node = $query->queryFirst();

            if (!$node) {
                throw new ZiboException('Could not find node id ' . $id);
            }
        }

        $rootNodeId = $node->getRootNodeId();

        return $this->getNode($rootNodeId, $recursiveDepth, $locale);
    }

    /**
     * Create an array with the node hierarchy. Usefull for an options field.
     * @param array $tree array with Node objects
     * @param string $prefix prefix for the node names
     * @return array Array with the node id as key and the node name as value
     */
    public function createListFromNodeTree(array $tree, $separator = '/', $prefix = '') {
        $list = array();

        foreach ($tree as $node) {
            $newPrefix = $prefix . $separator . $node->name;

            $list[$node->id] = $newPrefix;

            if ($node->children) {
                $children = $this->createListFromNodeTree($node->children, $separator, $newPrefix);
                $list = Structure::merge($list, $children);
            }
        }

        return $list;
    }

    /**
     * Get an array with the nodes and specify the number of levels for fetching the children of the nodes.
     * @param int|Node $parent the parent node
     * @param string|array $excludes id's of nodes which are not to be included in the result
     * @param int $maxDepth maximum number of nested levels will be looked for
     * @param string $locale Locale code
     * @param boolean $loadSettings set to true to load the NodeSettings object of the node
     * @param boolean $isFrontend Set to true to get only the nodes available in the frontend*
     * @return array Array with the node id as key and the node as value
     */
    public function getNodeTree($parent, $excludes = null, $maxDepth = null, $locale = null, $includeUnlocalized = null, $loadSettings = false, $isFrontend = false) {
        if ($excludes) {
            if (!is_array($excludes)) {
                $excludes = array($excludes);
            }
        } else {
        	$excludes = array();
        }

        $cache = Module::getCache();
        $cacheKey = md5('p' . $parent . 'd' . $maxDepth . 's' . $loadSettings . 'e' . implode(',', $excludes) . 'l' . $locale . 'i' . ($includeUnlocalized === null ? 'n' : $includeUnlocalized) . 'f' . $isFrontend);

        $tree = $cache->get(Module::CACHE_TYPE_NODE_TREE, $cacheKey);
        if ($tree) {
            return $tree;
        }

    	$nodeSettingsModel = null;
    	if ($loadSettings) {
    		$nodeSettingsModel = $this->getModel(NodeSettingModel::NAME);
    	}

    	if (is_numeric($parent)) {
    	   $parent = $this->getNode($parent, 0, $locale);
    	}

        $tree = $this->getNodes($parent, $excludes, $maxDepth, $locale, $includeUnlocalized, $nodeSettingsModel, $isFrontend);

        $cache->set(Module::CACHE_TYPE_NODE_TREE, $cacheKey, $tree);

        return $tree;
    }

    /**
     * Get the nodes with their nested children for a parent node
     * @param Node $parent
     * @param string|array $excludes
     * @param int $maxDepth
     * @param NodeSettingModel $nodeSettingModel pass this model to load the NodeSettings object for the nodes
     * @param boolean $isFrontend Set to true to get only the nodes available in the frontend
     * @return array Array with the node id as key and the Node object with nested children as value
     */
    private function getNodes(Node $parent, $excludes = null, $maxDepth = null, $locale = null, $includeUnlocalized = null, NodeSettingModel $nodeSettingsModel = null, $isFrontend = false) {
        if ($locale === null) {
            $locale = LocalizeController::getLocale();
        }
        if ($includeUnlocalized === null) {
            $includeUnlocalized = ModelQuery::INCLUDE_UNLOCALIZED_FETCH;
        }

        $path = $parent->getPath();

        $query = $this->createQuery(0, $locale, $includeUnlocalized);
		$query->addCondition('{parent} = %1% OR {parent} LIKE %2%', $path, $path . self::PATH_SEPARATOR . '%');

		if ($maxDepth !== null) {
			$maxDepth = $parent->getLevel() + $maxDepth;

		    $query->addCondition('(LENGTH({parent}) - LENGTH(REPLACE({parent}, %1%, %2%))) <= %3%', self::PATH_SEPARATOR, '', $maxDepth);
		}

		if ($excludes) {
        	if (!is_array($excludes)) {
        		$excludes = array($excludes);
        	}

            $query->addCondition('{id} NOT IN (%1%)', implode(', ', $excludes));
        }

        $query->addOrderBy('{parent} ASC, {orderIndex} ASC');
        $nodes = $query->query();

        if ($isFrontend) {
        	$nodeTypeFacade = NodeTypeFacade::getInstance();
        }

        // create an array by path
        $nodesByParent = array();
        foreach ($nodes as $node) {
        	if ($isFrontend && !$nodeTypeFacade->isAvailableInFrontend($node->type)) {
        		continue;
        	}

            if (!array_key_exists($node->parent, $nodesByParent)) {
                $nodesByParent[$node->parent] = array();
            }

            $nodesByParent[$node->parent][$node->id] = $node;
        }

        // link the nested nodes
        $nodes = array();
        foreach ($nodesByParent as $nodePath => $pathNodes) {
            if ($nodePath == $path) {
                $nodes = $pathNodes;
            }

            foreach ($pathNodes as $pathNode) {
                if ($nodeSettingsModel) {
                    $pathNode->settings = $nodeSettingsModel->getNodeSettings($pathNode);
                    $pathNode->settings->isPublished();
                }

                $pathNodePath = $pathNode->getPath();
                if (!array_key_exists($pathNodePath, $nodesByParent)) {
                    continue;
                }

                $pathNode->children = $nodesByParent[$pathNodePath];
            }
        }

        return $nodes;
    }

    /**
     * Gets the number of children levels for the provided node
     * @param Node $node
     * @return integer
     */
    public function getChildrenLevelsForNode(Node $node) {
    	$path = $node->getPath();

    	$query = $this->createQuery(0);
    	$query->setFields('MAX(LENGTH({parent}) - LENGTH(REPLACE({parent}, %1%, %2%))) + 1 AS levels', self::PATH_SEPARATOR, '');
    	$query->addCondition('{parent} LIKE %1%', $path . '%');

    	$result = $query->queryFirst();

    	return $result->levels - $node->getLevel();
    }

    /**
     * Get the breadcrumbs of a node
     * @param joppa\model\Node $node
     * @param string $baseUrl base url for the node routes
     * @return zibo\library\html\Breadcrumbs
     */
    public function getBreadcrumbsForNode(Node $node, $baseUrl) {
        $nodeTypeFacade = NodeTypeFacade::getInstance();

        $urls = array(
            $baseUrl . Request::QUERY_SEPARATOR . $node->getRoute() => $node->name,
        );

        $parent = $node->getParentNode();
        do {
            if ($nodeTypeFacade->isAvailableInFrontend($parent->type)) {
            	$url = $baseUrl . Request::QUERY_SEPARATOR . $parent->getRoute();
            	$urls[$url] = $parent->name;
            }

	        $parent = $parent->getParentNode();
        } while ($parent);

        $urls = array_reverse($urls, true);

        $breadcrumbs = new Breadcrumbs();
        foreach ($urls as $url => $name) {
        	$breadcrumbs->addBreadcrumb($url, $name);
        }

        return $breadcrumbs;
    }

    /**
     * Copies a node
     * @param integer|Node $node Id of the node or the node to copy
     * @param boolean $recursive Set to true to also copy the children of the node
     * @param boolean $reorder Set to false to just copy the order index instead of adding the copied node after the source node
     * @param boolean $keepOriginalName Set to true to keep the name untouched, else a suffix like (copy) or (copy 2, 3 ...) will be added to the name of the copy
     * @param boolean $copyRoutes Set to true to copy the routes of the nodes. This will only work when copying a root node, else a validation error will occur
     * @param boolean $resetCopyTable Set to false for recursive copying
     * @param boolean $newParent Provide a new parent for the copy, needed for recursive copying
     * @return null
     */
    public function copy($node, $recursive = true, $reorder = true, $keepOriginalName = false, $copyRoutes = false, $resetCopyTable = true, $newParent = null) {
    	$id = $this->getPrimaryKey($node);
    	$node = $this->getNode($id, 1);

    	if ($resetCopyTable) {
    		$this->copyTable = array();
    	}

    	$copy = $this->createData();

    	$nameSuffix = '';
    	if (!$keepOriginalName) {
        	$nameSuffix = $this->getNameSuffixForCopiedNode($node->parent, $node->name, $node->dataLocale);
    	}

        $copy->name = $node->name . $nameSuffix;
    	$copy->type = $node->type;
    	if ($newParent) {
    		$copy->parent = $newParent;
    	} else {
            $copy->parent = $node->parent;
    	}
    	if ($reorder) {
    		$copy->orderIndex = $node->orderIndex + 1;
    	} else {
    		$copy->orderIndex = $node->orderIndex;
    	}
    	if ($copyRoutes) {
    		$copy->route = $node->route;
    	}

    	$isTransactionStarted = $this->startTransaction();
    	try {
	    	$this->copyNodeSettings($node, $copy);

	    	$this->save($copy);

	    	$localizedModel = $this->meta->getLocalizedModel();
	    	$query = $localizedModel->createQuery();
	    	$query->addCondition('{dataId} = %1%', $node->id);
	    	$localizedNodes = $query->query();

	    	foreach ($localizedNodes as $localizedNode) {
	    		$nameSuffix = '';
	    		if (!$keepOriginalName) {
	    			$nameSuffix = $this->getNameSuffixForCopiedNode($node->parent, $localizedNode->name, $localizedNode->dataLocale);
	    		}

	    		$copyLocalizedNode = $this->createData(false);
	    		$copyLocalizedNode->id = $copy->id;
	    		$copyLocalizedNode->name = $localizedNode->name . $nameSuffix;
	    		if ($copyRoutes) {
                    $copyLocalizedNode->route = $localizedNode->route;
	    		}
	    		$copyLocalizedNode->dataLocale = $localizedNode->dataLocale;

	    		$this->save($copyLocalizedNode);
	    	}


	    	if ($reorder) {
	    		$orderFieldExpression = new FieldExpression('orderIndex');

	    		$mathExpression = new MathematicalExpression();
	    		$mathExpression->addExpression($orderFieldExpression);
	    		$mathExpression->addExpression(new ScalarExpression(1));

	    		$idCondition = new SimpleCondition(new FieldExpression('id'), new ScalarExpression($copy->id), Condition::OPERATOR_NOT_EQUALS);
	    		$parentCondition = new SimpleCondition(new FieldExpression('parent'), new ScalarExpression($copy->parent), Condition::OPERATOR_EQUALS);
	    		$greaterCondition = new SimpleCondition($orderFieldExpression, new ScalarExpression($copy->orderIndex), Condition::OPERATOR_GREATER_OR_EQUALS);
	    		$condition = new NestedCondition();
	    		$condition->addCondition($idCondition);
	    		$condition->addCondition($parentCondition);
	    		$condition->addCondition($greaterCondition);

	    		$updateStatement = new UpdateStatement();
	    		$updateStatement->addTable(new TableExpression($this->getName()));
	    		$updateStatement->addValue($orderFieldExpression, $mathExpression);
	    		$updateStatement->addCondition($condition);

	    		$this->executeStatement($updateStatement);
	    	}

	    	if ($recursive) {
	    		$query = $this->createQuery();
	    		$query->setFields('{id}, {parent}, {orderIndex}');
	    		$query->addCondition('{parent} = \'' . $node->getPath() . '\''); // when copying a site, the path is numeric and won't be escaped by the orm
	    		$query->addOrderBy('{orderIndex} ASC');
	    		$children = $query->query();

                $path = $copy->getPath();

	    		foreach ($children as $child) {
	    			$childCopy = $this->copy($child->id, true, false, true, $copyRoutes, false, $path);
	    		}
	    	}

	    	$this->copyTable[$id] = $copy->id;

	    	$this->commitTransaction($isTransactionStarted);
    	} catch (Exception $exception) {
    		$this->rollbackTransaction($isTransactionStarted);
    		throw $exception;
    	}

    	return $copy;
    }

    /**
     * Gets an array with the mapping of copied nodes
     * @return array Array with the original id as key and the copy id as value
     */
    public function getCopyTable() {
    	return $this->copyTable;
    }

    /**
     * Gets the suffix for the name of a copied node. eg (copy) or (copy 2)
     * @param string $parent
     * @param string $name
     * @param string $locale
     * @return string
     */
    private function getNameSuffixForCopiedNode($parent, $name, $locale) {
    	$index = 1;
    	$suffix = ' (copy)';

    	$baseQuery = $this->createQuery(0, $locale, false);
    	$baseQuery->addCondition('{parent} = %1%', $parent);

    	$query = clone($baseQuery);
    	$query->addCondition('{name} = %1%', $name . $suffix);

    	if (!$query->count()) {
    		return $suffix;
    	}

    	do {
            $index++;
    		$suffix = ' (copy ' . $index . ')';

	    	$query = clone($baseQuery);
            $query->addCondition('{name} = %1%', $name . $suffix);
    	} while ($query->count());

    	return $suffix;
    }

    /**
     * Copies the node's settings to the copy
     * @param Node $source Source node
     * @param Node $destination Destination node
     * @return null
     */
    private function copyNodeSettings($source, $destination) {
    	$nodeSettingModel = $this->getModel(NodeSettingModel::NAME);
    	$widgetModel = $this->getModel(WidgetModel::NAME);

    	$widgetSettingPrefixLength = strlen(NodeSettingModel::SETTING_WIDGET) + 1;
    	$sourceSettings = $source->settings->getArray();
    	$sourceInheritedSettings = $source->settings->getInheritedNodeSettings();
    	$destination->settings = array();
    	$widgetIds = array();

    	foreach ($sourceSettings as $index => $sourceSetting) {
    		if (strpos($sourceSetting->key, NodeSettingModel::SETTING_WIDGETS) !== 0) {
    			continue;
    		}

    		$newValue = '';
            $inheritedWidgetIds = array();

            if ($sourceInheritedSettings) {
	    		$inheritedValue = $sourceInheritedSettings->get($sourceSetting->key);
	    		if ($inheritedValue) {
	                $inheritedWidgetIds = explode(NodeSettingModel::WIDGETS_SEPARATOR, $inheritedValue);
	    		}
            }

    		$sourceWidgetIds = explode(NodeSettingModel::WIDGETS_SEPARATOR, $sourceSetting->value);
    		foreach ($sourceWidgetIds as $widgetId) {
    			if (in_array($widgetId, $inheritedWidgetIds)) {
    				$widgetIds[$widgetId] = $widgetId;
	    			$newValue .= ($newValue ? NodeSettingModel::WIDGETS_SEPARATOR : '') . $widgetId;
	    			continue;
    			}

    			$widget = $widgetModel->findById($widgetId, 0);
    			if (!$widget) {
    				continue;
    			}

    			$widgetCopy = $widgetModel->addWidget($widget->namespace, $widget->name);
    			$widgetIds[$widgetId] = $widgetCopy->id;

    			$newValue .= ($newValue ? NodeSettingModel::WIDGETS_SEPARATOR : '') . $widgetCopy->id;
    		}

    		$destinationSetting = $nodeSettingModel->createData();
    		$destinationSetting->key = $sourceSetting->key;
    		$destinationSetting->value = $newValue;
    		$destinationSetting->inherit = $sourceSetting->inherit;

    		$destination->settings[] = $destinationSetting;

    		unset($sourceSettings[$index]);
    	}

    	foreach ($sourceSettings as $index => $sourceSetting) {
            $destinationSetting = $nodeSettingModel->createData();
            $destinationSetting->key = $sourceSetting->key;
            $destinationSetting->value = $sourceSetting->value;
            $destinationSetting->inherit = $sourceSetting->inherit;

            if (strpos($sourceSetting->key, NodeSettingModel::SETTING_WIDGET) === 0) {
            	$settingKey = substr($sourceSetting->key, $widgetSettingPrefixLength);
            	$positionSettingSeparator = strpos($settingKey, '.');
            	if ($positionSettingSeparator === false) {
            		continue;
            	}

            	$widgetId = substr($settingKey, 0, $positionSettingSeparator);
                $settingKey = substr($settingKey, $positionSettingSeparator);

                if (!array_key_exists($widgetId, $widgetIds)) {
                	continue;
                }

                $destinationSetting->key = NodeSettingModel::SETTING_WIDGET . '.' . $widgetIds[$widgetId] . $settingKey;
            }

            $destination->settings[] = $destinationSetting;
    	}
    }

    /**
     * Reorder the nodes of a site
     * @param integer $parent Id of the parent node
     * @param array $nodeOrder Array with the node id as key and the number of children as value
     * @return null
     */
    public function orderNodes($parent, array $nodeOrder) {
        $query = $this->createQuery(0);
        $query->setFields('{id}, {parent}');
        $query->addCondition('{id} = %1%', $parent);
        $parent = $query->queryFirst();
        if (!$parent) {
            throw new ZiboException('Could not find node id ' . $id);
        }

        $path = $parent->getPath();
        $orderIndex = 1;
        $child = 0;

        $paths = array();
        $orderIndexes = array();
        $children = array();

        $query = $this->createQuery(0);
        $query->setFields('{id}, {parent}, {orderIndex}');
        $query->addCondition('{parent} = %1% OR {parent} LIKE %2%', $path, $path . self::PATH_SEPARATOR . '%');
        $nodes = $query->query();

        $transactionStarted = $this->startTransaction();
        try {
            foreach ($nodeOrder as $nodeId => $numChildren) {
            	if (!array_key_exists($nodeId, $nodes)) {
            		throw new ZiboException('Node with id ' . $nodeId . ' is not a child of node ' . $parent->id);
            	}

            	$nodes[$nodeId]->parent = $path;
            	$nodes[$nodeId]->orderIndex = $orderIndex;
            	$this->save($nodes[$nodeId]);

            	$orderIndex++;

                if ($child) {
                    $child--;

                    if (!$child) {
                        $orderIndex = array_pop($orderIndexes);
                        $path = array_pop($paths);
                        $child = array_pop($children);
                    }
                }

            	if ($numChildren) {
            		array_push($orderIndexes, $orderIndex);
            		array_push($paths, $path);
            		array_push($children, $child);

            		$orderIndex = 1;
            		$path = $nodes[$nodeId]->getPath();
            		$child = $numChildren;
            	}

            	unset($nodes[$nodeId]);
            }

            if ($nodes) {
            	throw new ZiboException('Not all nodes of the provided parent are provided in the node order array: missing nodes ' . implode(', ', array_keys($nodes)));
            }

            $this->commitTransaction($transactionStarted);
        } catch (Exception $exception) {
        	$this->rollbackTransaction($transactionStarted);
        	throw $exception;
        }
    }

    /**
     * Validate a node object
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when the data is invalid
     *
     * @todo validate the route (unique)
     */
    public function validate($data) {
    	if (!isset($data->route) || empty($data->route)) {
    	   return parent::validate($data);
    	}

        $route = rtrim(ltrim($data->route, Request::QUERY_SEPARATOR), Request::QUERY_SEPARATOR);

        $tokens = explode(Request::QUERY_SEPARATOR, $route);
        foreach ($tokens as $index => $token) {
            $token = String::safeString($token);

            if (empty($token)) {
                unset($tokens[$index]);
            } else {
                $tokens[$index] = $token;
            }
        }

        $data->route = implode(Request::QUERY_SEPARATOR, $tokens);

        try {
            parent::validate($data);
            $exception = new ValidationException();
        } catch (ValidationException $e) {
        	$exception = $e;
        }

        $query = $this->createQuery(0, $data->dataLocale, false);
        $query->setFields('{id}');
        $query->addCondition('{parent} LIKE %1% AND {route} = %2% AND {id} <> %3%', $data->getRootNodeId() . self::PATH_SEPARATOR . '%', $data->route, $data->id);
        $node = $query->queryFirst();

        if ($node) {
        	$error = new ValidationError(self::VALIDATION_ROUTE_ERROR_CODE, self::VALIDATION_ROUTE_ERROR_MESSAGE, array('route' => $data->route, 'node' => $node->id));
            $exception->addErrors('route', array($error));
        }

        if ($exception->hasErrors()) {
        	throw $exception;
        }
    }

    /**
     * Save a node to the model
     * @param Node $node
     * @return null
     */
    protected function saveData($node) {
        if (isset($node->settings) && $node->settings instanceof NodeSettings) {
            $settings = $node->settings;

            unset($node->settings);
        }

        if (!$node->id) {
        	if (!$node->orderIndex) {
                $node->orderIndex = $this->getNewOrderIndex($node->parent);
        	}
        } else {
            $oldRoute = $this->getRouteForNode($node->id, $node->dataLocale);
            if ($oldRoute && $node->route != $oldRoute) {
            	$expiredRouteModel = $this->getModel(ExpiredRouteModel::NAME);
            	$expiredRouteModel->addExpiredRoute($oldRoute, $node->id, $node->dataLocale);
            }
        }

        parent::saveData($node);

        if (isset($settings)) {
            $node->settings = $settings;

            $nodeSettingModel = $this->getModel(NodeSettingModel::NAME);
            $nodeSettingModel->setNodeSettings($node->settings);
        }
    }

    /**
     * Gets the current route for a node
     * @param integer $nodeId Id of the node
     * @param string $locale
     * @return string The current route of the provided node in the provided locale
     */
    private function getRouteForNode($nodeId, $locale) {
    	$localizedModel = $this->meta->getLocalizedModel();

    	$query = $localizedModel->createQuery(0);
    	$query->setFields('{route}');
    	$query->addCondition('{dataId} = %1% AND {dataLocale} = %2%', $nodeId, $locale);
    	$node = $query->queryFirst();

    	if ($node) {
    		return $node->route;
    	}

    	return null;
    }

    /**
     * Get a order index for a new node
     * @param string $parent path of the parent of the new node
     * @return int new order index
     */
    private function getNewOrderIndex($parent) {
        $query = $this->createQuery(0);
        $query->setFields('MAX({orderIndex}) AS maxOrderIndex');
        $query->addCondition('{parent} = %1%', $parent);

        $data = $query->queryFirst();

        return $data->maxOrderIndex + 1;
    }

    /**
     * Deletes the data from the database
     * @param Node $data
     * @return Node
     */
    protected function deleteData($data) {
    	$data = parent::deleteData($data);

    	if (!$data) {
    		return $data;
    	}

        $path = $data->getPath();

        $query = $this->createQuery(0, null, true);
        $query->setFields('{id}');
        $query->addCondition('{parent} = %1% OR {parent} LIKE %2%', $path, $path . self::PATH_SEPARATOR . '%');
    	$children = $query->query();

    	$this->delete($children);

    	return $data;
    }

}