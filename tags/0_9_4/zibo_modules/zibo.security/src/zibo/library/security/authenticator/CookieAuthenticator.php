<?php

namespace zibo\library\security\authenticator;

use zibo\library\Cookie;

/**
 * Authenticator with authentication storage in a cookie
 */
class CookieAuthenticator extends AbstractAuthenticator {

    /**
     * Cookie name for the username of the switched user
     * @var string
     */
    const COOKIE_SWITCHED_USERNAME = 'security.username.switched';

    /**
     * Cookie name for the username
     * @var string
     */
    const COOKIE_USERNAME = 'security.username';

    /**
     * Cookie name for the authentication string
     * @var string
     */
    const COOKIE_AUTHENTICATION_STRING = 'security.authentication';

    /**
     * Instance of the cookie
     * @var zibo\library\Cookie
     */
    private $cookie;

    /**
     * Constructs a new authenticator
     * @return null
     */
    public function __construct() {
        parent::__construct();

        $this->cookie = Cookie::getInstance();
    }

    /**
     * Save the authentification to the cookie
     * @param string $username Username
     * @param string $authentificationString Authentification string
     * @return null
     */
    protected function setAuthentification($username, $authentificationString = null) {
        $timeout = time() + $this->getTimeout();

        $this->cookie->set(self::COOKIE_USERNAME, $username, $timeout);
        $this->cookie->set(self::COOKIE_AUTHENTICATION_STRING, $authentificationString, $timeout);
    }

    /**
     * Sets the switched username to the storage
     * @param $username string Username of the user to switch to
     * @return null
     */
    protected function setSwitchedUsername($username) {
        return $this->cookie->set(self::COOKIE_SWITCHED_USERNAME, $username);
    }

    /**
     * Gets the stored username from the cookie
     * @return string Stored username if set, null otherwise
     */
    protected function getAuthentificationUsername() {
        return $this->cookie->get(self::COOKIE_USERNAME);
    }

    /**
     * Gets the stored authentification string from the cookie
     * @return string Stored authentification string if set, null otherwise
     */
    protected function getAuthentificationString() {
        return $this->cookie->get(self::COOKIE_AUTHENTICATION_STRING);
    }

    /**
     * Gets the stored username from the session
     * @return string Stored username if set, null otherwise
     */
    protected function getSwitchedUsername() {
        return $this->cookie->get(self::COOKIE_SWITCHED_USERNAME);
    }

    /**
     * Clears the authentification in the storage. When there is a user switch, only the authentication of the switched user should be cleared.
     * If there is no user switch, all authentication data should be cleared
     * @return null
     */
    protected function clearAuthentification() {
        $clear = 'clear';
        $timeout = time();

        if ($this->getSwitchedUsername()) {
            $this->cookie->set(self::COOKIE_SWITCHED_USERNAME, $clear, $timeout);
        } else {
            $this->cookie->set(self::COOKIE_USERNAME, $clear, $timeout);
            $this->cookie->set(self::COOKIE_AUTHENTICATION_STRING, $clear, $timeout);
        }
    }

}