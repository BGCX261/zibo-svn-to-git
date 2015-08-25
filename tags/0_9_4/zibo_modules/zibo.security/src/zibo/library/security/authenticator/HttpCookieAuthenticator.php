<?php

namespace zibo\library\security\authenticator;

use zibo\library\Session;

/**
 * HTTP authenticator with authentication storage as cookies
 */
class HttpSessionAuthenticator extends HttpAuthenticator {

    /**
     * Constructs a new authenticator
     * @return null
     */
    public function __construct() {
        parent::__construct(new CookieAuthenticator());
    }

}