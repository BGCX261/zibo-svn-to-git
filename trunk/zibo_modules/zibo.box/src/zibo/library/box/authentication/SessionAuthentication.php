<?php

namespace zibo\library\box\authentication;

use zibo\library\Session;

/**
 * Session implementation for the box.net authentication storage
 */
class SessionAuthentication implements Authentication {

    /**
     * Default name for the session variable
     * @var string
     */
    const SESSION_TOKEN = 'box.auth.token';

    /**
     * Constructs a new session authentication
     * @param string $variableName The name for the session variable
     * @return null
     */
    public function __construct($variableName = null) {
        if (!$variableName) {
            $variableName = self::SESSION_TOKEN;
        }

        $this->variableName = $variableName;
    }

    /**
     * This is the method that is called whenever an authentication token is
     * received.
     * @param string $token Authentication token
     * @return string The authentication token
     */
    public function store($token) {
        Session::getInstance()->set($this->variableName, $token);
    }

    /**
     * This is the method that is called whenever an authentication is requested. If a token
     * exists, it will be used for all operations of the client. If it does not exist, the
     * authentication will be triggered
     * @return string|null The authentication token if set, null otherwise
     */
    public function retrieve() {
        return Session::getInstance()->get($this->variableName);
    }

}