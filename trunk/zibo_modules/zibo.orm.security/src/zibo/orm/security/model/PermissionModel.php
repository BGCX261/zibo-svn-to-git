<?php

namespace zibo\orm\security\model;

use zibo\library\orm\model\SimpleModel;

/**
 * Permission model
 */
class PermissionModel extends SimpleModel {

    /**
     * Name of the model
     * @var string
     */
    const NAME = 'Permission';

    /**
     * Gets a list of permissions
     * @param string $locale
     * @return array Array with the id of the permission as key and the code of the permission as value
     */
    public function getDataList($locale = null) {
        $list = array();

        $query = $this->createQuery(0);
        $query->setFields('{id}, {code}');
        $query->addOrderBy('{code} ASC');

        $result = $query->query();
        foreach ($result as $permission) {
            $list[$permission->id] = $permission->code;
        }

        return $list;
    }

    /**
     * Gets all the permissions
     * @return array Array with PermissionData objects as value and the code as key
     */
    public function getPermissions() {
        $query = $this->createQuery(0);
        $query->addOrderBy('{code} ASC');

        $result = $query->query();

        $permissions = array();
        foreach ($result as $permission) {
            $permissions[$permission->getPermissionCode()] = $permission;
        }

        return $permissions;
    }

}