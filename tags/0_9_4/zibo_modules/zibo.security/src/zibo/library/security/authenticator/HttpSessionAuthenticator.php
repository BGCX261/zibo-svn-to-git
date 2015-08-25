<?php

namespace zibo\library\security\authenticator;

use zibo\library\Session;

/**
 * HTTP authenticator with user storage in the session
 */
class HttpSessionAuthenticator extends HttpAuthenticator {

    /**
     * Constructs a new authenticator
     * @return null
     */
    public function __construct() {
        parent::__construct(new SessionAuthenticator());
    }

}