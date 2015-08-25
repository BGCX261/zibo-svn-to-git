<?php

namespace zibo\library\security\authenticator;

use zibo\core\Zibo;

use zibo\library\security\exception\InactiveAuthenticationException;
use zibo\library\security\exception\PasswordAuthenticationException;
use zibo\library\security\exception\UnauthorizededException;
use zibo\library\security\exception\UsernameAuthenticationException;
use zibo\library\security\exception\UserNotFoundException;
use zibo\library\security\exception\UserSwitchException;
use zibo\library\security\exception\SecurityException;
use zibo\library\security\model\SecurityModel;
use zibo\library\security\model\User;
use zibo\library\security\SecurityManager;
use zibo\library\Boolean;
use zibo\library\String;

/**
 * Authenticator with user storage in a cookie
 */
abstract class AbstractAuthenticator implements Authenticator {

    /**
     * Configuration key for the salt
     * @var string
     */
    const CONFIG_SALT = 'security.salt';

    /**
     * Configuration key for the authentication timeout
     * @var string
     */
    const CONFIG_TIMEOUT = 'security.timeout';

    /**
     * Configuration key for the unique authentication flag
     * @var string
     */
    const CONFIG_UNIQUE = 'security.unique';

    /**
     * Default authentication salt
     * @var string
     */
    const DEFAULT_SALT = 'm2f589kx';

    /**
     * Default authentication timeout
     * @var integer
     */
    const DEFAULT_TIMEOUT = 604800; // 1 week

    /**
     * Default flag for unique authentication
     * @var boolean
     */
    const DEFAULT_UNIQUE = false;

    /**
     * Name of the user preference for the authentication token
     * @var string
     */
    const PREFERENCE_TOKEN = 'security.token';

    /**
     * Name of the user preference for the authentication timeout
     * @var string
     */
    const PREFERENCE_TIMEOUT = 'security.timeout';

    /**
     * Flag to see
     * @var boolean
     */
    private $isUnique;

    /**
     * The salt for the identification token
     * @var string
     */
    private $salt;

    /**
     * The timeout of the authentication in seconds
     * @var integer
     */
    private $timeout;

    /**
     * The current user
     * @var User
     */
    private $user = false;

    /**
     * The security model
     * @var zibo\library\security\model\SecurityModel
     */
    private $securityModel;

    /**
     * Constructs a new authenticator. Reads the authentication parameters from the Zibo configuration.
     * @return null
     */
    public function __construct() {
        $zibo = Zibo::getInstance();

        $salt = $zibo->getConfigValue(self::CONFIG_SALT, self::DEFAULT_SALT);
        $timeout = $zibo->getConfigValue(self::CONFIG_TIMEOUT, self::DEFAULT_TIMEOUT);
        $isUnique = $zibo->getConfigValue(self::CONFIG_UNIQUE, self::DEFAULT_UNIQUE);

        $this->setSalt($salt);
        $this->setTimeout($timeout);
        $this->setIsUnique($isUnique);
    }

    /**
     * Sets the security model to the authenticator
     * @param zibo\library\security\model\SecurityModel $model The security model
     * @return null
     */
    public function setSecurityModel(SecurityModel $model = null) {
        $this->securityModel = $model;
    }

    /**
     * Gets the security model of the authenticator
     * @return zibo\library\security\model\SecurityModel $model The security model
     */
    public function getSecurityModel() {
        return $this->securityModel;
    }

    /**
     * Gets the number of current visitors. This number includes the current users.
     * @return integer
     */
    public function getNumVisitors() {
        return -1;
    }

    /**
     * Gets the number of current users.
     * @return integer
     */
    public function getNumUsers() {
        return -1;
    }

    /**
     * Gets the usernames of the current users
     * @return array Array with usernames
     */
    public function getCurrentUsers() {
        return array();
    }

    /**
     * Switch to the provided user to test it's permissions. When logging out, the user before switching will be the current user
     * @param string $username The username of the user to switch to
     * @return null
     * @throws zibo\library\security\exception\UnauthorizedException when not authenticated
     * @throws zibo\library\security\exception\UserNotFoundException when the requested user could not be found
     */
    public function switchUser($username) {
        $user = $this->getUser();
        if (!$user) {
            throw new UnauthorizedException('Could not switch user: not authenticated');
        }

        $switchedUser = $this->securityModel->getUserByUsername($username);
        if (!$switchedUser) {
            throw new UserNotFoundException('Could not switch user: user not found');
        }

        if (method_exists($user, 'isSuperUser') && !$user->isSuperUser() && $switchedUser->isSuperUser()) {
            throw new UserSwitchException('Could not switch user: ' . $switchedUser->getUserName() . ' is a super user .');
        }

        $this->user = $switchedUser;
        $this->setSwitchedUsername($username);
    }

    /**
     * Login a user
     * @param string $username
     * @param string $password
     * @return zibo\library\security\model\User User instance if login succeeded
     * @throws zibo\library\security\exception\AuthenticationException when the login failed
     */
    public function login($username, $password) {
        if (!$this->securityModel) {
            throw new SecurityException('Could not login a user: no security model set');
        }

        $user = $this->securityModel->getUserByUsername($username);
        if ($user === null) {
            $this->clearAuthentification();
            throw new UsernameAuthenticationException();
        }

        if (!$user->isUserActive()) {
            $this->clearAuthentification();
            throw new InactiveAuthenticationException();
        }

        $securityManager = SecurityManager::getInstance();
        if ($securityManager->hashPassword($password) != $user->getUserPassword()) {
            $this->clearAuthentification();
            throw new PasswordAuthenticationException();
        }

        return $this->setUser($user);
    }

