<?php

namespace zibo\install\model\requirement;

use zibo\core\Zibo;

/**
 * Requirement implementation to check if the GD extension is loaded
 */
class DirectoryModulesRequirement extends AbstractDirectoryRequirement {

    /**
     * Translation key for the name of this requirement
     * @var string
     */
    const TRANSLATION_NAME = 'install.requirement.modules';

    /**
     * Translation key for the message when the application directory does not exists
     * @var string
     */
    const TRANSLATION_MESSAGE_EXISTS = 'install.requirement.modules.exists';

    /**
     * Translation key for the message when the application directory is not writable
     * @var string
     */
    const TRANSLATION_MESSAGE_WRITABLE = 'install.requirement.modules.writable';

    /**
     * Constructs a new requirement
     * @return null
     */
    public function __construct() {
        parent::__construct(Zibo::DIRECTORY_MODULES, self::TRANSLATION_NAME, self::TRANSLATION_MESSAGE_EXISTS, self::TRANSLATION_MESSAGE_WRITABLE);
    }

}