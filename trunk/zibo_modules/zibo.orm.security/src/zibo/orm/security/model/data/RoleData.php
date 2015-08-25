<?php

namespace zibo\orm\security\model\data;

use zibo\library\orm\model\data\Data;
use zibo\library\security\model\Role;
use zibo\library\security\SecurityManager;

/**
 * Role data container
 */
class RoleData extends Data implements Role {

    /**
     * Name of the role
     * @var string
     */
    public $name;

    /**
     * Permissions of this role
     * @var array
     */
    public $permissions;

    /**
     * Allowed routes (RouteData) for this role
     * @var array
     */
    public $routes;

    /**
     * Flag to see if this role is a super role
     * @var boolean
     */
    public $isSuperRole;

    /**
     * Allowed routes (string) for this role
     * @var array
     */
    private $roleRoutes = false;

    /**
    * Gets the id of this role
    * @return integer
    */
    public function getRoleId() {
    	return $this->id;
    }

    /**
     * Gets the name of this role
     * @return string
     */
    public function getRoleName() {
        return $this->name;
    }

    /**
     * Gets the permissions of this role
     * @return array
     */
    public function getRolePermissions() {
        return $this->permissions;
    }

    /**
     * Gets the allowed routes for this role
     * @return array Array with a route string as value
     */
    public function getRoleRoutes() {
        if ($this->roleRoutes !== false) {
            return $this->roleRoutes;
        }

        $this->roleRoutes = array();
        if ($this->routes) {
            foreach ($this->routes as $route) {
                $this->roleRoutes[] = $route->route;
            }
        }

        return $this->roleRoutes;
    }

    /**
     * Check whether this role allows the provided permission
     * @param string $code the permission code to check
     * @return boolean true if the permission is allowed, false if the permission is denied
     */
    public function isPermissionAllowed($code) {
        if ($this->isSuperRole) {
            return true;
        }

        if (!$this->permissions) {
            return false;
        }

        foreach ($this->permissions as $permission) {
            if ($permission->code == $code || $permission->code == SecurityManager::ASTERIX) {
                return true;
            }
        }

        return false;
    }

}