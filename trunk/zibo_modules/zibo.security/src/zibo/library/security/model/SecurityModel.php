<?php

namespace zibo\library\security\model;

/**
 * Model of the security data
 */
interface SecurityModel {

    /**
     * Creates a new user
     * @return User
     */
    public function createUser();

    /**
     * Gets a user by it's username
     * @param string $username Username of the user
     * @return User|null User object if found, null otherwise
     */
    public function getUserByUsername($username);

    /**
     * Gets a user by it's email address
     * @param string $email Email address of the user
     * @return User|null User object if found, null otherwise
     */
    public function getUserByEmail($email);

    /**
     * Find the users which match the provided part of a username
     * @param string $query Part of a username to match
     * @return array Array with the usernames which match the provided query
     */
    public function findUsersByUsername($query);

    /**
     * Find the users which match the provided part of a email address
     * @param string $query Part of a email address
     * @return array Array with the usernames of the users which match the provided query
     */
    public function findUsersByEmail($query);

    /**
     * Saves a user
     * @param User $user The user to save
     * @return null
     */
    public function setUser(User $user);

    /**
     * Saves the provided roles for the provided user
     * @param User $user The user to update
     * @param array $roles The roles to set to the user
     * @return null
     */
    public function setRolesToUser(User $user, array $roles);

    /**
     * Deletes the provided user
     * @param User $user The user to delete
     * @return null
     */
    public function deleteUser(User $user);

    /**
     * Gets the routes which are denied globally
     * @return array Array with a route per element
     */
    public function getDeniedRoutes();

    /**
     * Sets the routes which are denied globally
     * @param array $routes Array with a route per element
     * @return null
     */
    public function setDeniedRoutes(array $routes);

    /**
     * Gets all the roles
     * @return array Array with Role objects
     */
    public function getRoles();

    /**
     * Sets the allowed permissions to a role
     * @param Role $role Role to set the permissions to
     * @param array $permissionCodes Array with a permission code per element
     * @return null
     */
    public function setAllowedPermissionsToRole(Role $role, array $permissionCodes);

    /**
     * Sets the allowed routes to a role
     * @param Role $role Role to set the routes to
     * @param array $routes Array with a route per element
     * @return null
     */
    public function setAllowedRoutesToRole(Role $role, array $routes);

    /**
     * Gets all the permissions
     * @return array Array with Permission objects
     */
    public function getPermissions();

    /**
     * Checks whether a given permission is available
     * @param string $code Code of the permission to check
     * @return boolean
     */
    public function hasPermission($code);

    /**
     * Registers a new permission to the model
     * @param string $code Code of the permission
     * @return null
     */
    public function registerPermission($code);

    /**
     * Unregisters an existing permission from the model
     * @param string $code Code of the permission
     * @return null
     */
    public function unregisterPermission($code);

}