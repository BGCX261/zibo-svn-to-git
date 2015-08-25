<?php

namespace zibo\library\box\authentication;

/**
 * Session implementation for the box.net authentication storage
 */
class RequestAuthentication implements Authentication {

	/**
	 * The authentication token
	 * @var string
	 */
	private $token;

    /**
     * This is the method that is called whenever an authentication token is
     * received.
     * @param string $token Authentication token
     * @return string The authentication token
     */
    public function store($token) {
		$this->token = $token;
    }

    /**
     * This is the method that is called whenever an authentication is requested. If a token
     * exists, it will be used for all operations of the client. If it does not exist, the
     * authentication will be triggered
     * @return string|null The authentication token if set, null otherwise
     */
    public function retrieve() {
		return $this->token;
    }

}