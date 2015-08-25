<?php

namespace joppa\model;

use joppa\model\FolderModel;
use joppa\model\SiteModel;

use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;

use zibo\ZiboException;

/**
 * Manager of the node types
 */
class NodeTypeFacade {

    /**
     * Instance for the singleton pattern
     * @var NodeTypeFacade
     */
    private static $instance;

    /**
     * Array with NodeType objects as value and their name as key
     * @var array
     */
    private $nodeTypes;

    /**
     * Construct this manager
     * @return null
     */
    private function __construct() {
        $this->nodeTypes = array();

        $modelManager = ModelManager::getInstance();
        $this->registerNodeType(PageNodeType::NAME, new PageNodeType());
        $this->registerNodeType(FolderNodeType::NAME, new FolderNodeType());
        $this->registerNodeType(SiteModel::NODE_TYPE, $modelManager->getModel('Site'));
    }

    /**
     * Get the instance of this facade
     * @return NodeTypeFacade
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Checks whether a node type is registered
     * @param string $name name of the node type
     * @return boolean true if the node type is registered, false otherwise
     */
    public function hasNodeType($name) {
        if (array_key_exists($name, $this->nodeTypes)) {
            return true;
        }

        return false;
    }

    /**
     * Get the data of a node
     * @param string $name name of the node type
     * @param int $id id of the node
     * @param boolean $recursive true for a recursive lookup
     * @param string $locale code of the locale
     * @return mixed data of the node
     * @throws zibo\ZiboException when the node type is not registered
     */
    public function getNodeData($name, $id, $recursive = true, $locale = null) {
        $this->checkNodeType($name);

        return $this->nodeTypes[$name]->getNodeData($id, $recursive, $locale);
    }

    /**
     * Get the label of a node type
     * @param string $name Name of the node type
     * @param zibo\library\i18n\translation\Translator $translator Translator to use for the label
     * @return string
     * @throws zibo\ZiboException when the node type is not registered
     */
    public function getLabel($name, Translator $translator = null) {
        $this->checkNodeType($name);

        if (!$translator) {
            $translator = I18n::getInstance()->getTranslator();
        }

        return $this->nodeTypes[$name]->getLabel($translator);
    }

    /**
     * Get the labels of all the node types
     * @param zibo\library\i18n\translation\Translator $translator Translator to use for the labels
     * @return array with the name of the node type as key and the label as value
     */
    public function getLabels(Translator $translator = null) {
    	if (!$translator) {
            $translator = I18n::getInstance()->getTranslator();
    	}

        $labels = array();
        foreach ($this->nodeTypes as $name => $nodeType) {
            $labels[$name] = $nodeType->getLabel($translator);
        }

        return $labels;
    }

    /**
     * Check if a node type is available in the frontend
     * @param string $name name of the node type
     * @return boolean
     * @throws zibo\ZiboException when the node type is not registered
     */
    public function isAvailableInFrontend($name) {
        $this->checkNodeType($name);

        return $this->nodeTypes[$name]->isAvailableInFrontend();
    }

    /**
     * Get the default inherit value for a new node setting
     * @param string $name name of the node type
     * @return boolean
     * @throws zibo\ZiboException when the node type is not registered
     */
    public function getDefaultInherit($name) {
        $this->checkNodeType($name);

        return $this->nodeTypes[$name]->getDefaultInherit();
    }

    /**
     * Get the class name of the frontend controller of a node type
     * @param string $name name of the node type
     * @return string
     * @throws zibo\ZiboException when the node type is not registered
     */
    public function getFrontendController($name) {
        $this->checkNodeType($name);

        return $this->nodeTypes[$name]->getFrontendController();
    }

    /**
     * Get the class name of the backend controller of a node type
     * @param string $name name of the node type
     * @return string
     * @throws zibo\ZiboException when the node type is not registered
     */
    public function getBackendController($name) {
        $this->checkNodeType($name);

        return $this->nodeTypes[$name]->getBackendController();
    }

    /**
     * Register a node type
     * @param string $name name of the node type
     * @param NodeType $nodeType
     * @return null;
     */
    public function registerNodeType($name, NodeType $nodeType) {
        $this->nodeTypes[$name] = $nodeType;
    }

    /**
     * Unregister a node type
     * @param string $name name of the node type
     * @return null
     */
    public function unregisterNodeType($name) {
        if ($this->hasNodeType($name)) {
            unset($this->nodeTypes[$name]);
        }
    }

    /**
     * Check if a node type is registered
     * @param string $name name of the node type
     * @return null
     * @throws zibo\ZiboException when the node type is not registered
     */
    private function checkNodeType($name) {
        if (!$this->hasNodeType($name)) {
            throw new ZiboException('Node type ' . $name . ' is not registered');
        }
    }

}