<?php

namespace zibo\orm\security\model;

use zibo\library\orm\model\ExtendedModel;

use zibo\orm\security\model\data\RoleData;

/**
 * Role model
 */
class RoleModel extends ExtendedModel {

    /**
     * Name of this model
     * @var string
     */
    const NAME = 'Role';

    /**
     * Gets a list of roles
     * @param string $locale
     * @return array Array with the id of the role as key and the name of the role as value
     */
    public function getDataList($locale = null) {
        $list = array();

        $query = $this->createQuery(0, $locale);
        $query->setFields('{id}, {name}, {isSuperRole}');
        $query->addOrderBy('{name} ASC');

        $result = $query->query();
        foreach ($result as $role) {
            $list[$role->id] = $role->name . ($role->isSuperRole ? ' [S]' : '');
        }

        return $list;
    }

    /**
     * Gets all the roles
     * @return array Array with RoleData objects
     */
    public function getRoles() {
        $query = $this->createQuery();
        $query->addOrderBy('{name} ASC');
        return $query->query();
    }

    /**
     * Gets all the super roles
     * @param integer $recursiveDepth
     * @return array Array with RoleData objects
     */
    public function getSuperRoles($recursiveDepth = 0) {
        $query = $this->createQuery($recursiveDepth);
        $query->addCondition('{isSuperRole} = 1');
        $query->addOrderBy('{name} ASC');
        return $query->query();
    }

    /**
     * Sets the allowed routes for the provided role
     * @param zibo\orm\security\model\data\RoleData $role Role for the provided routes
     * @param array $routes Array with a route string per element
     * @return null
     */
    public function setAllowedRoutesToRole(RoleData $role, array $routes) {
        $routeModel = $this->getModel(RouteModel::NAME);

        $role->routes = $routeModel->getRoutesFromArray($routes);

        $this->save($role, 'routes');
    }

}