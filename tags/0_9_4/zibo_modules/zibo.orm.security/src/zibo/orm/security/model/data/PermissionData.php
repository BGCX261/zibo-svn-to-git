<?php

namespace zibo\orm\security\model\data;

use zibo\library\orm\model\data\Data;
use zibo\library\security\model\Permission;

/**
 * Permission data container
 */
class PermissionData extends Data implements Permission {

    /**
     * Code of this permission
     * @var string
     */
    public $code;

    /**
     * Description of this permission
     * @var string
     */
    public $description;

    /**
     * Gets the code of this permission
     * @return string
     */
    public function getPermissionCode() {
        return $this->code;
    }

    /**
     * Gets the description of this permission
     * @return string
     */
    public function getPermissionDescription() {
        if (empty($this->description)) {
            return $this->code;
        }
        return $this->description;
    }

}