<?php

namespace zibo\library\bbcode\tag;

/**
 * Implementation of the underline tag
 */
class UnderlineTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'u';

    /**
     * Constructs the underline tag
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
        return '<span style="text-decoration: underline;">' . $content . '</span>';
    }

}