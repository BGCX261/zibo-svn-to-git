<?php

namespace joppa\controller\backend;

use joppa\controller\backend\action\NodeActionManager;
use joppa\view\backend\TreeView;

use joppa\model\NodeModel;
use joppa\model\NodeTypeFacade;

use zibo\core\Request;

/**
 * Controller to dispatch the node actions
 */
class NodeController extends BackendController {

    /**
     * Dispatches or redirects the node action to the right node action controller
     * @param string $action
     * @param int $id id of the node
     * @return null
     */
    public function indexAction($action = null, $id = null) {
        if (!$id && !$action) {
            $this->response->setRedirect($this->getJoppaBaseUrl());
            return;
        }

        if (!$this->site) {
            $this->response->setRedirect($this->getJoppaBaseUrl());
            return;
        }

        if (!$id && is_numeric($action)) {
        	$lastAction = $this->getSession()->get(self::SESSION_LAST_ACTION, 'content');
            $this->response->setRedirect($this->getJoppaBaseUrl() . '/node/' . $lastAction . '/' . $action);
            return;
        }

        if (!$this->node || $this->node->id != $id) {
            $this->node = $this->models[NodeModel::NAME]->getNode($id, 0);

            if ($this->node) {
                $parameters = func_get_args();
                $url = $this->request->getBasePath() . Request::QUERY_SEPARATOR . implode(Request::QUERY_SEPARATOR, $parameters);

                $this->response->setRedirect($url);
            } else {
                $this->response->setRedirect($this->getJoppaBaseUrl());
            }

            return;
        }

        $nodeActionManager = NodeActionManager::getInstance();
        if ($nodeActionManager->hasAction($action)) {
            // chain to the node action
        	$this->getSession()->set(self::SESSION_LAST_ACTION, $action);

            $nodeAction = $nodeActionManager->getAction($action);

            $parameters = func_get_args();
            $action = array_shift($parameters);
            $id = array_shift($parameters);

            $basePath = $this->request->getBasePath();
            $basePath .= Request::QUERY_SEPARATOR . $action . Request::QUERY_SEPARATOR . $id;

            return $this->forward(get_class($nodeAction), null, $parameters, $basePath);
        }

        // chain to the node type's backend controller
        $nodeTypeFacade = NodeTypeFacade::getInstance();
        if (!$nodeTypeFacade->hasNodeType($this->node->type)) {
            return $this->setError404();
        }

        $nodeTypeController = $nodeTypeFacade->getBackendController($this->node->type);
        if (!$nodeTypeController) {
            return $this->setError404();
        }

        $nodeId = $this->node->id;
        $nodeData = $nodeTypeFacade->getNodeData($this->node->type, $nodeId, 0);
        if ($nodeData) {
            $nodeId = $nodeData->id;
        }

        $parameters = func_get_args();
        $action = array_shift($parameters);
        array_shift($parameters);
        array_unshift($parameters, $action, $nodeId);

        $basePath = $this->getJoppaBaseUrl() . Request::QUERY_SEPARATOR . $this->node->type;

        return $this->forward($nodeTypeController, null, $parameters, $basePath);
	}

	/**
	 * Action to reorder the nodes
	 * @param string $nodes Serialized node string from the node tree
	 * @return null
	 */
	public function orderAction() {
        if (!$this->site || !array_key_exists('node', $_REQUEST)) {
            $this->response->setRedirect($this->getJoppaBaseUrl());
            return;
        }

		$nodeOrder = array();

		foreach ($_REQUEST['node'] as $nodeId) {
			$numChildren = 0;
			if (strpos($nodeId, '_') !== false) {
				list($nodeId, $numChildren) = explode('_', $nodeId, 2);
			}

			$nodeOrder[$nodeId] = $numChildren;
		}

		$this->models[NodeModel::NAME]->orderNodes($this->site->node->id, $nodeOrder);

		$this->clearCache();

		$view = new TreeView($this->site, $this->node);
		$this->response->setView($view);
	}

}