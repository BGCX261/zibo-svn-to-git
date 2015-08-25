<?php

namespace zibo\orm\security\model\data;

use zibo\library\orm\model\data\Data;

/**
 * User preference data container
 */
class UserPreferenceData extends Data {

    /**
     * User of this preference
     * @var UserData
     */
    public $user;

    /**
     * Name of the preference
     * @var string
     */
    public $name;

    /**
     * Serialized value of the preference
     * @var string
     */
    public $value;

}