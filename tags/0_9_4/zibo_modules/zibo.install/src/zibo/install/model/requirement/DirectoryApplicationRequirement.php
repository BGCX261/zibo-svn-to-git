<?php

namespace zibo\install\model\requirement;

use zibo\core\Zibo;

/**
 * Requirement implementation to check if the GD extension is loaded
 */
class DirectoryApplicationRequirement extends AbstractDirectoryRequirement {

    /**
     * Translation key for the name of this requirement
     * @var string
     */
    const TRANSLATION_NAME = 'install.requirement.application';

    /**
     * Translation key for the message when the application directory does not exists
     * @var string
     */
    const TRANSLATION_MESSAGE_EXISTS = 'install.requirement.application.exists';

    /**
     * Translation key for the message when the application directory is not writable
     * @var string
     */
    const TRANSLATION_MESSAGE_WRITABLE = 'install.requirement.application.writable';

    /**
     * Constructs a new requirement
     * @return null
     */
    public function __construct() {
        parent::__construct(Zibo::DIRECTORY_APPLICATION, self::TRANSLATION_NAME, self::TRANSLATION_MESSAGE_EXISTS, self::TRANSLATION_MESSAGE_WRITABLE);
    }

}