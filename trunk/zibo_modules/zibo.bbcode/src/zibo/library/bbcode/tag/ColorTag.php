<?php

namespace zibo\library\bbcode\tag;

use zibo\library\Number;

use zibo\ZiboException;

/**
 * Implementation of the color tag
 */
class ColorTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'color';

    /**
     * Constructs the color tag
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

    /**
     * Parses the tag
     * @param string $content Content of the tag
     * @param array $parameters Parameters of the tag
     * @return string HTML replacement for the tag
     */
    public function parseTag($content, array $parameters) {
        if (count($parameters) != 1 && !array_key_exists(0, $parameters)) {
            return false;
        }

        $color = $parameters[0];

        return '<span style="color: ' . $color . ';">' . $content . '</span>';
    }

}