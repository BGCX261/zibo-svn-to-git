<?php

namespace zibo\library\security;

use zibo\core\environment\CliEnvironment;
use zibo\core\environment\Environment;
use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\encryption\Module as EncryptionModule;
use zibo\library\security\exception\SecurityModelNotSetException;
use zibo\library\security\model\Role;
use zibo\library\security\model\RouteMatcher;
use zibo\library\security\model\User;
use zibo\library\ObjectFactory;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Facade to the security system
 */
class SecurityManager {

    /**
     * Asterix value to match everything
     * @var string
     */
    const ASTERIX = '*';

    /**
     * Configuration key to the class name of the authenticator
     * @var string
     */
    const CONFIG_AUTHENTICATOR = 'security.authenticator';

    /**
     * Configuration key to the class name of the security model
     * @var string
     */
    const CONFIG_SECURITY_MODEL = 'security.model';

    /**
     * Configuration key for the name of the hash algorithm to use
     * @var string
     */
    const CONFIG_HASH_ALGORITHM = 'security.password.hash';

    /**
     * Name of the default hash algorithm
     * @var string
     */
    const DEFAULT_AUTHENTICATOR = 'zibo\\library\\security\\authenticator\\SessionAuthenticator';

    /**
     * Name of the default hash algorithm
     * @var string
     */
    const DEFAULT_HASH_ALGORITHM = 'sha1';

    /**
     * Name of the event run after a login
     * @var string
     */
    const EVENT_LOGIN = 'security.authentication.login';

    /**
     * Name of the security log
     * @var string
     */
    const LOG_NAME = 'security';

    /**
     * Class name of the authenticator interface
     * @var string
     */
    const INTERFACE_AUTHENTICATOR = 'zibo\\library\\security\\authenticator\\Authenticator';

    /**
     * Class name of the security model interface
     * @var string
     */
    const INTERFACE_SECURITY_MODEL = 'zibo\\library\\security\\model\\SecurityModel';

    /**
     * Name of the username field
     * @var string
     */
    const USERNAME = 'username';

    /**
     * Name of the password field
     * @var unknown_type
     */
    const PASSWORD = 'password';

    /**
     * The instance of the security manager
     * @var SecurityManager
     */
    private static $instance;

    /**
     * The authenticator which is being used
     * @var zibo\library\security\authenticator\Authenticator
     */
    private $authenticator;

    /**
     * The security model which is being used
     * @var zibo\library\security\model\SecurityModel
     */
    private $model;

    /**
     * The hash algorithm used to hash the passwords
     * @var zibo\library\encryption\hash\HashAlgorithm
     */
    private $hashAlgorithm;

    /**
     * Matcher for a route against route regular expressions
     * @var zibo\library\security\model\RouteMatcher
     */
    private $routeMatcher;

    /**
     * Flag to see if we are in CLI
     * @var boolean
     */
    private $isCli;

    /**
     * Constructs a new security manager
     * @return null
     */
    private function __construct() {
        $zibo = Zibo::getInstance();
        $objectFactory = new ObjectFactory();

        $this->initializeSecurityModel($zibo, $objectFactory);
        $this->initializeAuthenticator($zibo, $objectFactory);
        $this->initializeHashAlgorithm($zibo, $objectFactory);

        $this->routeMatcher = new RouteMatcher();

        $environment = Environment::getInstance()->getName();
        $this->isCli = $environment == CliEnvironment::NAME;
    }

    /**
     * Initializes the authenticator from the Zibo configuration
     * @param zibo\core\Zibo $zibo The Zibo instance
     * @param zibo\library\ObjectFactory $objectFactory Instance of an object factory
     * @return null
     */
    private function initializeAuthenticator(Zibo $zibo, ObjectFactory $objectFactory) {
        $authenticatorClass = $zibo->getConfigValue(self::CONFIG_AUTHENTICATOR, self::DEFAULT_AUTHENTICATOR);
        if (!$authenticatorClass) {
            throw new ZiboException('No authenticator set');
        }

        $this->authenticator = $objectFactory->create($authenticatorClass, self::INTERFACE_AUTHENTICATOR);
        $this->authenticator->setSecurityModel($this->model);
    }

