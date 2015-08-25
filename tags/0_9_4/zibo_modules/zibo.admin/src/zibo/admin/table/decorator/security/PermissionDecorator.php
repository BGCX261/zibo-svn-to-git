<?php

namespace zibo\admin\table\decorator\security;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\security\model\Permission;

/**
 * Decorator to display a the details of a permission
 */
class PermissionDecorator implements Decorator {

    /**
     * Decorates the cell with the details of the permission in the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which contains the cell
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array with the values of the remaining rows of the table
     * @return null|boolean
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $values) {
        $permission = $cell->getValue();

        if (!($permission instanceof Permission)) {
            $cell->setValue();
            return;
        }

        $html = $permission->getPermissionDescription() .
                '<div class="info">' . $permission->getPermissionCode() . '</div>';

        $cell->setValue($html);
    }

}