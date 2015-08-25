<?php

namespace joppa\controller\frontend;

use joppa\model\NodeModel;
use joppa\model\NodeTypeFacade;
use joppa\model\SiteModel;

use joppa\view\frontend\NodeView;

use joppa\Module;

use zibo\admin\controller\AbstractController;
use zibo\admin\controller\LocalizeController;

use zibo\core\Request;
use zibo\core\Response;
use zibo\core\View;

use zibo\ZiboException;

/**
 * Controller of the Joppa frontend
 */
class IndexController extends AbstractController {

    /**
     * Action of the frontend controller to display a node
     * @var string
     */
    const ACTION_NODE = 'nodeAction';

    /**
     * Action of the frontend controller to redirect an expired request of a node
     * @var string
     */
    const ACTION_EXPIRED = 'expiredAction';

    /**
     * Hook with the ORM module
     * @var array
     */
	public $useModels = array(NodeModel::NAME, SiteModel::NAME);

	/**
	 * Sets the default node to the response. If no default node, an error 404 will be set.
	 * @return null
	 */
	public function indexAction() {
        $this->setError404();
	}

	/**
     * Dispatches the frontend of a node
     * @param int $id id of the page
     * @return null
	 */
	public function nodeAction($id) {
		$baseUrl = $this->request->getBaseUrl();

	    $nodeDispatcher = $this->getNodeDispatcher($id, $baseUrl);
	    if (!$nodeDispatcher) {
	        $this->setError404();
	        return;
	    }

	    // $nodeDispatcher is the class name of the node's frontend controller
	    if (is_string($nodeDispatcher)) {
	        return $this->forward($nodeDispatcher, null, func_get_args());
	    }

		$node = $nodeDispatcher->getNode();

		$basePath = $baseUrl . Request::QUERY_SEPARATOR . $node->getRoute();

		$parameters = func_get_args();
		array_shift($parameters);

		$request = new Request($baseUrl, $basePath, null, '*', $parameters);

		$views = $nodeDispatcher->dispatch($request, $this->response);

		// no views, a redirect is assumed
		if (!$views) {
		    return;
		}

		// direct view recieved, a file or download view is assumed
		if ($views instanceof View) {
		    $this->response->setView($views);
		    return;
		}

		$nodeView = $nodeDispatcher->getNodeView();
		$nodeView->setDispatchedViews($views);

		$this->response->setView($nodeView);
	}

	/**
     * Get the dispatcher of a node. This method uses the Joppa cache to store the dispatcher.
     * @param int $id id of the node
     * @param string $baseUrl base url of the site
     * @return mixed null no node found or the node is not available in the frontend;
     *               string class name of the node's frontend controller;
     *               joppa\controller\frontend\NodeDispatcher a node dispatcher
	 */
	private function getNodeDispatcher($id, $baseUrl) {
        $cache = Module::getCache();
        $locale = LocalizeController::getLocale();

        $cacheKey = md5($baseUrl) . '-' . $id . '-' . $locale;

        $nodeDispatcher = $cache->get(Module::CACHE_TYPE_NODE_DISPATCHER, $cacheKey);
        if ($nodeDispatcher) {
            return $nodeDispatcher;
        }

        $node = $this->request->getNode();

        $frontendController = NodeTypeFacade::getInstance()->getFrontendController($node->type);
        if ($frontendController) {
            $cache->set(Module::CACHE_TYPE_NODE_DISPATCHER, $cacheKey, $frontendController);
            return $frontendController;
        }

        $breadcrumbs = $this->models[NodeModel::NAME]->getBreadcrumbsForNode($node, $baseUrl);
        $rootNode = $node->getRootNode();
        $nodeView = new NodeView($node, $rootNode->name);

        $nodeDispatcher = new NodeDispatcher($node, $nodeView);
        $nodeDispatcher->setBreadcrumbs($breadcrumbs);

        $cache->set(Module::CACHE_TYPE_NODE_DISPATCHER, $cacheKey, $nodeDispatcher);

        return $nodeDispatcher;
	}

	/**
	 * Action to redirect to the current route of the provided node
	 * @param integer $id Id of the node to redirect to
	 * @return null
	 */
	public function expiredAction($id) {
        $node = $this->models[NodeModel::NAME]->getNode($id, 0);
        if (!$node) {
            $this->setError404();
            return;
        }

        $baseUrl = $this->request->getBaseUrl();
        $redirectUrl = $baseUrl . Request::QUERY_SEPARATOR . $node->getRoute();

        $parameters = func_get_args();
        array_shift($parameters);

        if ($parameters) {
        	$redirectUrl .= Request::QUERY_SEPARATOR . implode(Request::QUERY_SEPARATOR, $parameters);
        }

        $this->response->setRedirect($redirectUrl, Response::STATUS_CODE_MOVED_PERMANENTLY);
	}

}