<?php

namespace joppa\model;

use joppa\model\NodeModel;

use zibo\admin\controller\LocalizeController;

use zibo\library\orm\model\SimpleModel;

use zibo\ZiboException;

/**
 * Model to manage the expired node routes
 */
class ExpiredRouteModel extends SimpleModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'ExpiredRoute';

	/**
	 * Adds a new expired route for a node
	 * @param string $route
	 * @param integer $nodeId
	 * @param string $locale
	 * @return null
	 */
	public function addExpiredRoute($route, $nodeId, $locale) {
		$data = $this->createData(false);
		$data->route = $route;
		$data->node = $nodeId;
		$data->locale = $locale;

		$this->save($data);
	}

   /**
     * Get the node which has an expired route which matches the array routes
     *
     * If recieve the query home/info1/action1/parameter, the routes array should be like:
     * <ul>
     * <li>home/info1/action1/parameter</li>
     * <li>home/info1/action1</li>
     * <li>home/info1</li>
     * <li>home</li>
     * </ul>
     *
     * @param array $routes Array with routes to match.
     * @return Node
     */
    public function getNodeByRoutes(array $routes, $site, $locale = null) {
        if (empty($routes) || (count($routes) == 1 && empty($routes[0]))) {
            return null;
        }

        if ($locale === null) {
            $locale = LocalizeController::getLocale();
        }

        $query = $this->createQuery(0);
        $query->addCondition('{node.parent} = %1% OR {node.parent} LIKE %2%', $site->node, $site->node . NodeModel::PATH_SEPARATOR . '%');

        $condition = '';
        foreach ($routes as $index => $route) {
            $condition .= ($condition ? ' OR ' : '') . '{route} = %' . $index . '%';
        }
        $query->addConditionWithVariables($condition, $routes);

        $query->addOrderBy('{route} DESC');

        $expiredRoutes = $query->query();

        if (!$expiredRoutes) {
            return null;
        }

        $nodeModel = $this->getModel(NodeModel::NAME);
        $route = null;
        foreach ($expiredRoutes as $expiredRoute) {
            if (!$route) {
                $route = $expiredRoute->route;
            } elseif ($expiredRoute->route != $route) {
                break;
            }

            $node = $nodeModel->createData(false);
            $node->id = $expiredRoute->node;
            $node->route = $expiredRoute->route;
            $node->dataLocale = $expiredRoute->locale;

            if ($node->dataLocale === $locale) {
                break;
            }
        }

        return $node;
    }

}