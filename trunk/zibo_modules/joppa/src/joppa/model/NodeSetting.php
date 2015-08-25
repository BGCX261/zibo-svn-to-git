<?php

namespace joppa\model;

/**
 * NodeSetting data
 */
class NodeSetting {

	/**
	 * Id of the node setting
	 * @var int
	 */
	public $id;

	/**
	 * Node where this setting belongs to
	 * @var int|Node
	 */
	public $node;

	/**
	 * Key of the setting
	 * @var string
	 */
	public $key;

	/**
	 * Value of the setting
	 * @var string
	 */
	public $value;

	/**
	 * Flag to set if this setting should be inherited to lower levels
	 * @var boolean
	 */
	public $inherit;

	/**
	 * Get a INI string for this setting
	 * @param boolean $escapeHtml Set to true to escape the HTML in value
	 * @return string
	 */
	public function getIniString($escapeHtml = false) {
	    $ini = '';

	    if ($this->inherit) {
	        $ini .= NodeSettings::INHERIT_PREFIX;
	    }

	    $value = $this->value;
	    if ($escapeHtml) {
            $value = htmlspecialchars($value);
	    } else {
            $value = addcslashes($value, '"');
	    }

	    $ini .= $this->key . ' = "' . $value . '"';

	    return $ini;
	}

}