<?php

namespace zibo\install\model\requirement;

/**
 * Requirement implementation to check if a database extension is available
 */
class DatabaseRequirement extends AbstractRequirement {

    /**
     * Translation key for the name of this requirement
     * @var string
     */
    const TRANSLATION_NAME = 'install.requirement.database';

    /**
     * Translation key for the message when this requirement is not met
     * @var string
     */
    const TRANSLATION_MESSAGE = 'install.requirement.database.message';

    /**
     * Constructs a new requirement
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::TRANSLATION_MESSAGE);
    }

    /**
     * Checks if a database extension is loaded
     * @return null
     */
    public function performCheck() {
        $this->isMet = extension_loaded('mysql') || extension_loaded('sqlite');
    }

}