<?php

namespace zibo\orm\security\model\data;

use zibo\core\Zibo;

use zibo\library\orm\model\data\Data;
use zibo\library\orm\ModelManager;
use zibo\library\security\model\RouteMatcher;
use zibo\library\security\model\User;
use zibo\library\security\SecurityManager;

/**
 * User data container
 */
class UserData extends Data implements User {

    /**
     * Username of this user
     * @var string
     */
    public $username;

    /**
     * Hashed password of this user
     * @var string
     */
    public $password;

    /**
     * Email address of this user
     * @var string
     */
    public $email;

    /**
     * Array with Role objects
     * @var array
     */
    public $roles;

    /**
     * Flag to see whether this user is active
     * @var boolean
     */
    public $isActive;

    /**
     * Internal flag to see if this user is a superuser
     * @var boolean|null
     */
    private $isSuperUser;

    /**
     * Array with UserPreference objects
     * @var array
     */
    public $preferences;

    /**
     * IP-address from the last login
     * @var string
     */
    public $lastIp;

    /**
     * Timestamp of the last login
     * @var integer
     */
    public $dateLastLogin;

    /**
     * Array with all the permissions of the roles
     * @var array
     */
    private $permissions;

    /**
     * Array with all the routes of the roles
     * @var array
     */
    private $routes;

    /**
     * Route matcher to check for allowed routes
     * @var zibo\library\security\model\RouteMatcher
     */
    private $routeMatcher;

    /**
     * Gets the user id of this user
     * @return integer
     */
    public function getUserId() {
        return $this->id;
    }

    /**
     * Gets the username of this user
     * @return string
     */
    public function getUserName() {
        return $this->username;
    }

    /**
     * Sets the username of this user
     * @param string $username The new username for this user
     * @return null
     */
    public function setUserName($username) {
        $this->username = $username;
    }

    /**
     * Gets the password for this user
     * @return string Hashed password
     */
    public function getUserPassword() {
        return $this->password;
    }

    /**
     * Sets a new password for this user
     *
     * This method will run the security.password.update event before setting the password. This event
     * has the user object and the new plain text password as arguments.
     * @param string $password Plain text password
     * @return null
     */
    public function setUserPassword($password) {
        Zibo::getInstance()->runEvent(User::EVENT_PASSWORD_UPDATE, $this, $password);

        $this->password = SecurityManager::getInstance()->hashPassword($password);
    }

    /**
     * Gets the email address of this user
     * @return string
     */
    public function getUserEmail() {
        return $this->email;
    }

    /**
     * Sets the email address for this user
     * @param string $email Email address for this user
     * @return null
     */
    public function setUserEmail($email) {
        $this->email = $email;
    }

    /**
     * Checks whether this user is a super user
     * @return boolean True if the user is a super user, false otherwise
     */
    public function isSuperUser() {
        if ($this->isSuperUser !== null) {
            return $this->isSuperUser;
        }

        foreach ($this->roles as $role) {
            if ($role->isSuperRole) {
                $this->isSuperUser = true;
            }
        }

        if ($this->isSuperUser === null) {
            $this->isSuperUser = false;
        }

        return $this->isSuperUser;
    }

    /**
     * Gets whether this user is active
     * @return boolean True when active, false otherwise
     */
    public function isUserActive() {
        return $this->isActive;
    }

    /**
     * Sets whether this user is active
     * @param boolean $flag True to activate, false to deactivate
     * @return null
     */
    public function setIsUserActive($flag) {
        $this->isActive = $flag;
    }

    /**
     * Checks if the provided permission is allowed by a role
     * @param string $code Code of the permission
     * @return boolean True if the permission is allowed, false otherwise
     */
    public function isPermissionAllowed($code) {
        if ($this->isSuperUser()) {
            return true;
        }

        if (!isset($this->permissions)) {
            $this->initializePermissions();
        }

        if (array_key_exists($code, $this->permissions)) {
            return true;
        }

        return false;
    }

    /**
     * Initializes the permissions for a quicker permission check
     * @return null
     */
    public function initializePermissions() {
        $this->permissions = array();

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                $this->permissions[$permission->code] = true;
            }
        }
    }

    /**
     * Checks if the provided route matches a allowed route of the roles
     * @param string $route Route to match
     * @return boolean True if a match is found, false otherwise
     */
    public function isRouteAllowed($route) {
        if ($this->isSuperUser()) {
            return true;
        }

        if (!isset($this->routes)) {
            $this->initializeRoutes();
        }

        if ($this->routeMatcher->matchRoute($route, $this->routes)) {
            return true;
        }

        return false;
    }

    /**
     * Initializes the routes for a quicker route check
     * @return null
     */
    public function initializeRoutes() {
        $this->routes = array();

        foreach ($this->roles as $role) {
            foreach ($role->routes as $roleRoute) {
                $this->routes[$roleRoute->route] = true;
            }
        }

        $this->routes = array_keys($this->routes);

        $this->routeMatcher = new RouteMatcher();
    }

    /**
     * Gets the roles of this user
     * @return array Array with Role objects
     */
    public function getUserRoles() {
        return $this->roles;
    }

    /**
     * Gets all the user preferences
     * @return array Array with the name of the preference as key and the preference as value
     */
    public function getUserPreferences() {
        $preferences = array();

        foreach ($this->preferences as $preference) {
            $preferences[$preference->name] = unserialize($preference->value);
        }

        return $preferences;
    }

    /**
     * Gets a preference of this user
     * @param string $name Name of the preference
     * @param mixed $default Default value for when the preference is not set
     * @return mixed Value for the preference if set, the provided default value otherwise
     */
    public function getUserPreference($name, $default = null) {
        if (!array_key_exists($name, $this->preferences)) {
            return $default;
        }

        return unserialize($this->preferences[$name]->value);
    }

    /**
     * Sets a preference for this user
     * @param string $name Name of the preference
     * @param mixed $value Value of the preference
     * @return null
     */
    public function setUserPreference($name, $value) {
        if (!array_key_exists($name, $this->preferences)) {
            $preferenceModel = ModelManager::getInstance()->getModel(UserPreferenceModel::NAME);

            $preference = $preferenceModel->createData();
            $preference->name = $name;
            $preference->value = serialize($value);

            $this->preferences[$name] = $preference;
        } else {
            $this->preferences[$name]->value = serialize($value);
        }
    }

}