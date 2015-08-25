<?php

namespace zibo\library\security\authenticator;

use zibo\library\security\model\SecurityModel;
use zibo\library\security\model\User;

/**
 * Interface to maintain the authentication of a user with username and password
 */
interface Authenticator {

    /**
     * Sets the security model to the authenticator
     * @param zibo\library\security\model\SecurityModel $model The security model
     * @return null
     */
    public function setSecurityModel(SecurityModel $model = null);

    /**
     * Gets the security model of the authenticator
     * @return zibo\library\security\model\SecurityModel $model The security model
     */
    public function getSecurityModel();

    /**
     * Switch to the provided user to test it's permissions. When logging out, the user before switching will be the current user
     * @param string $username The username of the user to switch to
     * @return null
     * @throws zibo\library\security\exception\UnauthorizedException when not authenticated
     * @throws zibo\library\security\exception\UserNotFoundException when the requested user could not be found
     */
    public function switchUser($username);

    /**
     * Login a user
     * @param string $username
     * @param string $password
     * @return zibo\library\security\model\User The user if the login succeeded
     * @throws zibo\library\security\exception\AuthenticationException when the user could not be authenticated
     */
    public function login($username, $password);

    /**
     * Logout the current user, if the current user is a switched user, the original user is now the current user
     * @return null
     */
    public function logout();

    /**
     * Gets the current user
     * @return zibo\library\security\model\User|null the current user if logged in, null otherwise
     */
    public function getUser();

    /**
     * Sets the current authenticated user
     * @param zibo\library\security\model\User $user User to set the authentication for
     * @return User updated user with the information of the authentification
     */
    public function setUser(User $user);

    /**
     * Gets the number of current visitors. This number includes the current users.
     * @return integer
     */
    public function getNumVisitors();

    /**
     * Gets the number of current users.
     * @return integer
     */
    public function getNumUsers();

    /**
     * Gets the usernames of the current users
     * @return array Array with usernames
     */
    public function getCurrentUsers();

}