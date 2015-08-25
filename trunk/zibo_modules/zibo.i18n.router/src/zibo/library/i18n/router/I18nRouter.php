<?php

namespace zibo\library\i18n\router;

use zibo\core\router\GenericRouter;
use zibo\core\Request;

use zibo\library\i18n\I18n;
use zibo\library\Url;

/**
 * Generic router implementation
 */
class I18nRouter extends GenericRouter {

    /**
     * Route the request query to a Request object
     * @return zibo\core\Request
     */
    public function getRequest() {
        $query = $this->getQuery();
        $routes = $this->getRoutesFromQuery($query);

        $io = $this->getIO();
        $route = $io->getRouteFromQuery($query, $routes);

        $request = null;
        $baseUrl = Url::getSystemBaseUrl();
        if ($route !== null) {
            $locale = I18n::getInstance()->getLocale();

            $basePath = $baseUrl . Request::QUERY_SEPARATOR . $locale->getCode() . Request::QUERY_SEPARATOR . $route->getPath();
            $controller = $route->getControllerClass();
            $action = $route->getAction();
            $parameters = $this->getParametersFromQuery($query, $route->getPath());

            $request = new Request($baseUrl, $basePath, $controller, $action, $parameters);
        } elseif ($defaultController = $this->getDefaultController()) {
            $parameters = $this->getParametersFromQuery($query, '');

            $request = new Request($baseUrl, $baseUrl, $defaultController, $this->getDefaultAction(), $parameters);
        }

        return $request;
    }

    /**
     * Get the full query of the request
     * @return string
     */
    protected function getQuery() {
        $query = parent::getQuery();

        $tokens = explode(Request::QUERY_SEPARATOR, $query, 2);
        if (!$tokens) {
            return $query;
        }

        $i18n = I18n::getInstance();

        if ($i18n->hasLocale($tokens[0])) {
            $locale = $i18n->getLocale($tokens[0]);
            $i18n->setCurrentLocale($locale);

            if (array_key_exists(1, $tokens)) {
                $query = $tokens[1];
            } else {
                $query = '';
            }
        }

        return $query;
    }

}