<?php

namespace zibo\library\bbcode\tag;

/**
 * Implementation of the code tag
 */
class CodeTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'code';

    /**
     * Constructs the url tag
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME, true, false);
    }

    /**
     * Parses the tag
     * @param string $content Content of the tag
     * @param array $parameters Parameters of the tag
     * @return string HTML replacement for the tag
     */
    public function parseTag($content, array $parameters) {
        return '<div class="code">' . $content . '</div>';
    }

}