<?php

namespace zibo\admin\table\decorator\security;

use zibo\admin\form\field\PermissionCodeDecorator;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\security\model\Permission;
use zibo\library\security\model\Role;

/**
 * Decorator to display a checkbox for a permission
 */
class PermissionOptionDecorator implements Decorator {

    /**
     * Prefix for the field name of the permission checkbox
     * @var string
     */
    private $name;

    /**
     * Role to decorate
     * @var zibo\library\security\model\Role
     */
    private $role;

    /**
     * Instance of the field factory
     * @var zibo\library\html\form\field\FieldFactory
     */
    private $fieldFactory;

    /**
     * Constructs a new permission option decorator
     * @param zibo\library\security\model\Role $role Role to decorate the permissions for
     * @param string $name Prefix for the field name of the permission checkbox
     * @return null
     */
    public function __construct(Role $role, $name) {
        $this->role = $role;
        $this->name = $name;
        $this->fieldFactory = FieldFactory::getInstance();
    }

    /**
     * Decorates the cell with a checkbox for the permission in the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which contains the cell
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return boolean|null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $permission = $cell->getValue();
        if (!($permission instanceof Permission)) {
            $cell->setValue();
            return;
        }

        $field = $this->createField($permission);
        if ($this->role->isPermissionAllowed($permission->getPermissionCode())) {
            $field->setValue($permission);
        }

        $cell->setValue($field->getHtml());
    }

    /**
     * Creates a option field for the provided permission
     * @param zibo\library\security\model\Permission $permission
     * @return zibo\library\html\form\field\Field
     */
    public function createField(Permission $permission = null) {
        $name = $this->name . '[' . $this->role->getRoleName() . ']';

        $field = $this->fieldFactory->createField(FieldFactory::TYPE_OPTION, $name, $permission);
        $field->setKeyDecorator(new PermissionCodeDecorator());
        $field->setIsMultiple(true);

        return $field;
    }

}