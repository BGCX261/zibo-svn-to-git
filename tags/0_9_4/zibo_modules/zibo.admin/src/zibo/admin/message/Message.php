<?php

namespace zibo\admin\message;

use zibo\library\i18n\Message as I18nMessage;

use zibo\ZiboException;

/**
 * Localized message for the admin interface
 */
class Message extends I18nMessage {

    /**
     * Type for a error message
     * @var string
     */
    const TYPE_ERROR = 'error';

    /**
     * Type for a information message
     * @var string
     */
    const TYPE_INFORMATION = 'information';

    /**
     * Type for a warning message
     * @var string
     */
    const TYPE_WARNING = 'warning';

    /**
     * Construct a new localized message for the admin interface
     * @param string $message translation key of the message
     * @param string $type type of the message
     * @param array $vars variables for the translation
     * @return null
     */
    public function __construct($message, $type, array $vars = null) {
        parent::__construct($message, $type, $vars);
    }

    /**
     * Set the type of this message
     * @param string $type
     * @return null
     * @ŧhrows zibo\ZiboException when an invalid type is provided
     */
    public function setType($type) {
        if ($type != self::TYPE_ERROR && $type != self::TYPE_INFORMATION && $type != self::TYPE_WARNING) {
            throw new ZiboException('Invalid type provided');
        }
        parent::setType($type);
    }

}