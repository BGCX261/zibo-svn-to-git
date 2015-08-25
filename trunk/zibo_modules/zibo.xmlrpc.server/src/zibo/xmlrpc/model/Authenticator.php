<?php

/**
 * @package zibo-xmlrpc-model
 */
namespace zibo\xmlrpc\model;

use zibo\library\security\exception\UnauthorizedException;
use zibo\library\security\SecurityManager;
use zibo\library\Session;

/**
 * Authenticator to login once and reuse your session
 */
class Authenticator {

    const SESSION_AUTHENTICATED = 'auth';

    /**
     * Authenticate a user and get his/her session id
     * @param string username of the user
     * @param string password of the user
     * @return string id of the user's session
     */
    public function authenticateUser($username, $password) {
        $securityManager = SecurityManager::getInstance();
        $securityManager->login($username, $password);

        $session = Session::getInstance();
        $session->set(self::SESSION_AUTHENTICATED, true);

        return $session->getId();
    }

    /**
     * Authenticate a user's session by it's id
     * @param string id of the user's session
     * @throws UnauthorizedException if the token is no longer valid
     */
    public function authenticateSession($id) {
        $session = Session::getInstance();
        if ($session->getId() != $id) {
            $session->load($id);
        }

        if (!$session->get(self::SESSION_AUTHENTICATED)) {
            throw new UnauthorizedException('You are no longer authorized because your session id or your session itself has become invalid.');
        }
    }

}