<?php

namespace joppa\router;

use joppa\model\Node;

use zibo\core\Request;

/**
 * Data container of a Joppa request
 */
class JoppaRequest extends Request {

	/**
	 * Node of the joppa request
	 * @var joppa\model\Node
	 */
	private $node;

    /**
     * Construct a new Joppa request
     * @param string $baseUrl the base url of the request
     * @param joppa\model\Node $node The node of this request
     * @param string $controllerName the full name of the controller class (including namespace)
     * @param string $actionName the action method in the controller class
     * @param array $parameters an array containing the parameters for the action method
     * @return null
     */
    public function __construct($baseUrl, Node $node, $controllerName, $actionName, array $parameters) {
    	$basePath = $baseUrl . Request::QUERY_SEPARATOR . $node->getRoute();

    	parent::__construct($baseUrl, $basePath, $controllerName, $actionName, $parameters);

    	$this->setNode($node);
    }

	/**
	 * Sets the node of this request
	 * @param joppa\model\Node $node
	 * @return null
	 */
	private function setNode(Node $node) {
		$this->node = $node;
	}

	/**
	 * Gets the node of this request
	 * @return joppa\model\Node
	 */
	public function getNode() {
		return $this->node;
	}

}