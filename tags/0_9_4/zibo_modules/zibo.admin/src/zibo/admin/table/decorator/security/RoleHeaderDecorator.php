<?php

namespace zibo\admin\table\decorator\security;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\security\model\Role;

/**
 * Decorator to display a role as header
 */
class RoleHeaderDecorator implements Decorator {

    /**
     * Name of the role
     * @var string
     */
    private $roleName;

    /**
     * Constructs a new role header decorator
     * @param zibo\library\security\model\Role $role
     * @return null;
     */
    public function __construct(Role $role) {
        $this->roleName = $role->getRoleName();
    }

    /**
     * Decorates the cell with the name of the role
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $cell->setValue($this->roleName);
    }

}