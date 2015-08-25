<?php

namespace joppa\model\content;

use joppa\model\NodeModel;

use zibo\library\orm\ModelManager;

use zibo\ZiboException;

/**
 * Parser for Joppa variables in a string. Example of a Joppa variable %node.5.url%
 */
class TextParser {

	/**
	 * Regular expression for a node variable
	 * @var string
	 */
	const REGEX_VARIABLE = '/%node\\.([0-9]*)\\.([a-zA-Z]*)%/';

	/**
	 * Name of the name variable
	 * @var string
	 */
	const VARIABLE_NAME = 'name';

	/**
	 * Name of the url variable
	 * @var string
	 */
	const VARIABLE_URL = 'url';

	/**
	 * Model of the nodes
	 * @var joppa\model\NodeModel
	 */
	private $nodeModel;

	/**
	 * Constructs a new text parser
	 * @return null
	 */
	public function __construct() {
		$this->nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
	}

	/**
	 * Parses a text to replace Joppa variables with their values. Syntax of a variable is %node.<id>.<variable>% eg. %node.5.url% or %node.3.name%
	 * @param string $text Text with names of the Joppa variables
	 * @return string Parsed text with the values of the Joppa variables
	 */
	public function parseText($text) {
        return preg_replace_callback(self::REGEX_VARIABLE, array($this, 'getParsedVariable'), $text);
	}

	/**
	 * Gets the value of the provided variable
	 * @param array $matches The matches of the variable regular expression (key 0: node id; key 1: name of the variable)
	 * @return string The value of the provided variable
	 * @throws zibo\ZiboException when an unsupported variable is provided
	 */
    public function getParsedVariable(array $matches) {
        $nodeId = $matches[1];
        $variableName = strtolower($matches[2]);

        $node = $this->nodeModel->getNode($nodeId, 0);
        if (!$node) {
        	return;
        }

        switch($variableName) {
        	case self::VARIABLE_URL:
        		$value = $node->getRoute();
        	    break;
        	case self::VARIABLE_NAME:
        		$value = $node->name;
        		break;
        	default:
        		throw new ZiboException($variableName . ' is not a supported Joppa variable. Try name of url');
        }

        return $value;
    }

}