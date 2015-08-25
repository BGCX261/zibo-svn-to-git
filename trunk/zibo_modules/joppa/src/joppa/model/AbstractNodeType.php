<?php

namespace joppa\model;

use joppa\Module;

use zibo\library\i18n\translation\Translator;

/**
 * Abstract node type
 */
class AbstractNodeType implements NodeType {

    /**
     * Name of the node type
     * @var string
     */
    protected $name;

    /**
     * Construct this node type
     * @param string $name name of the node type
     * @return null
     */
    public function __construct($name) {
        $this->name = strtolower($name);
    }

    /**
     * Get the data of a node
     * @param int $id id of the node
     * @param boolean $recursive true for a recursive lookup
     * @param string $locale code of the locale
     * @return mixed data of the node
     */
	public function getNodeData($id, $recursive = true, $locale = null) {
	    return null;
	}

    /**
     * Get the label of this node type
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getLabel(Translator $translator) {
        return $translator->translate(Module::TRANSLATION_NODE_TYPE_PREFIX . $this->name);
    }

    /**
     * Flag to see if this node type is available in the frontend
     * @return boolean
     */
    public function isAvailableInFrontend() {
        return false;
    }

    /**
     * Gets the default inherit value for a new node setting
     * @return boolean
     */
    public function getDefaultInherit() {
    	return false;
    }

    /**
     * Get the class name of the frontend controller
     * @return string
     */
    public function getFrontendController() {
        return null;
    }

    /**
     * Get the class name of the backend controller
     * @return string
     */
    public function getBackendController() {
        return null;
    }

}