    /**
     * Logout the current user
     * @return null
     */
    public function logout() {
        $this->user = false;
        $this->clearAuthentification();
    }

    /**
     * Gets the current user.
     * @return zibo\library\security\model\User User instance if a user is logged in, null otherwise
     */
    public function getUser() {
        if ($this->user !== false) {
            return $this->user;
        }

        $this->user = null;

        $username = $this->getAuthentificationUsername();
        if (!$username) {
            return null;
        }

        if (!$this->securityModel) {
            throw new SecurityException('Could not login a user: no security model set');
        }

        $user = $this->securityModel->getUserByUsername($username);
        if (!$user) {
            return null;
        }

        $user = $this->setUser($user);

        $username = $this->getSwitchedUsername();
        if (!$username) {
            return $user;
        }

        $switchedUser = $this->securityModel->getUserByUsername($username);
        if (!$switchedUser) {
            return $user;
        }

        $this->user = $switchedUser;

        return $switchedUser;
    }

    /**
     * Sets the current authenticated user
     * @param zibo\library\security\model\User $user User to set the authentication for
     * @return User updated user with the information of the authentification
     */
    public function setUser(User $user) {
        if (!$this->isUnique()) {
            $this->setAuthentification($user->getUserName());

            $this->user = $user;

            return $this->user;
        }

        if (!$this->isUniqueAuthentication($user)) {
            return null;
        }

        $now = time();

        $identifier = $this->getIdentifier($user->username);
        $token = $this->generateToken();
        $timeout = $now + $this->getTimeout();

        $authentificationString = $identifier . ':' . $token;
        $this->setAuthentification($user->getUserName(), $authentificationString);

        $user->setUserPreference(self::PREFERENCE_TOKEN, $token);
        $user->setUserPreference(self::PREFERENCE_TIMEOUT, $timeout);

        if (!$this->securityModel) {
            throw new SecurityException('Could not login a user: no security model set');
        }

        $securityModel->setUser($user);

        $this->user = $user;

        return $this->user;
    }

    /**
     * Checks if the provided user is uniquely authenticated
     * @param zibo\library\security\model\User $user
     * @return boolean True If the authentication is unique, false otherwise
     */
    protected function isUniqueAuthentication(User $user) {
        $string = $this->getAuthentificationString();
        if (!$string) {
            return true;
        }

        if (strpos($string, ':') === false) {
            return false;
        }

        list($identifier, $token) = explode(':', $string);
        if (!(ctype_alnum($identifier) && ctype_alnum($token))) {
            return false;
        }

        $userToken = $user->getUserPreference(self::PREFERENCE_TOKEN);
        $userTimeout = $user->getUserPreference(self::PREFERENCE_TIMEOUT);
        $userIdentifier = $this->getIdentifier($user->getUserName());
        $now = time();

        if (!($userToken == $token && $userTimeout > $now && $userIdentifier == $identifier)) {
            return false;
        }

        return true;
    }

    /**
     * Gets the authenticated username from the storage
     * @return string Username if set, null otherwise
     */
    abstract protected function getAuthentificationUsername();

    /**
     * Gets the unique authentification string from the storage
     * @return string stored authentification string if set, null otherwise
     */
    abstract protected function getAuthentificationString();

    /**
     * Gets the switched username from the storage
     * @return string Username of the switched user if set, null otherwise
     */
    abstract protected function getSwitchedUsername();

    /**
     * Sets the switched username from the storage
     * @param $username string Username of the user to switch to
     * @return null
     */
    abstract protected function setSwitchedUsername($username);

    /**
     * Sets the authentification to the storage
     * @param string $username Username
     * @param string $authentificationString Unique authentification string
     * @return null
     */
    abstract protected function setAuthentification($username, $authentificationString = null);

    /**
     * Clears the authentification in the storage. When there is a user switch, only the switched user setting should be cleared.
     * If no user switch, all authentication data should be cleared
     * @return null
     */
    abstract protected function clearAuthentification();

    /**
     * Gets the identifier for a given value
     * @param string $value Value to get an identifier from
     * @return string Identifier of the value
     */
    protected function getIdentifier($value) {
        return md5($this->salt . md5($value . $this->salt));
    }

    /**
     * Generates a random token
     * @return string
     */
    protected function generateToken() {
        return md5(uniqid(rand(), true));
    }

    /**
     * Sets the salt which is used to create a identifier
     * @param string salt
     * @return null
     */
    public function setSalt($salt) {
        if (String::isEmpty($salt)) {
            throw new ZiboException('Provided salt is empty');
        }

        $this->salt = $salt;
    }

    /**
     * Gets the salt which is used to create a identifier
     * @return string
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Sets the timeout of the authentification
     * @param integer $timeout Timeout in seconds
     * @return null
     * @throws zibo\ZiboException when the provided timeout is invalid
     */
    public function setTimeout($timeout) {
        if (empty($timeout) || !is_numeric($timeout) || $timeout < 0) {
            throw new ZiboException('Provided timeout is invalid');
        }

        $this->timeout = $timeout;
    }

    /**
     * Gets the timeout of the authentification
     * @return integer Timeout in seconds
     */
    public function getTimeout() {
        return $this->timeout;
    }

    /**
     * Sets the unique flag
     * @param boolean $flag True to let a user authenticate only at one client at a time, false otherwise
     * @return null
     * @throws zibo\ZiboException when the provided flag is invalid
     */
    public function setIsUnique($flag) {
        $this->isUnique = Boolean::getBoolean($flag);
    }

    /**
     * Gets the unique flag
     * @return boolean True to let a user authenticate only at one client at a time, false otherwise
     */
    public function isUnique() {
        return $this->isUnique;
    }

}