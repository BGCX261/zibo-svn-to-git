<?php

namespace zibo\install\model\profile;

use zibo\library\i18n\translation\Translator;

/**
 * Abstract implementation of a installation profile
 */
class MinimalProfile extends AbstractProfile {

    /**
     * Translation key for the name of this profile
     * @var string
     */
    const TRANSLATION_NAME = 'install.profile.minimal';

    /**
     * Translation key for the description of this profile
     * @var string
     */
    const TRANSLATION_DESCRIPTION = 'install.profile.minimal.description';

    /**
     * Constructs a new profile
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::TRANSLATION_DESCRIPTION);
    }

}