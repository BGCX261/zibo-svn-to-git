<?php

namespace zibo\orm\security\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;

use zibo\orm\security\model\data\PermissionData;

/**
 * Decorator for a permission
 */
class PermissionDecorator implements Decorator {

    /**
     * Action where the description of a permission will point to
     * @var string
     */
    private $action;

    /**
     * Constructs a new permission decorator
     * @param string $action URL where the description of a permission will point to
     * @return null
     */
    public function __construct($action) {
        $this->action = $action;
    }

    /**
     * Decorates the permission in the provided cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingRows Array with the values of the remaining rows
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingRows) {
        $permission = $cell->getValue();
        if (!($permission instanceof PermissionData)) {
            $cell->setValue('');
            return;
        }

        $html = $permission->getPermissionDescription();
        if ($this->action) {
            $anchor = new Anchor($html, $this->action . $permission->id);
            $html = $anchor->getHtml();
        }

        $html .= '<div class="info">';
        $html .= $permission->getPermissionCode();
        $html .= '</div>';

        $cell->setValue($html);
    }

}