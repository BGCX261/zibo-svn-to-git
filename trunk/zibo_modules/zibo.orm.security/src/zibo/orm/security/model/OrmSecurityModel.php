<?php

namespace zibo\orm\security\model;

use zibo\core\Zibo;

use zibo\library\orm\ModelManager;
use zibo\library\security\model\Role;
use zibo\library\security\model\User;
use zibo\library\security\model\SecurityModel;
use zibo\library\security\SecurityManager;

/**
 * Orm implementation of the security model
 */
class OrmSecurityModel implements SecurityModel {

    /**
     * Loaded denied routes
     * @var array|boolean
     */
    private $deniedRoutes = false;

    /**
     * Loaded roles
     * @var array|boolean
     */
    private $roles = false;

    /**
     * Loaded permissions
     * @var array|boolean
     */
    private $permissions = false;

    /**
     * Constructs a new orm security model
     * @return null
     */
    public function __construct() {
        // check connection, we need to work when constructed or the UI is in trouble...
        $this->getDeniedRoutes();

        Zibo::getInstance()->registerEventListener(SecurityManager::EVENT_LOGIN, array($this, 'onLogin'));
    }

    /**
     * Event listener to save the last access date and ip address of a user
     * @param zibo\library\security\model\User $user User who is logging in
     * @return null
     */
    public function onLogin(User $user) {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);

        $saveUser = $userModel->createData(false);

        $saveUser->id = $user->id;
        $saveUser->version = $user->version;
        $saveUser->dateLastLogin = time();
        $saveUser->lastIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        $userModel->save($saveUser);

        $user->dateLastLogin = $saveUser->dateLastLogin;
        $user->lastIp = $saveUser->lastIp;
        $user->version = $saveUser->version;
    }

    /**
     * Creates a new user
     * @return User
     */
    public function createUser() {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        return $userModel->createData();
    }

    /**
     * Gets a user by it's username
     * @param string $username Username
     * @return zibo\orm\security\model\data\UserData|null User object if found, null otherwise
     */
    public function getUserByUsername($username) {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        return $userModel->getUserByUsername($username);
    }

    /**
     * Gets a user by it's email address
     * @param string $email Email address of the user
     * @return User|null User object if found, null otherwise
     */
    public function getUserByEmail($email) {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        return $userModel->getUserByEmail($email);
    }

    /**
     * Find the users which match the provided part of a username
     * @param string $query Part of a username to match
     * @return array Array with the usernames which match the provided query
     */
    public function findUsersByUsername($query) {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        return $userModel->findUsersByUsername($query);
    }

    /**
     * Find the users which match the provided part of a email address
     * @param string $query Part of a email address
     * @return array Array with the usernames of the users which match the provided query
     */
    public function findUsersByEmail($query) {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        return $userModel->findUsersByEmail($query);
    }

    /**
     * Save a user
     * @param zibo\library\security\model\User $user
     * @return null
     */
    public function setUser(User $user) {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        $userModel->save($user);
    }

    /**
     * Saves the provided roles for the provided user
     * @param User $user The user to update
     * @param array $roles The roles to set to the user
     * @return null
     */
    public function setRolesToUser(User $user, array $roles) {
        $user->roles = $roles;

        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        $userModel->save($user, 'roles');
    }

    /**
     * Deletes the provided user
     * @param User $user The user to delete
     * @return null
     */
    public function deleteUser(User $user) {
        $userModel = ModelManager::getInstance()->getModel(UserModel::NAME);
        $userModel->delete($user);
    }

    /**
     * Gets the globally denied routes
     * @return array Array with a route per element
     */
    public function getDeniedRoutes() {
        if ($this->deniedRoutes !== false) {
            return $this->deniedRoutes;
        }

        $routeModel = ModelManager::getInstance()->getModel(RouteModel::NAME);
        $this->deniedRoutes = $routeModel->getDeniedRoutes();

        return $this->deniedRoutes;
    }

    /**
     * Set the globally denied routes
     * @param array $routes Array with a route per element
     * @return null
     */
    public function setDeniedRoutes(array $routes) {
        $routeModel = ModelManager::getInstance()->getModel(RouteModel::NAME);
        $routeModel->setDeniedRoutes($routes);

        $this->deniedRoutes = $routes;
    }

    /**
     * Gets all the roles
     * @return array Array with Role objects
     */
    public function getRoles() {
        if ($this->roles !== false) {
            return $this->roles;
        }

        $roleModel = ModelManager::getInstance()->getModel(RoleModel::NAME);
        $this->roles = $roleModel->getRoles();

        return $this->roles;
    }

    /**
     * Gets all the permissions
     * @return array Array with Permission objects
     */
    public function getPermissions() {
        if ($this->permissions !== false) {
            return $this->permissions;
        }

        $permissionModel = ModelManager::getInstance()->getModel(PermissionModel::NAME);
        $this->permissions = $permissionModel->getPermissions();

        return $this->permissions;
    }

    /**
     * Check if the given permission exists in the model
     * @param string $code Code of the permission to check
     * @return boolean True if it exists, false otherwise
     */
    public function hasPermission($code) {
        $permissions = $this->getPermissions();
        return array_key_exists($code, $permissions);
    }

    /**
     * Set the allowed routes to a Role
     * @param zibo\library\security\model\Role $role Role to set the routes to
     * @param array $routes Array with a route per element
     * @return null
     */
    public function setAllowedRoutesToRole(Role $role, array $routes) {
        $roleModel = ModelManager::getInstance()->getModel(RoleModel::NAME);
        $roleModel->setAllowedRoutesToRole($role, $routes);

        $this->roles[$role->id] = $role;
    }

    /**
     * Set the allowed permissions to a Role
     * @param zibo\library\security\model\Role $role Role to set the permissions to
     * @param array $permissions Array with a permission code per element
     * @return null
     */
    public function setAllowedPermissionsToRole(Role $role, array $permissions) {
        $role->permissions = array();
        foreach ($permissions as $code) {
            if (isset($this->permissions[$code])) {
                $role->permissions[$code] = $this->permissions[$code];
            }
        }

        $roleModel = ModelManager::getInstance()->getModel(RoleModel::NAME);
        $roleModel->save($role, 'permissions');

        $this->roles[$role->id] = $role;
    }

    /**
     * Adds a permission to the model if it does not exist
     * @param string $code Code of the permission to register
     * @return null
     */
    public function registerPermission($code) {
        if ($this->hasPermission($code)) {
            return;
        }

        $permissionModel = ModelManager::getInstance()->getModel(PermissionModel::NAME);

        $permission = $permissionModel->createData();
        $permission->code = $code;
        $permission->description = $code;

        $permissionModel->save($permission);

        $this->permissions[$code] = $permission;
    }

    /**
     * Removes a permission from the model
     * @param string $code Code of the permission to remove
     * @return null
     */
    public function unregisterPermission($code) {
        if (!$this->hasPermission($code)) {
            return;
        }

        $permission = $this->permissions[$code];

        $permissionModel = ModelManager::getInstance()->getModel(PermissionModel::NAME);
        $permissionModel->delete($permission);

        unset($this->permissions[$code]);
    }

}