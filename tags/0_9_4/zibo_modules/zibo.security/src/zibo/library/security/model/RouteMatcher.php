<?php

namespace zibo\library\security\model;

use zibo\library\security\SecurityManager;

/**
 * Matcher for a route against route regular expressions
 */
class RouteMatcher {

    /**
     * Check if a route matches one of the routes in the provided array
     * @param string $route Route the match
     * @param array $routeRegexes Array with regular expressions for possible routes to match
     * @return boolean True if matched, false otherwise
     */
    public function matchRoute($route, array $routeRegexes) {
        foreach ($routeRegexes as $regex) {
            $regex = str_replace(SecurityManager::ASTERIX, '([\w|\W])*', $regex);
            $regex = str_replace('/', '\\/', $regex);
            $regex = '/^' . $regex . '$/';

            if (preg_match($regex, $route)) {
                return true;
            }
        }

        return false;
    }

}