    /**
     * Initializes the security model from the Zibo configuration
     * @param zibo\core\Zibo $zibo The Zibo instance
     * @param zibo\library\ObjectFactory $objectFactory Instance of an object factory
     * @return null
     */
    private function initializeSecurityModel(Zibo $zibo, ObjectFactory $objectFactory) {
        $modelClass = $zibo->getConfigValue(self::CONFIG_SECURITY_MODEL);
        if (!$modelClass) {
            return;
        }

        try {
            $this->model = $objectFactory->create($modelClass, self::INTERFACE_SECURITY_MODEL);
            $zibo->runEvent(Zibo::EVENT_LOG, 'Using security model ' . $modelClass, '', 0, self::LOG_NAME);
        } catch (Exception $e) {
            $zibo->runEvent(Zibo::EVENT_LOG, 'Could not create security model ' . $modelClass, $e, 1, self::LOG_NAME);
        }
    }

    /**
     * Initializes the hash algorithm from the Zibo configuration
     * @param zibo\core\Zibo $zibo The Zibo instance
     * @param zibo\library\ObjectFactory $objectFactory Instance of an object factory
     * @return null
     */
    private function initializeHashAlgorithm(Zibo $zibo, ObjectFactory $objectFactory) {
        $hashAlgorithms = $zibo->getConfigValue(EncryptionModule::CONFIG_HASH_ALGORITHM);

        $hashAlgorithmName = $zibo->getConfigValue(self::CONFIG_HASH_ALGORITHM, self::DEFAULT_HASH_ALGORITHM);

        if (!array_key_exists($hashAlgorithmName, $hashAlgorithms)) {
            throw new ZiboException('Provided password hash algorithm ' . $hashAlgorithmName . ' could not be found');
        }

        $hashAlgorithmClass = $hashAlgorithms[$hashAlgorithmName];

        $this->hashAlgorithm = $objectFactory->create($hashAlgorithmClass, EncryptionModule::INTERFACE_HASH_ALGORITHM);
    }

    /**
     * Gets the instance of the SecurityManager
     * @return SecurityManager
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Hashes the provided password with the hash algorithm of the security manager
     * @param string $password Plain text password
     * @return string Hash value of the password
     */
    public function hashPassword($password) {
        return $this->hashAlgorithm->hashString($password);
    }

    /**
     * Sets the security model
     * @param zibo\library\security\model\SecurityModel $model Security model to use
     * @return null
     */
    public function setSecurityModel(SecurityModel $model = null) {
        $this->model = $model;
    }

    /**
     * Gets the security model which is currently in use
     * @param boolean $throwException Set to true to throw an exception when no security model has been set
     * @return zibo\library\security\model\SecurityModel|null
     * @throws zibo\library\security\exception\SecurityModelNotSetException when $throwException is set to true and no security model has been set
     */
    public function getSecurityModel($throwException = false) {
        if ($throwException && !$this->model) {
            throw new SecurityModelNotSetException();
        }

        return $this->model;
    }

    /**
     * Gets the authenticator which is currently in use
     * @return zibo\library\security\authenticator\Authenticator
     */
    public function getAuthenticator() {
        return $this->authenticator;
    }

    /**
     * Get the current user
     * @return User current user if logged in, null otherwise
     */
    public function getUser() {
        if (!$this->model) {
            return null;
        }

        return $this->authenticator->getUser();
    }

    /**
     * Saves a user
     * @param zibo\library\security\model\User $user The user to save
     * @return null
     */
    public function setUser(User $user) {
        if (!$this->model) {
            throw new SecurityModelNotSetException();
        }

        $this->model->setUser($user);
    }

    /**
     * Switch the current user
     * @param string $username Username to switch
     * @return null
     * @throws zibo\library\security\exception\UnauthorizedException when not authenticated
     * @throws zibo\library\security\exception\UserNotFoundException when the requested user could not be found
     */
    public function switchUser($username) {
        $this->authenticator->switchUser($username);
    }

    /**
     * Login a user
     * @param string $username
     * @param string $password
     * @return zibo\library\security\model\User|null The user if the login succeeded
     * @throws zibo\library\security\exception\AuthenticationException when the user could not be authenticated
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function login($username, $password) {
        $user = $this->authenticator->login($username, $password);

        Zibo::getInstance()->runEvent(self::EVENT_LOGIN, $user);

        return $user;
    }

    /**
     * Logout the current user
     * @return null
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function logout() {
        $this->authenticator->logout();
    }

    /**
     * Gets the number of current visitors. This number includes the current users.
     * @return integer
     */
    public function getNumVisitors() {
        return $this->authenticator->getNumVisitors();
    }

