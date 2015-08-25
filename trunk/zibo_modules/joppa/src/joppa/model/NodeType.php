<?php

namespace joppa\model;

use zibo\library\i18n\translation\Translator;

/**
 * Interface for a node type
 */
interface NodeType {

    /**
     * Get the data of a node
     * @param int $id id of the node
     * @param boolean $recursive true for a recursive lookup
     * @param string $locale code of the locale
     * @return mixed data of the node
     */
    public function getNodeData($id, $recursive = true, $locale = null);

    /**
     * Get the label of this node type
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getLabel(Translator $translator);

    /**
     * Checks if this node type is available in the frontend
     * @return boolean
     */
    public function isAvailableInFrontend();

    /**
     * Gets the default inherit value for a new node setting
     * @return boolean
     */
    public function getDefaultInherit();

    /**
     * Get the class name of the frontend controller
     * @return string|null
     */
    public function getFrontendController();

    /**
     * Get the class name of the backend controller
     * @return string|null
     */
    public function getBackendController();

}