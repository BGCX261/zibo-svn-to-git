<?php

namespace joppa\router;

use joppa\controller\frontend\IndexController;
use joppa\model\ExpiredRouteModel;
use joppa\model\NodeModel;
use joppa\model\SiteModel;

use zibo\admin\controller\LocalizeController;

use zibo\core\router\Route;
use zibo\core\router\GenericRouter;
use zibo\core\Dispatcher;
use zibo\core\Request;
use zibo\core\Router;
use zibo\core\Zibo;

use zibo\library\database\exception\DatabaseException;
use zibo\library\i18n\I18n;
use zibo\library\orm\exception\OrmException;
use zibo\library\orm\ModelManager;
use zibo\library\String;
use zibo\library\Structure;
use zibo\library\Url;

use \Exception;

/**
 * Router to handle Joppa requests
 */
class JoppaRouter extends GenericRouter {

    /**
     * Class name of the frontend controller
     * @var string
     */
	const FRONTEND_CONTROLLER = 'joppa\\controller\\frontend\\IndexController';

	/**
	 * Previous router, if no page is found, the getRequest() will be passed to this router
	 * @var zibo\core\Router
	 */
	private $router;

	/**
     * Construct this router
     * @param zibo\core\Router $router fallback router
     * @return null
	 */
	public function __construct(Router $router) {
		$this->router = $router;
		$this->router->setDefaultAction(self::FRONTEND_CONTROLLER);
	}

	/**
	 * Get the routes from the Joppa system combined with the routes from the fallback router
	 * @return array Array with the route as key and a Route object as value
	 */
	public function getRoutes() {
	    $model = ModelManager::getInstance()->getModel(NodeModel::NAME);

	    $routes = $model->getNodeRoutes();
	    foreach ($routes as $route) {
            $routes[$route] = new Route($route, self::FRONTEND_CONTROLLER);
	    }

	    return Structure::merge(parent::getRoutes(), $routes);
	}

	/**
	 * Get the request from Joppa, if none found pass the call to the fallback router
	 * @return zibo\core\Request
	 */
    public function getRequest() {
        $query = $this->getQuery();

        if (String::startsWith($query, Zibo::DIRECTORY_WEB . Request::QUERY_SEPARATOR)) {
            return $this->router->getRequest();
        }

        $request = $this->getRequestFromQuery($query);
        if ($request) {
            return $request;
        }

        return $this->router->getRequest();
    }

    /**
     * Look up the node which matches the query
     * @param string $query
     * @return null|zibo\core\Request Request object to dispatch a node if found, null otherwise
     */
    private function getRequestFromQuery($query) {
    	try {
    	    $modelManager = ModelManager::getInstance();
            $nodeModel = $modelManager->getModel(NodeModel::NAME);
            $siteModel = $modelManager->getModel(SiteModel::NAME);
    	} catch (OrmException $e) {
    		return null;
    	}

        $baseUrl = Url::getBaseUrl();

        try {
            $siteUrls = $siteModel->getSiteUrls();
        } catch (DatabaseException $exception) {
        	return null;
        }

        if (array_key_exists($baseUrl, $siteUrls)) {
            $site = $siteUrls[$baseUrl];
        } elseif (array_key_exists(0, $siteUrls)) {
            $site = $siteUrls[0];
        } else {
            return null;
        }

        $routes = $this->getRoutesFromQuery($query);

        $node = $nodeModel->getNodeByRoutes($routes, $query, $site);

        if (!$node) {
            return $this->getRequestFromExpiredQuery($routes, $query, $site);
        }

        if ($node->dataLocale) {
            LocalizeController::setLocale($node->dataLocale);

            $i18n = I18n::getInstance();
            $locale = $i18n->getLocale($node->dataLocale);
            $i18n->setCurrentLocale($locale);
        }

        $route = $node->getRoute();

        $parameters = $this->getParametersFromQuery($query, $route);
        array_unshift($parameters, $node->id);

        return new JoppaRequest($baseUrl, $node, self::FRONTEND_CONTROLLER, IndexController::ACTION_NODE, $parameters);
    }

    /**
     * Look up the node which matches the query based on the expired routes
     * @param string $query
     * @param array $routes
     * @return null|zibo\core\Request Request object to dispatch a node if found, null otherwise
     */
    private function getRequestFromExpiredQuery(array $routes, $query, $site) {
        $model = ModelManager::getInstance()->getModel(ExpiredRouteModel::NAME);

        $node = $model->getNodeByRoutes($routes, $site);

        if (!$node) {
        	return null;
        }

        $route = $node->getRoute();

        $baseUrl = Url::getBaseUrl();
        $basePath = $baseUrl . $route;

        $parameters = $this->getParametersFromQuery($query, $route);
        array_unshift($parameters, $node->id);

        return new Request($baseUrl, $basePath, self::FRONTEND_CONTROLLER, IndexController::ACTION_EXPIRED, $parameters, $query);
    }

}