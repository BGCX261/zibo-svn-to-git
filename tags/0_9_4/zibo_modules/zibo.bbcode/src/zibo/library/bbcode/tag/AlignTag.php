<?php

namespace zibo\library\bbcode\tag;

/**
 * Implementation of the align tag
 */
class AlignTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'align';

    /**
     * Left align constant
     * @var string
     */
    const ALIGN_LEFT = 'left';

    /**
     * Center align constant
     * @var string
     */
    const ALIGN_CENTER = 'center';

    /**
     * Right align constant
     * @var string
     */
    const ALIGN_RIGHT = 'right';

    /**
     * Justify align constant
     * @var string
     */
    const ALIGN_JUSTIFY = 'justify';

    /**
     * Constructs the center tag
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
        $align = null;

        if (count($parameters) == 1 && array_key_exists(0, $parameters)) {
            $align = strtolower($parameters[0]);
        }

        if ($align != self::ALIGN_LEFT && $align != self::ALIGN_CENTER && $align != self::ALIGN_RIGHT && $align != self::ALIGN_JUSTIFY) {
            return false;
        }

        return '<div style="text-align: ' . $align . ';">' . $content . '</div>';
    }

}