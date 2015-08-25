<?php

/**
 * @package zibo-xmlrpc-model
 */
namespace zibo\xmlrpc\model;

use zibo\library\xmlrpc\Value;

/**
 * Webservice module for security services
 */
class SecurityWebserviceModule extends AbstractWebserviceModule {

    const DESCRIPTION_AUTHENTICATE = 'Authenticate yourself with your username and password and retrieve your session id. (username, password)';

    const PREFIX = 'security.';

    const SERVICE_AUTHENTICATE = 'authenticate';

    public function __construct() {
        parent::__construct(self::PREFIX);
        $this->authenticator = new Authenticator();
    }

    public function getServices() {
        return array(
            array(
                self::SERVICE_AUTHENTICATE,
                array($this, self::SERVICE_AUTHENTICATE),
                Value::TYPE_STRING,
                array(Value::TYPE_STRING, Value::TYPE_STRING),
                self::DESCRIPTION_AUTHENTICATE,
            ),
        );
    }

    public function authenticate($username, $password) {
        return $this->authenticator->authenticateUser($username, $password);
    }

    public function getAuthenticator() {
        return $this->authenticator;
    }

}