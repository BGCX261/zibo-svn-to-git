<?php

namespace joppa\model;

use joppa\Module;
use zibo\core\Zibo;
use zibo\library\orm\model\SimpleModel;

use zibo\library\String;

use \Exception;

/**
 * Model to manage the settings of a node
 */
class NodeSettingModel extends SimpleModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'NodeSetting';

    /**
     * Permission constant to allow everybody to a node
     * @var string
     */
	const AUTHENTICATION_STATUS_EVERYBODY = 'everybody';

	/**
     * Permission constant to allow only anonymous users to a node
     * @var string
     */
	const AUTHENTICATION_STATUS_ANONYMOUS = 'anonymous';

	/**
     * Permission constant to allow only authenticated users to a node
     * @var string
     */
	const AUTHENTICATION_STATUS_AUTHENTICATED = 'authenticated';

	/**
	 * Locales value for all locales
	 * @var string
	 */
	const LOCALES_ALL = 'all';

	/**
	 * Separator between the locales value
	 * @var string
	 */
	const LOCALES_SEPARATOR = ',';

	/**
	 * Zibo configuration key for the default value of the publish flag
	 * @var string
	 */
	const CONFIG_PUBLISH_DEFAULT = 'joppa.publish.default';

	/**
	 * Date format for a date setting
	 * @var string
	 */
    const DATE_FORMAT = 'joppa';

    /**
     * Default value for the publish flag
     * @var boolean
     */
    const DEFAULT_PUBLISH = 1;

    /**
     * Setting key for the locales
     * @var string
     */
    const SETTING_LOCALES = 'locales';

    /**
     * Setting key for the description meta
     * @var string
     */
    const SETTING_META_DESCRIPTION = 'meta.description';

    /**
     * Setting key for the kêywords meta
     * @var string
     */
    const SETTING_META_KEYWORDS = 'meta.keywords';

    /**
     * Setting key for the permissions
     * @var string
     */
    const SETTING_PERMISSIONS = 'permissions';

    /**
     * Setting key for the publish flag
     * @var string
     */
    const SETTING_PUBLISH = 'published';

    /**
     * Setting key for the publish start date
     * @var string
     */
    const SETTING_PUBLISH_START = 'publish.start';

    /**
     * Setting key for the publish stop date
     * @var string
     */
    const SETTING_PUBLISH_STOP = 'publish.stop';

    /**
     * Setting key for the theme
     * @var string
     */
    const SETTING_THEME = 'theme';

    /**
     * Base setting key for widget properties
     * @var string
     */
    const SETTING_WIDGET = 'widget';

    /**
     * Setting key for the widgets
     * @var string
     */
    const SETTING_WIDGETS = 'widgets';

    /**
     * Separator between the widget ids in the widgets setting
     * @var string
     */
    const WIDGETS_SEPARATOR = ',';

    /**
     * Save a NodeSettings object to the model
     * @param NodeSettings $nodeSettings
     * @param boolean $setInheritToAll set to true if all settings should be inherit to lower levels
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when not all settings are valid
     * @throws Exception when the save fails
     */
    public function setNodeSettings(NodeSettings $nodeSettings, $setInheritToAll = false) {
        $nodeModel = $this->getModel(NodeModel::NAME);

        $node = $nodeSettings->getNode();

        $validator = new NodeSettingsValidator();
        $validator->validateNodeSettings($nodeSettings);

        $settings = $nodeSettings->getArray();
        foreach ($settings as $key => $nodeSetting) {
            if (String::isEmpty($nodeSetting->value)) {
                unset($settings[$key]);
                continue;
            }

            $nodeSetting->id = 0;
            $nodeSetting->node = $node->id;

            if ($setInheritToAll) {
                $nodeSetting->inherit = true;
            }
        }

        $transactionStarted = $this->startTransaction();
        try {
            $this->delete($this->findByNodeId($node->id));

            $this->save($settings);

            $this->commitTransaction($transactionStarted);
        } catch (Exception $e) {
            $this->rollbackTransaction($transactionStarted);
            throw $e;
        }
    }

    /**
     * Get a NodeSettings object for a node
     * @param int|Node $node
     * @return NodeSettings
     */
    public function getNodeSettings($node) {
        if (is_numeric($node)) {
            $nodeId = $node;
            $node = null;
        } else {
            $nodeId = $node->id;
        }

    	$cache = Module::getCache();
    	$nodeSettings = $cache->get(Module::CACHE_TYPE_NODE_SETTINGS, $nodeId);
    	if ($nodeSettings !== null) {
    		return $nodeSettings;
    	}

    	if (!$node) {
            $nodeModel = $this->getModel(NodeModel::NAME);
            $node = $nodeModel->getNode($nodeId, 0);
    	}

        $inheritedSettings = null;
        if ($node->parent) {
            $inheritedSettings = $this->getNodeSettings($node->getParentNodeId());
        }

        $nodeSettings = new NodeSettings($node, $inheritedSettings);

        $settings = $this->findByNodeId($nodeId);
        foreach ($settings as $setting) {
            $nodeSettings->setNodeSetting($setting);
        }

        $cache->set(Module::CACHE_TYPE_NODE_SETTINGS, $nodeId, $nodeSettings);

        return $nodeSettings;
    }

    /**
     * Get node settings for a node
     * @param int $nodeId id of the node
     * @param boolean $recursive
     * @return array Array with NodeSetting objects as value and it's id as key
     */
	private function findByNodeId($nodeId) {
	    $query = $this->createQuery(0);
        $query->addCondition('{node} = %1%', $nodeId);
		return $query->query();
	}

	/**
     * Get the nodes which contain the provided widget in one of it's regions
     * @param int $widgetId
     * @return array Array with node id as key and a Node instance as value
	 */
    public function getNodesForWidgetId($widgetId) {
    	$query = $this->createQuery(1);
    	$query->setDistinct(true);
    	$query->setFields('{node}');
    	$query->addCondition('{key} LIKE %1%', 'widgets.%');
    	$query->addCondition('{value} = %1% OR {value} LIKE "%1%,%" OR {value} LIKE "%,%1%,%" OR {value} LIKE "%,%1%"', $widgetId);
    	$nodeSettings = $query->query();

    	$nodes = array();
    	foreach ($nodeSettings as $nodeSetting) {
    		$nodes[$nodeSetting->node->id] = $nodeSetting->node;
    	}

    	return $nodes;
    }

    /**
     * Get all the node settings for all the children of the provided node (recursively).
     * @param Node $node
     * @return array Array with NodeSetting objects
     */
    public function getAllNodeSettingsForNode(Node $node, $condition = null, $order = null, array $variables = null) {
    	$path = $node->getPath();

    	$query = $this->createQuery(0);
    	$query->addCondition('{node.id} = %1% OR {node.parent} = %2% OR {node.parent} LIKE %3%', $node->id, $path, $path . NodeModel::PATH_SEPARATOR . '%');
    	if ($condition) {
            $query->addConditionWithVariables($condition, $variables);
    	}
    	if ($order) {
    		$query->addOrderByWithVariables($order, $variables);
    	}

    	return $query->query();
    }

}