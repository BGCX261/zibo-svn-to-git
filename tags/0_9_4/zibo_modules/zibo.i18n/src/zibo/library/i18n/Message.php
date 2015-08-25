<?php

namespace zibo\library\i18n;

use zibo\library\i18n\translation\Translator;
use zibo\library\message\Message as CoreMessage;

/**
 * Localized message
 */
class Message extends CoreMessage {

    /**
     * Construct a new localized message
     * @param string $message translation key of the message
     * @param string $type type of this message
     * @param array $vars variables for the translation
     * @param zibo\library\i18n\translation\Translator $translator
     * @return null
     */
    public function __construct($message, $type = null, array $vars = null, Translator $translator = null) {
        if ($translator === null) {
            $i18n = I18n::getInstance();
            $translator = $i18n->getTranslator();
        }
        $message = $translator->translate($message, $vars);
        parent::__construct($message, $type);
    }

}