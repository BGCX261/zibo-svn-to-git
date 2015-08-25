<?php

namespace zibo\admin\form\field;

use zibo\library\html\form\field\decorator\Decorator;
use zibo\library\security\model\Permission;

/**
 * Decorator to display a permission's code
 */

class PermissionCodeDecorator implements Decorator {

    public function decorate($value) {
        if (!($value instanceof Permission)) {
            return;
        }

        return $value->getPermissionCode();
    }

}