<?php

namespace zibo\library\exchange\type;

/**
 * The BodyType element identifies how the body text is formatted in the response.
 */
class BodyType {

    /**
     * The response will return the richest available content of body text. This is useful if it is unknown whether the content is text or HTML.
     * The returned body will be text if the stored body is plain text. Otherwise, the response will return HTML if the stored body is in either HTML or RTF format.
     * This is the default value.
     * @var string
     */
    const TYPE_BEST = 'Best';

    /**
     * The response will return an item body as HTML.
     * @var string
     */
    const TYPE_HTML = 'HTML';

    /**
     * The response will return an item body as plain text.
     * @var string
     */
    const TYPE_TEXT = 'Text';

    /**
     * Checks if the provided type is a valid body type
     * @param string $type
     * @return boolean True if the type is valid, false otherwise
     */
    public static function isValidBodyType($type) {
        $types = array(
            self::TYPE_BEST,
            self::TYPE_HTML,
            self::TYPE_TEXT,
        );

        return in_array($type, $types);
    }

}