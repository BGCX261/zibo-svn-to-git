<?php

namespace zibo\admin\table;

use zibo\admin\table\decorator\security\PermissionDecorator;
use zibo\admin\table\decorator\security\PermissionOptionDecorator;
use zibo\admin\table\decorator\security\RoleHeaderDecorator;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;
use zibo\library\security\SecurityManager;
use zibo\library\validation\filter\TrimLinesFilter;

/**
 * Table to show the an overview of the roles with their assigned permissions
 */
class SecurityTable extends ExtendedTable {

    /**
     * Name of the form of the table
     * @var string
     */
    const FORM_NAME = 'formSecurity';

    /**
     * Name of the roles field
     * @var string
     */
    const FIELD_ROLES = 'roles';

    /**
     * Name of the allowed routes field
     * @var string
     */
    const FIELD_ALLOWED_ROUTES = 'allowedRoutes';

    /**
     * Name of the denied routes field
     * @var unknown_type
     */
    const FIELD_DENIED_ROUTES = 'deniedRoutes';

    /**
     * Name of the save button
     * @var string
     */
    const FIELD_SAVE = 'save';

    /**
     * Translation key for the save button
     * @var string
     */
    const TRANSLATION_SAVE = 'button.save';

    /**
     * Array with the name of the role as key and a Role object as value
     * @var array
     */
    private $roles;

    /**
     * Constructs a new security table
     * @param string $formAction URL where the form of the table will point to
     * @return null
     */
    public function __construct($formAction) {
        $manager = SecurityManager::getInstance();

        $permissions = $manager->getPermissions();
        $roles = $manager->getRoles();
        $deniedRoutes = $manager->getDeniedRoutes();

        parent::__construct($permissions, $formAction, self::FORM_NAME);

        $this->addDecorators($roles);
        $this->addFields($deniedRoutes);
    }

    /**
     * Adds the decorators to the table
     * @param array $roles Array with a Role object as value
     * @return null
     */
    private function addDecorators(array $roles) {
        $this->roles = array();

        $this->addDecorator(new ZebraDecorator(new PermissionDecorator()));

        foreach ($roles as $role) {
            $permissionOptionDecorator = new PermissionOptionDecorator($role, self::FIELD_ROLES);

            $this->addDecorator($permissionOptionDecorator, new RoleHeaderDecorator($role));
            $this->form->addField($permissionOptionDecorator->createField());

            $this->roles[$role->getRoleName()] = $role;
        }
    }

    /**
     * Adds the routes fields and a save button to the form of the table
     * @param array $deniedRoutes Array with a route as value
     * @return null
     */
    private function addFields(array $deniedRoutes) {
        $factory = FieldFactory::getInstance();
        $trimFilter = new TrimLinesFilter();

        foreach ($this->roles as $roleName => $role) {
            $name = $this->getAllowedRoutesFieldName($roleName);
            $value = $this->getRoutesString($role->getRoleRoutes());

            $allowedRoutesField = $factory->createField(FieldFactory::TYPE_TEXT, $name, $value);
            $allowedRoutesField->addFilter($trimFilter);

            $this->form->addField($allowedRoutesField);
        }

        $value = $this->getRoutesString($deniedRoutes);

        $deniedRoutesField = $factory->createField(FieldFactory::TYPE_TEXT, self::FIELD_DENIED_ROUTES, $value);
        $deniedRoutesField->addFilter($trimFilter);

        $this->form->addField($deniedRoutesField);

        $this->form->addField($factory->createSubmitField(self::FIELD_SAVE, self::TRANSLATION_SAVE));
    }

    /**
     * Gets all the roles
     * @return array Array with the name of the role as key and the Role object as value
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * Gets the submitted permissions for a role
     * @param string $roleName Name of the role
     * @return array Array with the permission code as value
     */
    public function getPermissions($roleName) {
        $name = self::FIELD_ROLES . '['. $roleName . ']';
        return $this->form->getValue($name);
    }

    /**
     * Gets the submitted allowed routes for a role
     * @param string $roleName Name of the role
     * @return array Array with a route as value
     */
    public function getAllowedRoutes($roleName) {
        $name = $this->getAllowedRoutesFieldName($roleName);
        return $this->getFormRoutes($name);
    }

    /**
     * Gets the submitted denied routes
     * @return array Array with a route as value
     */
    public function getDeniedRoutes() {
        return $this->getFormRoutes(self::FIELD_DENIED_ROUTES);
    }

    /**
     * Gets the routes for the provided field name
     * @param string $fieldName Name of the routes field
     * @return array Array with a route as value
     */
    private function getFormRoutes($fieldName) {
        $routes = $this->form->getValue($fieldName);

        if (empty($routes)) {
            return array();
        }

        return explode("\n", $routes);
    }

    /**
     * Gets a route string for an array of routes
     * @param array $routes Array with a route as value
     * @return string
     */
    private function getRoutesString(array $routes) {
        sort($routes);

        $string = '';
        foreach ($routes as $route) {
            if (empty($route)) {
                continue;
            }

            $string .= ($string ? "\n" : '') . $route;
        }

        return $string;
    }

    /**
     * Gets the field name of the allowed routes field for the provided role
     * @param string $roleName Name of the role
     * @return string
     */
    private function getAllowedRoutesFieldName($roleName) {
        return self::FIELD_ALLOWED_ROUTES . '[' . $roleName . ']';
    }

}