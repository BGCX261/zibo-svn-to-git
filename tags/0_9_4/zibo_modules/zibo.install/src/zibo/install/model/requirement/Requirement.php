<?php

namespace zibo\install\model\requirement;

use zibo\library\i18n\translation\Translator;

/**
 * Interface to implement a requirement check
 */
interface Requirement {

    /**
     * Performs a system requirement check
     * @return null
     */
    public function performCheck();

    /**
     * Gets the name of the check
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getName(Translator $translator);

    /**
     * Gets the result of the check, if the check is not performed yet, it will be done
     * @return boolean True if the requirement is met, false if it failed
     */
    public function isMet();

    /**
     * Gets the fail message of the check
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getMessage(Translator $translator);

}