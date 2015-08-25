<?php

namespace zibo\library\security\model;

/**
 * Role of the SecurityModel
 */
interface Role {

    /**
     * Gets the id of this role
     * @return integer
     */
    public function getRoleId();

    /**
     * Gets the name of this role
     * @return string
     */
    public function getRoleName();

    /**
     * Gets the permissions of this role
     * @return array Array with Permission objects
     */
    public function getRolePermissions();

    /**
     * Gets the allowed routes of this Role
     * @return array Array with a route(regex) per element
     */
    public function getRoleRoutes();

    /**
     * Checks whether a permission is allowed for this role
     * @param string $code Code of the permission to check
     * @return boolean True if permission is allowed, false otherwise
     * @see SecurityManager::ASTERIX
     */
    public function isPermissionAllowed($code);

}