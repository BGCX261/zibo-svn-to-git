<?php

namespace zibo\install\model\requirement;

use zibo\library\i18n\translation\Translator;

/**
 * Abstract implementation of a requirement check
 */
abstract class AbstractRequirement implements Requirement {

    /**
     * Translation key for the name of this requirement
     * @var string
     */
    protected $name;

    /**
     * Translation key for the message when the requirement is not met
     * @var string
     */
    protected $message;

    /**
     * Flag to store the met status of this requirement
     * @var boolean
     */
    protected $isMet;

    /**
     * Constructs a new requirement
     * @param string $name The translation key for the name of this requirement
     * @param string $message The translation key for the message when this requirement is not met
     * @return null
     */
    public function __construct($name, $message) {
        $this->name = $name;
        $this->message = $message;

        $this->isMet = null;
    }

    /**
     * Gets the name of the check
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getName(Translator $translator) {
        return $translator->translate($this->name);
    }

    /**
     * Gets the result of the check, if the check is not performed yet, it will be done
     * @return boolean True if the requirement is met, false if it failed
     */
    public function isMet() {
        if ($this->isMet === null) {
            $this->performCheck();
        }

        return $this->isMet;
    }

    /**
     * Gets the fail message of the check
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getMessage(Translator $translator) {
        if ($this->isMet()) {
            return '';
        }

        return $translator->translate($this->message);
    }

}