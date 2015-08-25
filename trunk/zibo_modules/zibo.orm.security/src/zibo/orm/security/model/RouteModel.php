<?php

namespace zibo\orm\security\model;

use zibo\core\Zibo;

use zibo\library\orm\model\SimpleModel;

/**
 * Route model
 */
class RouteModel extends SimpleModel {

    /**
     * Name of the model
     * @var string
     */
    const NAME = 'Route';

    /**
     * Saves the global denied routes to the model
     * @param array $routes Array with a route string per element
     * @return null
     * @throws Exception when an error occured
     */
    public function setDeniedRoutes(array $routes) {
        $transactionStarted = $this->startTransaction();

        try {
            // clear denied flag on all routes
            $query = $this->createQuery(0);
            $query->addCondition('{isDenied} = 1');

            $result = $query->query();

            $this->save(false, 'isDenied', $result);

            // set denied flag to provided routes
            $routes = $this->getRoutesFromArray($routes);
            foreach ($routes as $route) {
                $route->isDenied = true;
            }

            $this->save($routes);

            $this->commitTransaction($transactionStarted);
        } catch (Exception $e) {
            $this->rollbackTransaction($transactionStarted);
        }
    }

    /**
     * Gets the global denied routes from the model
     * @return array Array with a route string as key and value
     */
    public function getDeniedRoutes() {
        $query = $this->createQuery(0);
        $query->addCondition('{isDenied} = 1');

        $result = $query->query();

        $deniedRoutes = array();
        foreach ($result as $route) {
            $deniedRoutes[$route->route] = $route->route;
        }

        return $deniedRoutes;
    }

    /**
     * Get Route objects from route strings
     * @param array $routes Array with a route string per element
     * @return array Array with a RouteData object per element
     */
    public function getRoutesFromArray(array $routes) {
        $modelRoutes = array();

        foreach ($routes as $route) {
            $modelRoute = $this->findFirstBy('route', $route, 0);

            if ($modelRoute == null) {
                $modelRoute = $this->createData();
                $modelRoute->route = $route;
                $modelRoute->isDenied = false;
            }

            $modelRoutes[] = $modelRoute;
        }

        return $modelRoutes;
    }

}