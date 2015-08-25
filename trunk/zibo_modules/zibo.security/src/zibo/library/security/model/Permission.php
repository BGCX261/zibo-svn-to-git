<?php

namespace zibo\library\security\model;

/**
 * Permission of the SecurityModel
 */
interface Permission {

    /**
     * Gets the code of this permission
     * @return string
     */
    public function getPermissionCode();

    /**
     * Gets the description of this permission
     * @return string
     */
    public function getPermissionDescription();

}