    /**
     * Gets the number of current users.
     * @return integer
     */
    public function getNumUsers() {
        return $this->authenticator->getNumUsers();
    }

    /**
     * Gets the usernames of the current users
     * @return array Array with usernames
     */
    public function getCurrentUsers() {
        return $this->authenticator->getCurrentUsers();
    }

    /**
     * Check whether the current user is allowed to pass the given permission
     * @param string $code Code of the permission
     * @return boolean
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function isPermissionAllowed($code) {
        if (!$this->model || $this->isCli) {
            return true;
        }

        if (!$this->hasPermission($code)) {
            $this->registerPermission($code);
        }

        $user = $this->getUser();

        if ($user == null) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Permission ' . $code . ' is not allowed', 'not authenticated', 0, self::LOG_NAME);
            return false;
        }

        if ($user->isPermissionAllowed($code)) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Permission ' . $code . ' is allowed for user ' . $user->getUsername(), '', 0, self::LOG_NAME);
            return true;
        }

        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Permission ' . $code . ' is not allowed', '', 0, self::LOG_NAME);
        return false;
    }

    /**
     * Check whether the current user is allowed to view the given route
     * @param string $route Route of the page
     * @return boolean
     */
    public function isRouteAllowed($route) {
        if (!$this->model || $this->isCli) {
            return true;
        }

        $route = ltrim($route, Request::QUERY_SEPARATOR);

        $allowed = !$this->routeMatcher->matchRoute($route, $this->getDeniedRoutes());
        if ($allowed) {
            return true;
        }

        $user = $this->getUser();
        if ($user != null && $user->isRouteAllowed($route)) {
            return true;
        }

        return false;
    }

    /**
     * Get the routes which are denied globally
     * @return array Array with a denied route per element
     */
    public function getDeniedRoutes() {
        $model = $this->getSecurityModel(true);
        return $model->getDeniedRoutes();
    }

    /**
     * Set the denied routes globally
     * @param array $routes Array with a denied route per element
     * @return null
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function setDeniedRoutes(array $routes) {
        foreach ($routes as $index => $route) {
            try {
                if (String::isEmpty($route)) {
                    unset($routes[$index]);
                } else {
                    $route = trim($route);
                    if (String::isEmpty($route)) {
                        unset($routes[$index]);
                    }
                }
            } catch (ZiboException $e) {
                unset($routes[$index]);
            }
        }

        $model = $this->getSecurityModel(true);
        $model->setDeniedRoutes($routes);
    }

    /**
     * Gets all the permissions
     * @return array Array with Permission objects
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function getPermissions() {
        $model = $this->getSecurityModel(true);
        return $model->getPermissions();
    }

    /**
     * Checks whether a permission exists
     * @param string $code Code ot the permission
     * @return boolean True if the permission is available in the model, false otherwise
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function hasPermission($code) {
        $model = $this->getSecurityModel(true);
        return $model->hasPermission($code);
    }

    /**
     * Registers a permission to the security model
     * @param string $code Code of the permission
     * @return null
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function registerPermission($code) {
        $model = $this->getSecurityModel(true);
        $model->registerPermission($code);
    }

    /**
     * Unregisters an existing permission from the security model
     * @param string $code Code of the permission
     * @return null
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function unregisterPermission($code) {
        $model = $this->getSecurityModel(true);
        $model->unregisterPermission($code);
    }

    /**
     * Get all the roles
     * @return array Array with Role objects
     */
    public function getRoles() {
        return $this->getSecurityModel()->getRoles();
    }

    /**
     * Set the allowed routes to a role
     * @param Role $role Role to assign the allowed routes to
     * @param array $routes Array with a allowed route per element
     * @return null
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function setAllowedRoutesToRole(Role $role, array $routes) {
        $model = $this->getSecurityModel(true);
        $model->setAllowedRoutesToRole($role, $routes);
    }

    /**
     * Set the allowed permissions to a role
     * @param Role $role Role to assign the allowed permissions to
     * @param array $permissions Array with a permission code per element
     * @return null
     * @throws zibo\library\security\exception\SecurityModelNotSetException when no security model has been set
     */
    public function setAllowedPermissionsToRole(Role $role, array $permissionCodes) {
        $model = $this->getSecurityModel(true);
        $model->setAllowedPermissionsToRole($role, $permissionCodes);
    }

}