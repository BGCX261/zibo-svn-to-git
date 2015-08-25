<?php

namespace zibo\core\environment;

use zibo\core\Request;

/**
 * Environment for a web request
 */
class WebEnvironment extends Environment {

    /**
     * Name of this environment
     * @var string
     */
    const NAME = 'web';

    /**
     * Name of the query argument
     * @var string
     */
    const QUERY_NAME = 'q';

    /**
     * Get the name of this environment
     * @return string
     */
    public function getName() {
        return self::NAME;
    }

    /**
     * Get the query for the request
     * @return string
     */
    public function getQuery() {
        $query = $this->getArgument(self::QUERY_NAME, '');
        if ($query) {
//            $query = str_replace(Url::getBaseUrl(), '', $query);
            $query = str_replace('?' . self::QUERY_NAME . '=', '', $query);
            $query = ltrim($query, Request::QUERY_SEPARATOR);
            $query = rtrim($query, Request::QUERY_SEPARATOR);
        }

        return $query;
    }

    /**
     * Get all the arguments of this environment
     * @return array
     */
    public function getArguments() {
        return $_GET;
    }

    /**
     * Get a argument of this environment
     * @param string $name name of the argument
     * @param mixed $default default value for when the argument is not set
     * @return mixed the value of the argument or the provided default value if the value is not set
     */
    public function getArgument($name, $default = null) {
        if (array_key_exists($name, $_GET)) {
            return $_GET[$name];
        }

        return $default;
    }

}