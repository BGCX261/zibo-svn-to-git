<?php

namespace zibo\library\security\model;

/**
 * User of the SecurityModel
 */
interface User {

    /**
     * Event run when a user updates it's password. The event has the user object and the plain password as arguments.
     * @var string
     */
    const EVENT_PASSWORD_UPDATE = 'security.password.update';

    /**
     * Gets the unique id of this user
     * @return string
     */
    public function getUserId();

    /**
     * Gets the name of this user
     * @return string
     */
    public function getUserName();

    /**
     * Gets the password of this user
     * @return string Encrypted password
     */
    public function getUserPassword();

    /**
     * Sets a new password for this user
     *
     * This method will run the security.password.update event before setting the password. This event
     * has the User object and the new plain password as arguments.
     * @param string $password Plain text password
     * @return null
     * @see SecurityModel
     */
    public function setUserPassword($password);

    /**
     * Gets the email address of this user
     * @return string
     */
    public function getUserEmail();

    /**
     * Sets the email address of this user
     * @param string $email
     * @return
     */
    public function setUserEmail($email);

    /**
     * Gets whether this user is active
     * @return boolean
     */
    public function isUserActive();

    /**
     * Sets whether this user is active
     * @param boolean $flag
     * @return null
     */
    public function setIsUserActive($flag);

    /**
     * Checks whether a permission is allowed for this user
     * @param string $code Code of the permission to check
     * @return boolean True if permission is allowed, false otherwise
     * @see SecurityManager::ASTERIX
     */
    public function isPermissionAllowed($code);

    /**
     * Checks whether a route is allowed for this user
     * @param string $route Route to check
     * @return boolean True if the route is allowed, false otherwise
     * @see SecurityManager::ASTERIX, zibo\library\security\model\RouteMatcher
     */
    public function isRouteAllowed($route);

    /**
     * Gets the roles of this user
     * @return array Array of Role objects
     */
    public function getUserRoles();

    /**
     * Gets all the preferences of this user
     * @return array Array with the name of the setting as key and the setting as value
     */
    public function getUserPreferences();

    /**
     * Gets a preference of this user
     * @param string $name Name of the preference
     * @param mixed $default Default value for when the preference is not set
     * @return mixed The value of the preference or the provided default value if the preference is not set
     */
    public function getUserPreference($name, $default = null);

    /**
     * Sets a preference for this user
     * @param string $name Name of the preference
     * @param mixed $value Value for the preference
     * @return null
     */
    public function setUserPreference($name, $value);

}