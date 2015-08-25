<?php

namespace zibo\install\model\requirement;

/**
 * Requirement implementation to check if the GD extension is loaded
 * @author kayalion
 *
 */
class GdRequirement extends AbstractRequirement {

    /**
     * Translation key for the name of this requirement
     * @var string
     */
    const TRANSLATION_NAME = 'install.requirement.gd';

    /**
     * Translation key for the message when this requirement is not met
     * @var string
     */
    const TRANSLATION_MESSAGE = 'install.requirement.gd.message';

    /**
     * Constructs a new requirement
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::TRANSLATION_MESSAGE);
    }

    /**
     * Checks if the GD extension is loaded
     * @return null
     */
    public function performCheck() {
        $this->isMet = extension_loaded('gd');
    }

}